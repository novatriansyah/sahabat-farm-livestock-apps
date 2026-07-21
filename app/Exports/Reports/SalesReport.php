<?php
namespace App\Exports\Reports;
use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class SalesReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'PENJUALAN'; }
    public function headings(): array { return ['invoice_no','customer','date','total','status','items']; }
    public function map($i): array { return [$i->invoice_number,$i->customer_name,$i->issued_date?->format('Y-m-d'),$i->total_amount,$i->status,$i->items?->count()]; }
    public function query() { return Invoice::query()->with('items')->orderBy('issued_date','desc'); }
}