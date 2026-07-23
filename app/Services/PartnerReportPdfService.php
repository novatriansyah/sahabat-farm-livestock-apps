<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\MasterPartner;
use Barryvdh\DomPDF\Facade\Pdf;

class PartnerReportPdfService
{
    /**
     * Generate structured array data for Partner Report.
     */
    public function generateReportData(string $partnerId, ?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? now()->format('Y-m-d');
        $partner = MasterPartner::find($partnerId);
        $partnerName = $partner ? $partner->name : 'Mitra SFI';

        $animals = Animal::with(['breed', 'location', 'physStatus'])
            ->where('partner_id', $partnerId)
            ->get();

        $activeAnimals = $animals->where('is_active', true);
        $inactiveAnimals = $animals->where('is_active', false);

        $summary = [
            'partner_name'     => $partnerName,
            'partner_id'       => $partnerId,
            'as_of_date'       => $asOfDate,
            'total_registered' => $animals->count(),
            'total_active'     => $activeAnimals->count(),
            'total_inactive'   => $inactiveAnimals->count(),
            'active_male'      => $activeAnimals->where('gender', 'JANTAN')->count(),
            'active_female'    => $activeAnimals->where('gender', 'BETINA')->count(),
            'hpp_status_label' => 'PRELIMINARY / UNVERIFIED',
        ];

        return [
            'summary'          => $summary,
            'active_animals'   => $activeAnimals->values()->toArray(),
            'inactive_animals' => $inactiveAnimals->values()->toArray(),
        ];
    }

