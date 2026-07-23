<?php

namespace App\Http\Controllers;

use App\Exports\AnimalMasterExport;
use App\Exports\BlankImportTemplate;
use App\Exports\ImportCompatibleAnimalExport;
use App\Exports\PartnerReportExport;
use App\Models\MasterPartner;
use App\Services\PartnerReportPdfService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportCenterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isMitra = $user->role === 'MITRA';

        if ($isMitra) {
            $partners = MasterPartner::where('id', $user->partner_id)->get();
        } else {
            $partners = MasterPartner::all();
        }

        return view('exports.index', compact('partners', 'isMitra'));
    }

    public function download(Request $request)
    {
        $user = $request->user();
        $isMitra = $user->role === 'MITRA';

        $product = $request->query('product', 'canonical'); // canonical, import_compatible, partner_report
        $partnerId = $request->query('partner_id');
        $format = strtolower($request->query('format', 'xlsx')); // xlsx, pdf

        // Tenant Isolation: MITRA can ONLY access their own partner_id
        if ($isMitra) {
            if (!$user->partner_id) {
                abort(403, 'User Mitra tidak terhubung dengan partner_id.');
            }
            $partnerId = (string) $user->partner_id;
            if ($product === 'canonical') {
                $product = 'import_compatible'; // Force scoped export for MITRA
            }
        }

        $timestamp = date('Ymd_His');

        if ($product === 'canonical' && !$isMitra) {
            $filename = "CANONICAL_FULL_EXPORT_{$timestamp}.xlsx";
            return Excel::download(new AnimalMasterExport(), $filename);
        }

        if ($product === 'template') {
            $filename = "BLANK_IMPORT_TEMPLATE_V2.0.0_{$timestamp}.xlsx";
            return Excel::download(new BlankImportTemplate(), $filename);
        }

        if ($product === 'import_compatible') {
            $partnerObj = $partnerId ? MasterPartner::find($partnerId) : null;
            $ownerTag = $partnerObj ? str_replace(' ', '_', $partnerObj->name) : 'ALL';
            $filename = "IMPORT_COMPATIBLE_{$ownerTag}_V2.0.0_{$timestamp}.xlsx";
            return Excel::download(new ImportCompatibleAnimalExport($partnerId), $filename);
        }

        if ($product === 'partner_report') {
            if (!$partnerId) {
                return back()->with('error', 'Silakan pilih Mitra untuk Laporan Mitra.');
            }
            $partnerObj = MasterPartner::findOrFail($partnerId);
            $ownerTag = str_replace(' ', '_', $partnerObj->name);

            if ($format === 'pdf') {
                $pdfService = new PartnerReportPdfService();
                $pdfContent = $pdfService->generatePdfContent($partnerId);
                $filename = "PARTNER_REPORT_{$ownerTag}_{$timestamp}.pdf";
                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
            } else {
                $filename = "PARTNER_REPORT_{$ownerTag}_{$timestamp}.xlsx";
                return Excel::download(new PartnerReportExport($partnerId), $filename);
            }
        }

        return back()->with('error', 'Produk export tidak valid.');
    }
}
