<?php

namespace App\Http\Controllers;

use App\Exports\AnimalMasterExport;
use App\Exports\BlankImportTemplate;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function animals(Request $request)
    {
        $filters = $request->only(['partner_id', 'location_id', 'status', 'from', 'to']);
        return Excel::download(
            new AnimalMasterExport($filters),
            'SFI_Export_Ternak_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function template()
    {
        return Excel::download(
            new BlankImportTemplate(),
            'SFI_Template_Import_Kosong.xlsx'
        );
    }

    public function fullBackup()
    {
        $data = [
            'animals'            => \App\Models\Animal::with(['breed','partner','location','physStatus'])->get()->toArray(),
            'weight_logs'        => \App\Models\WeightLog::all()->toArray(),
            'treatment_logs'     => \App\Models\TreatmentLog::all()->toArray(),
            'breeding_events'    => \App\Models\BreedingEvent::all()->toArray(),
            'mating_colonies'    => \App\Models\MatingColony::with('members')->get()->toArray(),
            'ear_tag_logs'       => \App\Models\AnimalEarTagLog::all()->toArray(),
            'ownership_logs'     => \App\Models\AnimalOwnershipLog::all()->toArray(),
            'invoices'           => \App\Models\Invoice::with('items')->get()->toArray(),
            'inventory_items'    => \App\Models\InventoryItem::all()->toArray(),
            'inventory_purchases'=> \App\Models\InventoryPurchase::all()->toArray(),
            'inventory_usage'    => \App\Models\InventoryUsageLog::all()->toArray(),
            'hpp_manual_costs'   => \App\Models\HppManualCost::all()->toArray(),
            'farm_settings'      => \App\Models\FarmSetting::all()->toArray(),
            'exported_at'        => now()->toIso8601String(),
        ];
        $filename = 'SFI_FullBackup_' . now()->format('Y-m-d_His') . '.json';
        return response()->json($data, 200, [
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    public function reconcile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx',
        ]);

        try {
            $importedRows = Excel::toCollection(null, $request->file('file'));
            // Flatten sheets — take first sheet with data
            $firstSheet = $importedRows->first() ?? collect([]);
            // Skip header row
            $dataRows = $firstSheet->skip(1)->map(function ($row) {
                return [
                    'id'         => $row[0] ?? null,
                    'tag_id'     => $row[1] ?? null,
                    'birth_date' => $row[2] ?? null,
                    'gender'     => $row[3] ?? null,
                    'generation' => $row[4] ?? null,
                    'ear_tag_color' => $row[5] ?? null,
                    'necklace_color' => $row[6] ?? null,
                    'purchase_price' => $row[7] ?? null,
                    'sale_price'     => $row[8] ?? null,
                    'partner_id'     => $row[9] ?? null,
                    'current_location_id' => $row[10] ?? null,
                    'breed_id'           => $row[11] ?? null,
                    'google_drive_link'  => $row[12] ?? null,
                    'is_active'          => $row[13] ?? null,
                    'physical_status'    => $row[14] ?? null,
                ];
            })->filter(fn($r) => !empty($r['tag_id']));

            $service = new ReconciliationService();
            $result = $service->compare($dataRows);

            return view('admin.export.reconcile-diff', [
                'batchId'  => $result['batch_id'],
                'timestamp' => $result['timestamp'],
                'summary'  => $result['summary'],
                'results'  => collect($result['results']),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $service = new ReconciliationService();
        $batches = $service->getBatches();
        return view('admin.export.reconciliation-index', compact('batches'));
    }

    public function show(string $batchId)
    {
        $service = new ReconciliationService();
        $results = $service->getBatchDiff($batchId);

        if ($results->isEmpty()) {
            abort(404, 'Batch tidak ditemukan');
        }

        $summary = [
            'SAME'      => $results->where('status', 'SAME')->count(),
            'CONFLICT'  => $results->where('status', 'CONFLICT')->count(),
            'WEB_ONLY'  => $results->where('status', 'WEB_ONLY')->count(),
            'EXCEL_ONLY'=> $results->where('status', 'EXCEL_ONLY')->count(),
            'UNCERTAIN' => $results->where('status', 'UNCERTAIN')->count(),
        ];

        return view('admin.export.reconcile-diff', [
            'batchId'   => $batchId,
            'timestamp' => $results->first()->created_at,
            'summary'   => $summary,
            'results'   => $results,
        ]);
    }
}