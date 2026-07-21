<?php
namespace App\Exports\Reports;
use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class HppReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'HPP'; }
    public function headings(): array { return ['tag_id','gender','generation','partner','purchase_price','current_hpp','hpp_pct']; }
    public function map($a): array { return [$a->tag_id,$a->gender,$a->generation,$a->partner?->name,$a->purchase_price,$a->current_hpp,$a->purchase_price>0?round($a->current_hpp/$a->purchase_price*100,1).'%':'N/A']; }
    public function query() { return Animal::query()->where('is_active',true)->with('partner')->orderBy('current_hpp','desc'); }
}