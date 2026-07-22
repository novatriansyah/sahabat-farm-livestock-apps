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

    public function dataSnapshotJson()
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
            $service = new ReconciliationService();
            $result = $service->compareFile($request->file('file')->getRealPath());

            return view('admin.export.reconcile-diff', [
                'batchId'   => $result['batch_id'],
                'timestamp' => $result['timestamp'],
                'summary'   => $result['summary'],
                'results'   => collect($result['results']),
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