<?php

namespace App\Http\Controllers;

use App\Exports\AnimalMasterExport;
use App\Exports\BlankImportTemplate;
use App\Exports\ImportCompatibleAnimalExport;
use App\Exports\PartnerReportExport;
use App\Services\PartnerReportPdfService;
use App\Services\ReconciliationService;
use App\Traits\PartnerScopeTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use PartnerScopeTrait;

    /**
     * Product A: Canonical Full Export.
     * Filterless, full database snapshot for PEMILIK / Super Admin only.
     */
    public function animals(Request $request)
    {
        return Excel::download(
            new AnimalMasterExport(),
            'SFI_Canonical_Full_Export_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Product B: Import-Compatible Animal Export.
     * Mode ALL or PARTNER (dropdown selection / forced partner scope).
     */
    public function importCompatible(Request $request)
    {
        $partnerId = $this->resolvePartnerScope($request);
        $suffix = $partnerId ? "Partner_{$partnerId}" : 'ALL';

        return Excel::download(
            new ImportCompatibleAnimalExport($partnerId),
            "SFI_Import_Compatible_Export_{$suffix}_" . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Product C: Partner Report XLSX Export.
     */
    public function partnerReportXlsx(Request $request)
    {
        $partnerId = $this->resolvePartnerScope($request);
        if (!$partnerId) {
            return redirect()->back()->withErrors(['partner_id' => 'Mitra wajib dipilih untuk laporan mitra.']);
        }

        return Excel::download(
            new PartnerReportExport($partnerId),
            "SFI_Laporan_Mitra_{$partnerId}_" . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Product C: Partner Report PDF Summary.
     */
    public function partnerReportPdf(Request $request, PartnerReportPdfService $pdfService)
    {
        $partnerId = $this->resolvePartnerScope($request);
        if (!$partnerId) {
            return redirect()->back()->withErrors(['partner_id' => 'Mitra wajib dipilih untuk laporan mitra.']);
        }

        $pdfContent = $pdfService->generatePdfContent($partnerId);
        $partner = \App\Models\MasterPartner::find($partnerId);
        $partnerName = $partner ? str_replace(' ', '_', $partner->name) : "Partner_{$partnerId}";

        return response()->streamDownload(
            fn() => print($pdfContent),
            "PARTNER_REPORT_{$partnerName}_" . now()->format('Y-m-d') . ".pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Blank Import Template Download.
     */
    public function template()
    {
        return Excel::download(
            new BlankImportTemplate(),
            'SFI_Template_Import_Kosong.xlsx'
        );
    }

    /**
     * Data Snapshot JSON Export.
     */
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

    /**
     * Reconciliation File Compare Endpoint.
     */
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

    /**
     * Reconciliation Batch List View.
     */
    public function index()
    {
        $service = new ReconciliationService();
        $batches = $service->getBatches();
        return view('admin.export.reconciliation-index', compact('batches'));
    }

    /**
     * Reconciliation Batch Detail View.
     */
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