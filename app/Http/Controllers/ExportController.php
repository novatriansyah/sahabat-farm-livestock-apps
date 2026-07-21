<?php

namespace App\Http\Controllers;

use App\Exports\AnimalMasterExport;
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
            new AnimalMasterExport([]),
            'SFI_Template_Kosong.xlsx'
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
        $request->validate(['file' => 'required|file|mimes:xlsx']);
        return redirect()->back()->with('info', 'Fitur rekonsiliasi akan tersedia di Task 1.3');
    }

    public function applyReconciliation(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,txt,csv']);
        $service = new \App\Services\ReconciliationService();
        $importedRows = collect([]); // placeholder
        $diffs = $service->compare($importedRows);
        return view('admin.export.reconcile-diff', compact('diffs'));
    }
}
