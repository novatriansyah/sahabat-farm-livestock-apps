<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
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
        $totalActive = Animal::where('is_active', true)->count();
        $totalIndukan = Animal::where('gender', 'BETINA')->where('is_active', true)->count();
        $totalAnakan = Animal::where('gender', 'JANTAN')->where('is_active', true)->whereNotNull('dam_id')->count();
        $totalMitra = Animal::where('is_active', true)->whereNotNull('partner_id')->count();
        $totalPartners = \App\Models\MasterPartner::count();
        $totalPurchasePrice = Animal::where('is_active', true)->sum('purchase_price');
        $totalCurrentHpp = Animal::where('is_active', true)->sum('current_hpp');
        $totalUnnumbered = Animal::where('is_active', true)->whereNull('tag_id')->count();
        $orphans = Animal::where('acquisition_type', 'HASIL_TERNAK')->whereNull('dam_id')->count();

        return [
            ['total_active', $totalActive, 'Total ternak aktif'],
            ['total_indukan', $totalIndukan, 'Total indukan betina aktif'],
            ['total_anakan', $totalAnakan, 'Total anakan aktif (memiliki dam_id)'],
            ['total_mitra_ternak', $totalMitra, 'Total ternak milik mitra'],
            ['total_partners', $totalPartners, 'Total mitra terdaftar'],
            ['total_purchase_price', $totalPurchasePrice, 'Total harga beli'],
            ['total_current_hpp', $totalCurrentHpp, 'Total HPP saat ini'],
            ['total_unnumbered', $totalUnnumbered, 'Ternak tanpa tag_id'],
            ['orphans_without_dam', $orphans, 'Anakan tanpa data induk (harus 0)'],
        ];
    }

    public function columnFormats(): array
    {
        return ['A' => NumberFormat::FORMAT_TEXT, 'C' => NumberFormat::FORMAT_TEXT];
    }
}