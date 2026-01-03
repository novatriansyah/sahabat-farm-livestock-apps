<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Fetch active animals that are available for sale (Healthy, Active)
        // Optionally filter out those already sold.
        $animals = Animal::where('is_active', true)
            ->where('health_status', '!=', 'DECEASED')
            ->get();

        return view('invoices.create', compact('animals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
            'issued_date' => 'required|date',
            'due_date' => 'nullable|after_or_equal:issued_date',
            'type' => 'required|in:PROFORMA,COMMERCIAL',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.related_animal_id' => 'nullable|exists:animals,id',
        ]);

        DB::beginTransaction();
        try {
            // Generate Invoice Number
            // Format: INV/YYYY/MM/XXXX
            $date = Carbon::parse($validated['issued_date']);
            $count = Invoice::whereYear('issued_date', $date->year)
                ->whereMonth('issued_date', $date->month)
                ->count();
            $number = 'INV/' . $date->format('Y/m') . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            if ($validated['type'] === 'PROFORMA') {
                $number = 'PRF/' . $date->format('Y/m') . '/' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            }

            // Calculate Totals
            $subtotal = 0;
            foreach ($validated['items'] as $itemData) {
                $subtotal += $itemData['quantity'] * $itemData['unit_price'];
            }
            $total = $subtotal; // Add Tax/Discount logic here if needed

            $invoice = Invoice::create([
                'invoice_number' => $number,
                'customer_name' => $validated['customer_name'],
                'customer_contact' => $validated['customer_contact'],
                'status' => $validated['type'] === 'PROFORMA' ? 'DRAFT' : 'ISSUED',
                'type' => $validated['type'],
                'issued_date' => $validated['issued_date'],
                'due_date' => $validated['due_date'] ?? null,
                'subtotal' => $subtotal,
                'total_amount' => $total,
            ]);

            foreach ($validated['items'] as $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'subtotal' => $itemSubtotal,
                    'related_animal_id' => $itemData['related_animal_id'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Invoice berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load('items.relatedAnimal');
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Convert Proforma to Commercial.
     */
    public function convert(Invoice $invoice): RedirectResponse
    {
        if ($invoice->type !== 'PROFORMA') {
            return back()->with('error', 'Hanya Proforma Invoice yang bisa dikonversi.');
        }

        DB::beginTransaction();
        try {
            // Option 1: Convert existing record
            // $invoice->update([
            //     'type' => 'COMMERCIAL',
            //     'status' => 'ISSUED',
            //     'invoice_number' => str_replace('PRF', 'INV', $invoice->invoice_number), // Simple logic
            // ]);
            
            // Option 2: Clone to new record (better for audit) - but Client Requirement says "once deal confirmed (converted)". 
            // "Conversion: Ability to generate a final Commercial ... converted from Proforma"
            // Let's modify the existing one to keep it simple and link one deal to one record.
            
            $newNumber = str_replace('PRF', 'INV', $invoice->invoice_number);
            
            $invoice->update([
                'type' => 'COMMERCIAL',
                'status' => 'ISSUED', // Ready for payment
                'invoice_number' => $newNumber,
            ]);

            DB::commit();
            return back()->with('success', 'Berhasil dikonversi ke Commercial Invoice.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal konversi: ' . $e->getMessage());
        }
    }

    /**
     * Mark as Paid.
     */
    public function markAsPaid(Invoice $invoice): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $invoice->update(['status' => 'PAID']);

            // Automate "Mark as Sold" for linked animals
            foreach ($invoice->items as $item) {
                if ($item->related_animal_id) {
                    $animal = Animal::find($item->related_animal_id);
                    if ($animal && $animal->is_active) {
                        // Create Exit Log
                        \App\Models\ExitLog::create([
                            'animal_id' => $animal->id,
                            'exit_type' => 'SALE',
                            'exit_date' => $invoice->issued_date, // or now() ? Using Invoice Date for consistency
                            'price' => $item->subtotal,
                            'final_hpp' => $animal->current_hpp ?? 0,
                            'notes' => 'Auto-generated via Invoice ' . $invoice->invoice_number,
                        ]);

                        // Update Animal Status
                        // Assuming 'SOLD' status exists in MasterPhysStatus or similar logic. 
                        // If strict FK, we need ID. But often 'phys_status' is nullable or handled by scope.
                        // Let's just set is_active = false for now to "Exit" it from inventory.
                        
                        // Check if "SOLD" status exists in database to be precise?
                        // For MVP, just is_active = false is sufficient to hide from "Active" lists.
                        $animal->update([
                            'is_active' => false,
                            'health_status' => 'SOLD', // Semantic update if column allows string enum/check
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Invoice LUNAS. Hewan terkait otomatis ditandai TERJUAL.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }
}
