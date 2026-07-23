<?php

namespace App\Exports;

use App\Exports\Concerns\BuildsPartnerCharts;
use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\MasterPartner;
use App\Models\TreatmentLog;
use App\Models\WeightLog;
use App\Services\PartnerMetricsService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PartnerReportExport implements WithMultipleSheets, WithEvents, WithCharts
{
    use BuildsPartnerCharts;

    private PartnerMetricsService $metrics;

    public function __construct(
        private string $partnerId,
        private ?string $asOfDate = null,
        ?PartnerMetricsService $metrics = null
    ) {
        $this->asOfDate = $asOfDate ?? now()->format('Y-m-d');
        $this->metrics  = $metrics ?? new PartnerMetricsService();
    }

    public function sheets(): array
    {
        $partner = MasterPartner::find($this->partnerId);
        $partnerName = $partner ? $partner->name : 'Mitra';

        return [
            'RINGKASAN_MITRA'       => new RingkasanMitraSheet($partnerName, $this->partnerId, $this->asOfDate, $this->metrics),
            'DASHBOARD_MITRA'       => new DashboardMitraSheet($this->partnerId, $this->asOfDate, $this->metrics),
            'DAFTAR_TERNAK_AKTIF'   => new DaftarTernakAktifSheet($this->partnerId),
            'HISTORI_TERNAK_KELUAR' => new HistoriTernakKeluarSheet($this->partnerId),
            'KELAHIRAN_REPRODUKSI'  => new KelahiranReproduksiSheet($this->partnerId),
            'BOBOT_ADG'             => new BobotAdgSheet($this->partnerId, $this->metrics),
            'KESEHATAN_TREATMENT'   => new KesehatanTreatmentSheet($this->partnerId, $this->metrics),
            'DATA_QUALITY'          => new DataQualitySheet($this->partnerId),
            'METADATA_FILTER'       => new MetadataFilterSheet($partnerName, $this->partnerId, $this->asOfDate),
        ];
    }

    public function charts(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                if ($event->sheet->getTitle() === 'DASHBOARD_MITRA') {
                    $trendData = $this->metrics->monthlyTrendData($this->partnerId);
                    $this->attachAllPartnerCharts($event->sheet->getDelegate(), $trendData);
                }
            },
        ];
    }
}

class RingkasanMitraSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerName,
        private string $partnerId,
        private string $asOfDate,
        private PartnerMetricsService $metrics
    ) {}

    public function title(): string
    {
        return 'RINGKASAN_MITRA';
    }

    public function headings(): array
    {
        return ['METRIK_RINGKASAN', 'NILAI'];
    }

    public function collection()
    {
        $animals = Animal::where('partner_id', $this->partnerId)->get();
        $active = $animals->where('is_active', true);
        $inactive = $animals->where('is_active', false);

        $adgVal = $this->metrics->averageAdgGramPerDay($this->partnerId);
        $treatmentCostVal = $this->metrics->totalTreatmentCost($this->partnerId);

        return collect([
            ['Nama Mitra', $this->partnerName],
            ['ID Mitra', $this->partnerId],
            ['Data As Of', $this->asOfDate],
            ['Total Ternak Terdaftar', $animals->count()],
            ['Total Ternak Aktif', $active->count()],
            ['Total Ternak Nonaktif / Keluar', $inactive->count()],
            ['Jantan Aktif', $active->where('gender', 'JANTAN')->count()],
            ['Betina Aktif', $active->where('gender', 'BETINA')->count()],
            ['Average ADG Populasi', $this->metrics->display($adgVal, 'g/hari')],
            ['Total Biaya Pengobatan', $this->metrics->display($treatmentCostVal, 'Rp')],
            ['Status Settlement HPP', 'PRELIMINARY / UNVERIFIED'],
        ]);
    }
}

class DashboardMitraSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize, WithCharts, WithEvents
{
    use BuildsPartnerCharts;

    public function __construct(
        private string $partnerId,
        private string $asOfDate,
        private PartnerMetricsService $metrics
    ) {}

    public function title(): string
    {
        return 'DASHBOARD_MITRA';
    }

    public function headings(): array
    {
        return ['KPI_DASHBOARD', 'HITUNGAN_POPULASI', 'TREN_DAN_DESKRIPSI'];
    }

