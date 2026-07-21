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
        return [
            'invoice_number', 'customer_name', 'customer_phone', 'issue_date',
            'tag_id', 'animal_breed', 'unit_price', 'line_total',
            'subtotal', 'discount', 'tax', 'total',
            'dp_amount', 'status', 'notes',
            'created_at',
        ];
    }

    public function map($invoice): array
    {
        $items = $invoice->items ?? collect();
        $rows = [];
        foreach ($items as $item) {
            $rows[] = [
                $invoice->invoice_number,
                $invoice->customer_name,
                $invoice->customer_phone,
                $invoice->issue_date?->format('Y-m-d') ?: '',
                $item->animal?->tag_id,
                $item->animal?->breed?->name,
                $item->unit_price,
                $item->line_total,
                $invoice->subtotal,
                $invoice->discount,
                $invoice->tax,
                $invoice->total,
                $invoice->dp_amount,
                $invoice->status,
                $invoice->notes,
                $invoice->created_at?->format('Y-m-d H:i:s'),
            ];
        }
        return $rows;
    }

    public function query()
    {
        return Invoice::query()
            ->with(['items.animal.breed'])
            ->when($this->filters['from'] ?? null, fn($q, $d) => $q->whereDate('issue_date', '>=', $d))
            ->when($this->filters['to'] ?? null, fn($q, $d) => $q->whereDate('issue_date', '<=', $d))
            ->when($this->filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->orderBy('issue_date');
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,   // tag_id
        ];
    }
}