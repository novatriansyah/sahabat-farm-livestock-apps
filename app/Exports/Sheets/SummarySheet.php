<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use App\Models\MasterPartner;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SummarySheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'REKAP'; }

    public function headings(): array
    {
        return ['metric', 'value', 'notes'];
    }

    public function array(): array
    {
        $totalAll = Animal::count();
        $totalActive = Animal::where('is_active', true)->count();
        $totalInactive = Animal::where('is_active', false)->count();
        $totalMale = Animal::where('gender', 'JANTAN')->count();
        $totalFemale = Animal::where('gender', 'BETINA')->count();
        $totalWithSire = Animal::whereNotNull('sire_id')->count();
        $totalWithDam = Animal::whereNotNull('dam_id')->count();
        $totalMitra = Animal::whereNotNull('partner_id')->count();
        $totalPartners = MasterPartner::count();
        $totalPurchasePrice = Animal::sum('purchase_price');
        $totalCurrentHpp = Animal::sum('current_hpp');
        $totalUnnumbered = Animal::whereNull('tag_id')->orWhere('tag_id', '')->count();
        $orphans = Animal::where('acquisition_type', 'HASIL_TERNAK')->whereNull('dam_id')->count();
        $totalDead = Animal::where('is_active', false)->where('health_status', 'DECEASED')->count();
        $totalSold = Animal::where('is_active', false)->where('health_status', 'SOLD')->count();
        $totalWeightLogs = \App\Models\WeightLog::count();
        $totalTreatmentLogs = \App\Models\TreatmentLog::count();
        $totalBreedingEvents = \App\Models\BreedingEvent::count();
        $totalMatingColonies = \App\Models\MatingColony::count();
        $totalInvoices = \App\Models\Invoice::count();

        return [
            ['total_all', $totalAll, 'Total seluruh ternak (aktif + nonaktif)'],
            ['total_active', $totalActive, 'Total ternak aktif'],
            ['total_inactive', $totalInactive, 'Total ternak nonaktif (mati/terjual/hilang)'],
            ['total_jantan', $totalMale, 'Total jantan'],
            ['total_betina', $totalFemale, 'Total betina'],
            ['total_with_sire', $totalWithSire, 'Ternak dengan data pejantan'],
            ['total_with_dam', $totalWithDam, 'Ternak dengan data induk'],
            ['total_mitra_ternak', $totalMitra, 'Total ternak milik mitra'],
            ['total_partners', $totalPartners, 'Total mitra terdaftar'],
            ['total_purchase_price', $totalPurchasePrice, 'Total harga beli seluruh ternak'],
            ['total_current_hpp', $totalCurrentHpp, 'Total HPP saat ini'],
            ['total_unnumbered', $totalUnnumbered, 'Ternak tanpa tag_id'],
            ['orphans_without_dam', $orphans, 'Anakan tanpa data induk (harus 0)'],
            ['total_dead', $totalDead, 'Ternak mati'],
            ['total_sold', $totalSold, 'Ternak terjual'],
            ['total_weight_logs', $totalWeightLogs, 'Total catatan timbangan'],
            ['total_treatment_logs', $totalTreatmentLogs, 'Total catatan kesehatan'],
            ['total_breeding_events', $totalBreedingEvents, 'Total event perkawinan'],
            ['total_mating_colonies', $totalMatingColonies, 'Total koloni kawin'],
            ['total_invoices', $totalInvoices, 'Total invoice/penjualan'],
        ];
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT, 'C' => NumberFormat::FORMAT_TEXT];
    }
}