    public function charts()
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $trendData = $this->metrics->monthlyTrendData($this->partnerId);
                $this->buildPartnerCharts($event->sheet->getDelegate(), $trendData);
            },
        ];
    }

    public function collection()
    {
        $animals = Animal::where('partner_id', $this->partnerId)->get();
        $active = $animals->where('is_active', true);
        $inactive = $animals->where('is_active', false);

        $adgVal = $this->metrics->averageAdgGramPerDay($this->partnerId);
        $treatmentCostVal = $this->metrics->totalTreatmentCost($this->partnerId);

        return collect([
            ['Aktif Populasi', $active->count(), 'Ternak aktif di kandang saat ini'],
            ['Nonaktif / History', $inactive->count(), 'Ternak mati/keluar/terjual'],
            ['Populasi Jantan', $active->where('gender', 'JANTAN')->count(), 'Komposisi pejantan'],
            ['Populasi Betina', $active->where('gender', 'BETINA')->count(), 'Komposisi indukan'],
            ['Tingkat Kelahiran', $animals->whereNotNull('birth_date')->count(), 'Total ternak lahir terdaftar'],
            ['Average ADG', $this->metrics->display($adgVal, 'g/hari'), 'ADG populasi dari data penimbangan aktual'],
            ['Biaya Treatment', $this->metrics->display($treatmentCostVal, 'Rp'), 'Total rekap biaya pengobatan aktual'],
        ]);
    }
}

class DaftarTernakAktifSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'DAFTAR_TERNAK_AKTIF'; }

    public function headings(): array
    {
        return ['tag_id', 'jenis_kelamin', 'ras_rumpun', 'generasi', 'kandang_lokasi', 'status_fisik', 'link_gdrive'];
    }

    public function collection()
    {
        return Animal::with(['breed', 'location', 'physStatus'])
            ->where('partner_id', $this->partnerId)
            ->where('is_active', true)
            ->get()
            ->map(function (Animal $a) {
                return [
                    'tag_id'        => (string) $a->tag_id,
                    'jenis_kelamin' => (string) $a->gender,
                    'ras_rumpun'    => (string) ($a->breed?->name ?? 'Garut'),
                    'generasi'      => (string) ($a->declared_generation ?? $a->generation ?? 'PUREBRED'),
                    'kandang_lokasi'=> (string) ($a->location?->name ?? 'Kandang Utama'),
                    'status_fisik'  => (string) ($a->physStatus?->name ?? 'SEHAT'),
                    'link_gdrive'   => (string) ($a->google_drive_link ?? ''),
                ];
            });
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }
}

class HistoriTernakKeluarSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'HISTORI_TERNAK_KELUAR'; }

    public function headings(): array
    {
        return ['tag_id', 'jenis_kelamin', 'ras_rumpun', 'tanggal_keluar', 'status_terakhir', 'keterangan'];
    }

    public function collection()
    {
        return Animal::with(['breed', 'physStatus'])
            ->where('partner_id', $this->partnerId)
            ->where('is_active', false)
            ->get()
            ->map(function (Animal $a) {
                return [
                    'tag_id'         => (string) $a->tag_id,
                    'jenis_kelamin'  => (string) $a->gender,
                    'ras_rumpun'     => (string) ($a->breed?->name ?? 'Garut'),
                    'tanggal_keluar' => $a->updated_at ? date('Y-m-d', strtotime($a->updated_at)) : '',
                    'status_terakhir'=> (string) ($a->physStatus?->name ?? 'DEAD'),
                    'keterangan'     => (string) ($a->notes ?? 'Ternak nonaktif/keluar'),
                ];
            });
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT];
    }
}

class KelahiranReproduksiSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'KELAHIRAN_REPRODUKSI'; }

    public function headings(): array
    {
        return ['tag_indukan', 'tag_anakan', 'tanggal_lahir', 'jenis_kelamin', 'bobot_lahir', 'jumlah_kembar', 'generasi', 'status'];
    }

    public function collection()
    {
        $animals = Animal::with(['dam', 'physStatus'])
            ->where('partner_id', $this->partnerId)
            ->get();

        if ($animals->isEmpty()) {
            return collect([]);
        }

        return $animals->map(function (Animal $a) {
            return [
                'tag_indukan'   => (string) ($a->dam?->tag_id ?? '-'),
                'tag_anakan'    => (string) $a->tag_id,
                'tanggal_lahir' => $a->birth_date ? date('Y-m-d', strtotime($a->birth_date)) : '',
                'jenis_kelamin' => (string) $a->gender,
                'bobot_lahir'   => $a->birth_weight !== null ? (float) $a->birth_weight : null,
                'jumlah_kembar' => $a->litter_size !== null ? (int) $a->litter_size : null,
                'generasi'      => (string) ($a->declared_generation ?? $a->generation ?? 'PUREBRED'),
                'status'        => (string) ($a->physStatus?->name ?? ($a->is_active ? 'SEHAT' : 'MATI')),
            ];
        });
    }
}

class BobotAdgSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerId,
        private PartnerMetricsService $metrics
    ) {}

    public function title(): string { return 'BOBOT_ADG'; }

    public function headings(): array
    {
        return ['tag_id', 'tanggal_timbang', 'bobot_kg', 'calculated_adg_g_day', 'catatan'];
    }

    public function collection()
    {
        $animalIds = Animal::where('partner_id', $this->partnerId)->pluck('id');

        return WeightLog::with('animal')
            ->whereIn('animal_id', $animalIds)
            ->orderBy('weigh_date', 'desc')
            ->get()
            ->map(function (WeightLog $w) {
                // Calculate animal-specific ADG dynamically
                $adgVal = null;
                if ($w->animal) {
                    $logs = $w->animal->weightLogs->sortBy('weigh_date')->values();
                    if ($logs->count() >= 2) {
                        $first = $logs->first();
                        $last  = $logs->last();
                        $days  = \Carbon\Carbon::parse($first->weigh_date)->diffInDays(\Carbon\Carbon::parse($last->weigh_date));
                        if ($days > 0 && $last->weight_kg >= $first->weight_kg) {
                            $adgVal = round((($last->weight_kg - $first->weight_kg) / $days) * 1000, 1);
                        }
                    }
                }

                return [
                    'tag_id'                => (string) ($w->animal?->tag_id ?? '-'),
                    'tanggal_timbang'       => $w->weigh_date ? date('Y-m-d', strtotime($w->weigh_date)) : '',
                    'bobot_kg'              => (float) $w->weight_kg,
                    'calculated_adg_g_day'  => $adgVal !== null ? (float) $adgVal : 'TIDAK DAPAT DIHITUNG',
                    'catatan'               => (string) ($w->notes ?? 'Penimbangan rutin'),
                ];
            });
    }
}

class KesehatanTreatmentSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerId,
        private PartnerMetricsService $metrics
    ) {}

    public function title(): string { return 'KESEHATAN_TREATMENT'; }

    public function headings(): array
    {
        return ['tag_id', 'tanggal_treatment', 'diagnosa', 'tindakan_obat', 'biaya'];
    }

    public function collection()
    {
        $animalIds = Animal::where('partner_id', $this->partnerId)->pluck('id');

        return TreatmentLog::with('animal')
            ->whereIn('animal_id', $animalIds)
            ->get()
            ->map(function (TreatmentLog $t) {
                return [
                    'tag_id'            => (string) ($t->animal?->tag_id ?? '-'),
                    'tanggal_treatment' => $t->treatment_date ? date('Y-m-d', strtotime($t->treatment_date)) : '',
                    'diagnosa'          => (string) ($t->type ?? 'Pemeriksaan Rutin'),
                    'tindakan_obat'     => (string) ($t->notes ?? '-'),
                    'biaya'             => $t->cost !== null ? (float) $t->cost : 0.0,
                ];
            });
    }
}

class DataQualitySheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'DATA_QUALITY'; }

    public function headings(): array
    {
        return ['tag_id', 'isu_kualitas_data', 'tingkat_keparahan', 'rekomendasi_tindakan'];
    }

    public function collection()
    {
        $animals = Animal::where('partner_id', $this->partnerId)->get();

        if ($animals->isEmpty()) {
            return collect([
                [
                    'tag_id'               => '-',
                    'isu_kualitas_data'    => 'NO DATA / NOT ASSESSED',
                    'tingkat_keparahan'    => 'INFO',
                    'rekomendasi_tindakan' => 'Portfolio mitra tidak memiliki data ternak',
                ]
            ]);
        }

        $issues = collect();
        foreach ($animals as $a) {
            if (empty($a->birth_date)) {
                $issues->push(['tag_id' => (string) $a->tag_id, 'isu_kualitas_data' => 'Tanggal lahir kosong', 'tingkat_keparahan' => 'MEDIUM', 'rekomendasi_tindakan' => 'Isi tanggal lahir atau beri tanda estimasi']);
            }
        }

        if ($issues->isEmpty()) {
            $issues->push(['tag_id' => '-', 'isu_kualitas_data' => 'Tidak ditemukan isu kualitas data', 'tingkat_keparahan' => 'OK', 'rekomendasi_tindakan' => 'Data lengkap']);
        }

        return $issues;
    }
}

class MetadataFilterSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerName,
        private string $partnerId,
        private string $asOfDate
    ) {}

    public function title(): string { return 'METADATA_FILTER'; }

    public function headings(): array { return ['PARAMETER', 'NILAI']; }

    public function collection()
    {
        return collect([
            ['Nama Mitra', $this->partnerName],
            ['ID Mitra', $this->partnerId],
            ['Tanggal Cetak Laporan', date('c')],
            ['Data As Of', $this->asOfDate],
            ['Versi Skema Laporan', '2.0.0'],
            ['Status Settlement HPP', 'PRELIMINARY / UNVERIFIED'],
        ]);
    }
}