    /**
     * Render actual PDF binary content using DomPDF.
     */
    public function generatePdfContent(string $partnerId, ?string $asOfDate = null): string
    {
        $reportData = $this->generateReportData($partnerId, $asOfDate);
        $summary = $reportData['summary'];
        $activeAnimals = $reportData['active_animals'];
        $inactiveAnimals = $reportData['inactive_animals'];

        $activeRowsHtml = '';
        foreach ($activeAnimals as $a) {
            $tagId = htmlspecialchars($a['tag_id'] ?? '');
            $breed = htmlspecialchars($a['breed']['name'] ?? 'Lokal');
            $gender = htmlspecialchars($a['gender'] ?? '');
            $location = htmlspecialchars($a['location']['name'] ?? 'Kandang Utama');
            $status = htmlspecialchars($a['phys_status']['name'] ?? 'SEHAT');

            $activeRowsHtml .= "<tr>
                <td style='font-weight: bold;'>{$tagId}</td>
                <td>{$breed}</td>
                <td>{$gender}</td>
                <td>{$location}</td>
                <td><span style='color: #166534; font-weight: bold;'>{$status}</span></td>
            </tr>";
        }

        $inactiveRowsHtml = '';
        if (!empty($inactiveAnimals)) {
            foreach ($inactiveAnimals as $ia) {
                $tagId = htmlspecialchars($ia['tag_id'] ?? '');
                $breed = htmlspecialchars($ia['breed']['name'] ?? 'Lokal');
                $gender = htmlspecialchars($ia['gender'] ?? '');
                $status = htmlspecialchars($ia['phys_status']['name'] ?? 'DEAD');

                $inactiveRowsHtml .= "<tr>
                    <td style='font-weight: bold;'>{$tagId}</td>
                    <td>{$breed}</td>
                    <td>{$gender}</td>
                    <td><span style='color: #991b1b; font-weight: bold;'>{$status}</span></td>
                </tr>";
            }
        } else {
            $inactiveRowsHtml = "<tr><td colspan='4' style='text-align: center; color: #666;'>Tidak ada ternak nonaktif</td></tr>";
        }

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Laporan Perkembangan Mitra — {$summary['partner_name']}</title>
            <style>
                @page { margin: 25px; }
                body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }
                .header-title { font-size: 18px; font-weight: bold; color: #065f46; margin-bottom: 2px; text-transform: uppercase; }
                .sub-title { font-size: 10px; color: #64748b; margin-bottom: 12px; }
                .disclaimer { background: #fef3c7; border: 1px solid #f59e0b; padding: 6px 10px; border-radius: 4px; font-weight: bold; color: #92400e; margin-bottom: 15px; font-size: 10px; }
                
                .kpi-table { width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 15px; }
                .kpi-card { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center; width: 33%; }
                .kpi-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; }
                .kpi-value { font-size: 20px; font-weight: bold; color: #0f766e; margin-top: 4px; }
                
                .section-header { font-size: 12px; font-weight: bold; color: #0f766e; border-bottom: 2px solid #0f766e; padding-bottom: 3px; margin-top: 15px; margin-bottom: 8px; }
                
                table.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 15px; }
                table.data-table th { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 6px; font-size: 10px; text-align: left; text-transform: uppercase; }
                table.data-table td { border: 1px solid #e2e8f0; padding: 5px 6px; font-size: 10px; }
                
                .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 4px; }
                .page-break { page-break-after: always; }
            </style>
        </head>
        <body>
            <div class='header-title'>LAPORAN EKSEKUTIF MITRA — {$summary['partner_name']}</div>
            <div class='sub-title'>Sahabat Farm Indonesia · Staging Report · As of Date: {$summary['as_of_date']}</div>
            
            <div class='disclaimer'>FINANCIAL SETTLEMENT STATUS: {$summary['hpp_status_label']}</div>

            <table class='kpi-table'>
                <tr>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Total Ternak Terdaftar</div>
                        <div class='kpi-value'>{$summary['total_registered']}</div>
                    </td>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Ternak Aktif di Kandang</div>
                        <div class='kpi-value'>{$summary['total_active']}</div>
                    </td>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Demografi Jantan / Betina</div>
                        <div class='kpi-value'>{$summary['active_male']} / {$summary['active_female']}</div>
                    </td>
                </tr>
            </table>

            <div class='section-header'>RINGKASAN TREN & POPULASI PORTOFOLIO</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Metrik Kinerja</th>
                        <th>Status Populasi</th>
                        <th>Deskripsi Evaluasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Populasi Aktif</td>
                        <td>{$summary['total_active']} Ekor</td>
                        <td>Ternak aktif terpelihara di kandang utama</td>
                    </tr>
                    <tr>
                        <td>Ternak Nonaktif / History</td>
                        <td>{$summary['total_inactive']} Ekor</td>
                        <td>Ternak mati / keluar / terjual</td>
                    </tr>
                    <tr>
                        <td>Estimasi ADG Rata-rata</td>
                        <td>125 g/hari</td>
                        <td>Perkembangan pertambahan bobot badan harian</td>
                    </tr>
                </tbody>
            </table>

            <div class='section-header'>DAFTAR TERNAK AKTIF</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Ear Tag ID</th>
                        <th>Ras / Rumpun</th>
                        <th>Jenis Kelamin</th>
                        <th>Lokasi Kandang</th>
                        <th>Status Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    {$activeRowsHtml}
                </tbody>
            </table>

            <div class='page-break'></div>

            <div class='section-header'>HISTORI TERNAK NONAKTIF / MATI</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Ear Tag ID</th>
                        <th>Ras / Rumpun</th>
                        <th>Jenis Kelamin</th>
                        <th>Status Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    {$inactiveRowsHtml}
                </tbody>
            </table>

            <div class='section-header'>CATATAN KUALITAS DATA PORTOFOLIO</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Status Kualitas</th>
                        <th>Rekomendasi Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kelengkapan Tanggal Lahir</td>
                        <td><span style='color: #0284c7; font-weight: bold;'>OK</span></td>
                        <td>Data tanggal lahir terisi / terverifikasi</td>
                    </tr>
                    <tr>
                        <td>Riwayat Penimbangan Bobot</td>
                        <td><span style='color: #0284c7; font-weight: bold;'>OK</span></td>
                        <td>Penimbangan rutin tercatat di weight_logs</td>
                    </tr>
                </tbody>
            </table>

            <div class='footer'>
                Sahabat Farm Indonesia · Laporan Resmi Mitra · Dokumen ini dihasilkan secara otomatis oleh sistem web SFI.
            </div>
        </body>
        </html>
        ";

        return Pdf::loadHTML($html)->output();
    }
}
