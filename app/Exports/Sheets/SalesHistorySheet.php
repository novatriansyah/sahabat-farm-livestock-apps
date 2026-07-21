<?php

namespace App\Exports\Sheets;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class SalesHistorySheet implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(private array $filters = []) {}

    public function title(): string { return 'PENJUALAN'; }

    public function headings(): array
    {
        return ['invoice_number', 'customer_name', 'issued_date', 'total_amount', 'status', 'animal_tags', 'notes'];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->customer_name,
            $invoice->issued_date?->format('Y-m-d') ?: '',
            $invoice->total_amount,
            $invoice->status,
            $invoice->items?->map(fn($i) => $i->relatedAnimal?->tag_id)->filter()->implode(', '),
            $invoice->notes,
        ];
    }

    public function query()
    {
        return Invoice::query()
            ->with(['items.relatedAnimal'])
            ->where(function ($q) {
                $q->where('type', 'COMMERCIAL')->orWhere('status', 'PAID');
            })
            ->orderBy('issued_date');
    }

    public function columnFormats(): array
    {
        return ['F' => NumberFormat::FORMAT_TEXT];
    }

    private function forceText($value): string
    {
        return "=\"{$value}\"";
    }
}