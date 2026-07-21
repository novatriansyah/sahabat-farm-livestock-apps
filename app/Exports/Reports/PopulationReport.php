<?php

namespace App\Exports\Reports;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PopulationReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'POPULASI'; }
    public function headings(): array { return ['tag_id','gender','breed','generation','age_category','location','partner','status']; }
    public function map($a): array { return [$a->tag_id,$a->gender,$a->breed?->name,$a->generation,'',$a->location?->name,$a->partner?->name,$a->is_active?'Aktif':'Non-Aktif']; }
    public function query() { return Animal::query()->with(['breed','location','partner'])->when($this->filters['partner_id']??null,fn($q,$v)=>$q->where('partner_id',$v)); }
}