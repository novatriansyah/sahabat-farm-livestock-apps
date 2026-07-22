<?php

namespace App\Exports;

use App\Models\Animal;
use App\Models\BreedingEvent;
use App\Models\MasterPartner;
use App\Models\TreatmentLog;
use App\Models\WeightLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PartnerReportExport implements WithMultipleSheets
{
    public function __construct(
        private string $partnerId,
        private ?string $asOfDate = null
    ) {
        $this->asOfDate = $asOfDate ?? now()->format('Y-m-d');
    }

    public function sheets(): array
    {
        $partner = MasterPartner::find($this->partnerId);
        $partnerName = $partner ? $partner->name : 'Mitra';

        return [
            'RINGKASAN_MITRA'       => new RingkasanMitraSheet($partnerName, $this->partnerId, $this->asOfDate),
            'DASHBOARD_MITRA'       => new DashboardMitraSheet($this->partnerId, $this->asOfDate),
            'DAFTAR_TERNAK_AKTIF'   => new DaftarTernakAktifSheet($this->partnerId),
            'HISTORI_TERNAK_KELUAR' => new HistoriTernakKeluarSheet($this->partnerId),
            'KELAHIRAN_REPRODUKSI'  => new KelahiranReproduksiSheet($this->partnerId),
            'BOBOT_ADG'             => new BobotAdgSheet($this->partnerId),
            'KESEHATAN_TREATMENT'   => new KesehatanTreatmentSheet($this->partnerId),
            'DATA_QUALITY'          => new DataQualitySheet($this->partnerId),
            'METADATA_FILTER'       => new MetadataFilterSheet($partnerName, $this->partnerId, $this->asOfDate),
        ];
    }
}

class RingkasanMitraSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerName,
        private string $partnerId,
        private string $asOfDate
    ) {}

    public function title(): string
    {
        return 'RINGKASAN_MITRA';
    }

    public function headings(): array
    {
        return ['METRIK RINGKASAN', 'NILAI / JUMLAH', 'CATATAN'];
    }

    public function collection()
    {
        $animals = Animal::where('partner_id', $this->partnerId)->get();
        $active = $animals->where('is_active', true);
        $inactive = $animals->where('is_active', false);

        return collect([
            ['Identitas Mitra', $this->partnerName, "ID: {$this->partnerId}"],
            ['Tanggal Laporan (As Of)', $this->asOfDate, 'Data per tanggal laporan'],
            ['Total Ternak Terdaftar', $animals->count(), 'Seluruh histori ternak'],
            ['Jumlah Ternak Aktif', $active->count(), 'Ternak aktif dalam populasi'],
            ['Jumlah Ternak Nonaktif / Keluar', $inactive->count(), 'Ternak mati, dijual, atau berpindah'],
            ['Jumlah Jantan Aktif', $active->where('gender', 'JANTAN')->count(), 'Jantan aktif'],
            ['Jumlah Betina Aktif', $active->where('gender', 'BETINA')->count(), 'Betina aktif'],
            ['Status HPP / Bagi Hasil', 'PRELIMINARY / UNVERIFIED', 'Belum final settlement (Release 4 HPP)'],
        ]);
    }
}

class DashboardMitraSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(
        private string $partnerId,
        private string $asOfDate
    ) {}

    public function title(): string
    {
        return 'DASHBOARD_MITRA';
    }

    public function headings(): array
    {
        return ['KATEGORI KPI', 'METRIK', 'JUMLAH / NILAI'];
    }

    public function collection()
    {
        $animals = Animal::where('partner_id', $this->partnerId)->get();
        $active = $animals->where('is_active', true);

        return collect([
            ['POPULASI', 'Aktif', $active->count()],
            ['POPULASI', 'Nonaktif (Keluar/Mati/Jual)', $animals->where('is_active', false)->count()],
            ['DEMOGRAFI', 'Jantan', $active->where('gender', 'JANTAN')->count()],
            ['DEMOGRAFI', 'Betina', $active->where('gender', 'BETINA')->count()],
            ['KESEHATAN', 'Sehat', $active->where('physical_status', 'SEHAT')->count()],
            ['KESEHATAN', 'Perlu Perhatian (Sakit/Karantina)', $active->whereIn('physical_status', ['SAKIT', 'KARANTINA'])->count()],
            ['FINANSIAL', 'Nilai Investasi Awal (Estimasi)', $active->sum('acquisition_cost')],
            ['FINANSIAL', 'Klaim Bagi Hasil', 'PRELIMINARY / UNVERIFIED'],
        ]);
    }
}

class DaftarTernakAktifSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'DAFTAR_TERNAK_AKTIF'; }

    public function headings(): array
    {
        return ['tag_id', 'legacy_tag_id', 'gender', 'breed', 'declared_generation', 'physical_status', 'birth_date', 'current_weight', 'location', 'gdrive_folder_url'];
    }

    public function collection()
    {
        return Animal::with(['breed', 'location', 'physStatus'])
            ->where('partner_id', $this->partnerId)
            ->where('is_active', true)
            ->get()
            ->map(function (Animal $a) {
                return [
                    'tag_id'              => (string) $a->tag_id,
                    'legacy_tag_id'       => $a->legacy_tag_id ? (string) $a->legacy_tag_id : null,
                    'gender'              => $a->gender,
                    'breed'               => $a->breed?->name ?? 'Lokal',
                    'declared_generation' => $a->declared_generation ?? 'UNKNOWN',
                    'physical_status'     => $a->physStatus?->name ?? 'SEHAT',
                    'birth_date'          => $a->birth_date?->format('Y-m-d'),
                    'current_weight'      => $a->current_weight ? (float) $a->current_weight : null,
                    'location'            => $a->location?->name ?? 'Kandang Utama',
                    'gdrive_folder_url'   => \App\Schemas\AnimalTemplateSchema::extractGDriveUrl($a),
                ];
            });
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT, 'B' => NumberFormat::FORMAT_TEXT];
    }
}

class HistoriTernakKeluarSheet implements WithTitle, WithHeadings, FromCollection, WithColumnFormatting, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'HISTORI_TERNAK_KELUAR'; }

    public function headings(): array
    {
        return ['tag_id', 'gender', 'breed', 'status_keluar', 'tanggal_keluar', 'keterangan'];
    }

    public function collection()
    {
        return Animal::where('partner_id', $this->partnerId)
            ->where('is_active', false)
            ->get()
            ->map(function (Animal $a) {
                return [
                    'tag_id'         => (string) $a->tag_id,
                    'gender'         => $a->gender,
                    'breed'          => $a->breed?->name ?? 'Lokal',
                    'status_keluar'  => $a->physStatus?->name ?? 'NONAKTIF',
                    'tanggal_keluar' => $a->updated_at?->format('Y-m-d'),
                    'keterangan'     => $a->notes ?? 'Ternak nonaktif/keluar',
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
        return ['tag_induk', 'tag_pejantan', 'tanggal_kawin', 'estimasi_lahir', 'status_reproduksi'];
    }

    public function collection()
    {
        $animalIds = Animal::where('partner_id', $this->partnerId)->pluck('id');

        return BreedingEvent::with(['femaleAnimal', 'maleAnimal'])
            ->whereIn('female_id', $animalIds)
            ->get()
            ->map(function (BreedingEvent $b) {
                return [
                    'tag_induk'         => (string) ($b->femaleAnimal?->tag_id ?? '-'),
                    'tag_pejantan'      => (string) ($b->maleAnimal?->tag_id ?? '-'),
                    'tanggal_kawin'     => $b->mating_date?->format('Y-m-d'),
                    'estimasi_lahir'    => $b->expected_birth_date?->format('Y-m-d'),
                    'status_reproduksi' => $b->status ?? 'BUNTING',
                ];
            });
    }
}

class BobotAdgSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

    public function title(): string { return 'BOBOT_ADG'; }

    public function headings(): array
    {
        return ['tag_id', 'tanggal_timbang', 'bobot_kg', 'catatan'];
    }

    public function collection()
    {
        $animalIds = Animal::where('partner_id', $this->partnerId)->pluck('id');

        return WeightLog::with('animal')
            ->whereIn('animal_id', $animalIds)
            ->orderBy('weigh_date', 'desc')
            ->get()
            ->map(function (WeightLog $w) {
                return [
                    'tag_id'          => (string) ($w->animal?->tag_id ?? '-'),
                    'tanggal_timbang' => $w->weigh_date?->format('Y-m-d'),
                    'bobot_kg'        => (float) $w->weight_kg,
                    'catatan'         => $w->notes ?? '-',
                ];
            });
    }
}

class KesehatanTreatmentSheet implements WithTitle, WithHeadings, FromCollection, ShouldAutoSize
{
    public function __construct(private string $partnerId) {}

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
                    'tanggal_treatment' => $t->treatment_date?->format('Y-m-d'),
                    'diagnosa'          => $t->diagnosis ?? 'Pemeriksaan Rutin',
                    'tindakan_obat'     => $t->treatment_details ?? '-',
                    'biaya'             => $t->cost ? (float) $t->cost : 0.0,
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
        $issues = collect();

        foreach ($animals as $a) {
            if (empty($a->birth_date)) {
                $issues->push(['tag_id' => (string) $a->tag_id, 'isu_kualitas_data' => 'Tanggal lahir kosong', 'tingkat_keparahan' => 'MEDIUM', 'rekomendasi_tindakan' => 'Isi tanggal lahir atau beri tanda estimasi']);
            }
            if (empty($a->current_weight)) {
                $issues->push(['tag_id' => (string) $a->tag_id, 'isu_kualitas_data' => 'Bobot terkini belum pernah ditimbang', 'tingkat_keparahan' => 'LOW', 'rekomendasi_tindakan' => 'Lakukan penimbangan']);
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
            ['Tanggal Cetak Laporan', now()->toIso8601String()],
            ['Data As Of', $this->asOfDate],
            ['Versi Skema Laporan', '1.1.0'],
            ['Status Settlement HPP', 'PRELIMINARY / UNVERIFIED'],
        ]);
    }
}
