<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportController extends Controller
{
    public function export(string $reportType, string $format, Request $request)
    {
        $filters = $request->only(['period', 'from', 'to', 'partner_id', 'location_id', 'status', 'columns']);
        $reportClass = match($reportType) {
            'population' => \App\Exports\Reports\PopulationReport::class,
            'birth'      => \App\Exports\Reports\BirthReport::class,
            'growth'     => \App\Exports\Reports\GrowthReport::class,
            'kpi'        => \App\Exports\Reports\KpiReport::class,
            'hpp'        => \App\Exports\Reports\HppReport::class,
            'sales'      => \App\Exports\Reports\SalesReport::class,
            'profit'     => \App\Exports\Reports\ProfitShareReport::class,
            'inventory'  => \App\Exports\Reports\InventoryReport::class,
            'health'     => \App\Exports\Reports\HealthReport::class,
            'audit'      => \App\Exports\Reports\AuditReport::class,
            default      => throw new \InvalidArgumentException("Unknown report: $reportType"),
        };
        return match($format) {
            'pdf'   => $this->exportPdf($reportClass, $filters, $reportType),
            'excel' => Excel::download(new $reportClass($filters), "{$reportType}_" . now()->format('Y-m-d') . '.xlsx'),
            'csv'   => Excel::download(new $reportClass($filters), "{$reportType}_" . now()->format('Y-m-d') . '.csv', \Maatwebsite\Excel\Excel::CSV),
            'ppt'   => $this->exportPpt($reportClass, $filters, $reportType),
            'png'   => view('admin.export.png-preview', ['reportType' => $reportType, 'filters' => $filters]),
            default => throw new \InvalidArgumentException("Unknown format: $format"),
        };
    }

    private function exportPdf($reportClass, array $filters, string $reportType)
    {
        $report = new $reportClass($filters);
        $data = $report->query()->get();
        $pdf = Pdf::loadView('admin.export.report-pdf', compact('data', 'reportType', 'filters'));
        return $pdf->download("{$reportType}_" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportPpt($reportClass, array $filters, string $reportType)
    {
        $report = new $reportClass($filters);
        $data = $report->query()->get();
        $ppt = new \PhpOffice\PhpPresentation\PhpPresentation();
        $slide = $ppt->getActiveSlide();
        $shape = $slide->createRichTextShape();
        $shape->setHeight(600)->setWidth(800);
        $textRun = $shape->createTextRun("Laporan {$reportType} - " . now()->format('Y-m-d'));
        $textRun->getFont()->setSize(24)->setBold(true);
        $pptWriter = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
        ob_start();
        $pptWriter->save('php://output');
        $content = ob_get_clean();
        return response($content)->header('Content-Type', 'application/vnd.openxmlformats-officedocument.presentationml.presentation')
            ->header('Content-Disposition', "attachment; filename={$reportType}_" . now()->format('Y-m-d') . '.pptx');
    }
}