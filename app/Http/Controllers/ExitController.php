<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ExitLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ExitController extends Controller
{
    public function create(Animal $animal): View
    {
        return view('animals.exit', compact('animal'));
    }

    public function store(Request $request, Animal $animal): RedirectResponse
    {
        if (!$animal->is_active) {
            return redirect()->route('animals.index')->with('error', 'Ternak sudah keluar.');
        }

        $validated = $request->validate([
            'exit_type' => 'required|in:JUAL,MATI',
            'exit_date' => 'required|date',
            'price' => 'nullable|numeric|min:0', // Required if SALE
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validated['exit_type'] === 'JUAL' && empty($validated['price'])) {
            return back()->withErrors(['price' => 'Price is required for sales.']);
        }

        DB::transaction(function () use ($request, $animal, $validated) {
            // Profit Formula: Sale Price - (Purchase Price + Accumulated Feed + Accumulated Medicine)
            // Note: We store "Final HPP" in ExitLog as the sum of Feed + Medicine for simplified reporting,
            // but the dashboard logic calculates Net Profit using Purchase Price too.
            // current_hpp = feed + medicine.

            $finalHpp = $animal->current_hpp;

            // Create Exit Log
            ExitLog::create([
                'animal_id' => $animal->id,
                'exit_date' => $validated['exit_date'],
                'exit_type' => $validated['exit_type'] === 'JUAL' ? 'JUAL' : 'MATI',
                'price' => $validated['price'] ?? 0,
                'final_hpp' => $finalHpp,
            ]);

            // Update Animal Status
            $animal->update([
                'is_active' => false,
                'health_status' => $validated['exit_type'] === 'JUAL' ? 'TERJUAL' : 'MATI',
            ]);

            // Create Draft Invoice if Sold
            if ($validated['exit_type'] === 'JUAL') {
                $prefix = 'INV-' . now()->format('Ymd');
                $lastInvoice = \App\Models\Invoice::where('invoice_number', 'like', "{$prefix}-%")
                    ->latest('id')
                    ->lockForUpdate()
                    ->first();
                
                $nextNumber = 1;
                if ($lastInvoice) {
                    $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
                    $nextNumber = $lastNumber + 1;
                }
                
                $invoiceNumber = $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                
                $invoice = \App\Models\Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'customer_name' => $validated['customer_name'] ?? 'Draft Customer',
                    'customer_contact' => $validated['customer_contact'],
                    'status' => 'DRAFT',
                    'type' => 'KOMERSIAL',
                    'issued_date' => $validated['exit_date'],
                    'due_date' => \Carbon\Carbon::parse($validated['exit_date'])->addDays(7),
                    'subtotal' => $validated['price'],
                    'total_amount' => $validated['price'],
                    'notes' => 'Otomatis dibuat dari penjualan hewan ' . $animal->tag_id,
                ]);

                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Penjualan Ternak: ' . $animal->tag_id . ' (' . $animal->full_breed . ')',
                    'quantity' => 1,
                    'unit_price' => $validated['price'],
                    'subtotal' => $validated['price'],
                    'related_animal_id' => $animal->id,
                ]);
            }

            // Sync Partnership End Date
            $animal->ownershipLogs()->whereNull('end_date')->update([
                'end_date' => $validated['exit_date']
            ]);
        });

        return redirect()->route('animals.index')->with('success', 'Kematian/Penjualan ternak berhasil dicatat.');
    }
}
