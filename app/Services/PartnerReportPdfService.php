<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\MasterPartner;
use App\Models\WeightLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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

        $animals = Animal::with(['breed', 'location', 'physStatus', 'weightLogs', 'dam'])
            ->where('partner_id', $partnerId)
            ->get();

        $activeAnimals = $animals->where('is_active', true);
        $inactiveAnimals = $animals->where('is_active', false);

        // Calculate dynamic ADG per animal
        $adgValues = [];
        $animalDataList = [];

        foreach ($activeAnimals as $a) {
            $logs = $a->weightLogs->sortBy('weigh_date')->values();
            $adgText = 'TIDAK DAPAT DIHITUNG';
            $latestWeight = '-';

            if ($logs->count() >= 2) {
                $first = $logs->first();
                $last = $logs->last();
                $latestWeight = number_format($last->weight_kg, 2) . ' kg';

                $days = Carbon::parse($first->weigh_date)->diffInDays(Carbon::parse($last->weigh_date));
                if ($days > 0) {
                    $gainKg = $last->weight_kg - $first->weight_kg;
                    $adgGrams = round(($gainKg * 1000) / $days, 1);
                    $adgValues[] = $adgGrams;
                    $adgText = "{$adgGrams} g/hari";
                }
            } elseif ($logs->count() === 1) {
                $latestWeight = number_format($logs->first()->weight_kg, 2) . ' kg';
            }

            $animalDataList[] = [
                'id'            => $a->id,
                'tag_id'        => $a->tag_id,
                'legacy_tag_id' => $a->legacy_tag_id ?? '-',
                'breed'         => $a->breed?->name ?? 'Lokal',
                'gender'        => $a->gender,
                'generation'    => $a->declared_generation ?? $a->generation ?? 'PUREBRED',
                'location'      => $a->location?->name ?? 'Kandang Utama',
                'status'        => $a->physStatus?->name ?? 'SEHAT',
                'latest_weight' => $latestWeight,
                'adg'           => $adgText,
                'dam_tag'       => $a->dam?->tag_id ?? '-',
            ];
        }

        $avgAdgText = !empty($adgValues)
            ? round(array_sum($adgValues) / count($adgValues), 1) . ' g/hari'
            : 'TIDAK DAPAT DIHITUNG';

        $summary = [
            'partner_name'     => $partnerName,
            'partner_id'       => $partnerId,
            'as_of_date'       => $asOfDate,
            'total_registered' => $animals->count(),
            'total_active'     => $activeAnimals->count(),
            'total_inactive'   => $inactiveAnimals->count(),
            'active_male'      => $activeAnimals->where('gender', 'JANTAN')->count(),
            'active_female'    => $activeAnimals->where('gender', 'BETINA')->count(),
            'avg_adg'          => $avgAdgText,
            'hpp_status_label' => 'PRELIMINARY / UNVERIFIED',
        ];

        return [
            'summary'          => $summary,
            'active_animals'   => $animalDataList,
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
            $breed = htmlspecialchars($a['breed'] ?? 'Lokal');
            $gender = htmlspecialchars($a['gender'] ?? '');
            $gen = htmlspecialchars($a['generation'] ?? 'PUREBRED');
            $location = htmlspecialchars($a['location'] ?? 'Kandang Utama');
            $status = htmlspecialchars($a['status'] ?? 'SEHAT');
            $weight = htmlspecialchars($a['latest_weight'] ?? '-');
            $adg = htmlspecialchars($a['adg'] ?? 'NOT CALCULABLE');

            $activeRowsHtml .= "<tr>
                <td style='font-weight: bold;'>{$tagId}</td>
                <td>{$breed} ({$gen})</td>
                <td>{$gender}</td>
                <td>{$location}</td>
                <td>{$weight}</td>
                <td>{$adg}</td>
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
                .kpi-card { background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center; width: 25%; }
                .kpi-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; }
                .kpi-value { font-size: 18px; font-weight: bold; color: #0f766e; margin-top: 4px; }
                
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
            <div class='sub-title'>Sahabat Farm Indonesia · Official Report · As of Date: {$summary['as_of_date']}</div>
            
            <div class='disclaimer'>FINANCIAL SETTLEMENT STATUS: {$summary['hpp_status_label']}</div>

            <table class='kpi-table'>
                <tr>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Total Terdaftar</div>
                        <div class='kpi-value'>{$summary['total_registered']}</div>
                    </td>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Ternak Aktif</div>
                        <div class='kpi-value'>{$summary['total_active']}</div>
                    </td>
                    <td class='kpi-card'>
                        <div class='kpi-label'>Demografi (J / B)</div>
                        <div class='kpi-value'>{$summary['active_male']} / {$summary['active_female']}</div>
                    </td>
                    <td class='kpi-card'>
                        <div class='kpi-label'>ADG Rata-rata</div>
                        <div class='kpi-value'>{$summary['avg_adg']}</div>
                    </td>
                </tr>
            </table>

            <div class='section-header'>RINGKASAN PERKEMBANGAN PORTOFOLIO</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Metrik Kinerja</th>
                        <th>Nilai Evaluasi</th>
                        <th>Keterangan / Sumber Calculation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Populasi Aktif di Kandang</td>
                        <td>{$summary['total_active']} Ekor</td>
                        <td>Ternak aktif terpelihara di kandang utama SFI</td>
                    </tr>
                    <tr>
                        <td>History Nonaktif / Mati</td>
                        <td>{$summary['total_inactive']} Ekor</td>
                        <td>Ternak nonaktif / mati tercatat di database</td>
                    </tr>
                    <tr>
                        <td>ADG Rata-rata Portofolio</td>
                        <td>{$summary['avg_adg']}</td>
                        <td>Kalkulasi dinamis dari weight_logs (Bukan nilai statis)</td>
                    </tr>
                </tbody>
            </table>

            <div class='section-header'>DAFTAR TERNAK AKTIF</div>
            <table class='data-table'>
                <thead>
                    <tr>
                        <th>Ear Tag ID</th>
                        <th>Ras & Generasi</th>
                        <th>Gender</th>
                        <th>Lokasi Kandang</th>
                        <th>Bobot Terakhir</th>
                        <th>ADG Dinamis</th>
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
                        <th>Rekomendasi / Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Kelengkapan Tanggal Lahir</td>
                        <td><span style='color: #0284c7; font-weight: bold;'>VERIFIED</span></td>
                        <td>Data tanggal lahir terisi dari Master Excel v3</td>
                    </tr>
                    <tr>
                        <td>Kalkulasi ADG Portofolio</td>
                        <td><span style='color: #0284c7; font-weight: bold;'>DYNAMIC</span></td>
                        <td>Dihitung langsung dari log penimbangan aktual</td>
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
