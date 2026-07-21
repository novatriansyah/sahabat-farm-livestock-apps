<?php
namespace App\Exports\Reports;
use App\Models\InventoryItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class InventoryReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'INVENTORY'; }
    public function headings(): array { return ['item_name','category','stock','unit','unit_cost','location','supplier']; }
    public function map($i): array { return [$i->item_name,$i->category?->name,$i->stock,$i->unit,$i->unit_cost,$i->location?->name,$i->supplier?->name]; }
    public function query() { return InventoryItem::query()->with(['category','location','supplier'])->orderBy('item_name'); }
}