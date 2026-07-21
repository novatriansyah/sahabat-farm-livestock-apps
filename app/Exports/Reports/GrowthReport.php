<?php
namespace App\Exports\Reports;
use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class GrowthReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'PERTUMBUHAN'; }
    public function headings(): array { return ['tag_id','gender','birth_date','age_months','latest_weight','daily_adg','last_weighed_at']; }
    public function map($a): array { return [$a->tag_id,$a->gender,$a->birth_date?->format('Y-m-d'),$a->birth_date?->diffInMonths(now()),$a->latestWeight()?->weight,$a->daily_adg,$a->latestWeight()?->weighed_at?->format('Y-m-d')]; }
    public function query() { return Animal::query()->where('is_active',true)->when($this->filters['partner_id']??null,fn($q,$v)=>$q->where('partner_id',$v)); }
}