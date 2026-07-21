<?php
namespace App\Exports\Reports;
use App\Models\TreatmentLog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class HealthReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'KESEHATAN'; }
    public function headings(): array { return ['tag_id','date','type','diagnosis','medicine','cost','vet']; }
    public function map($t): array { return [$t->animal?->tag_id,$t->treatment_date?->format('Y-m-d'),$t->treatment_type,$t->diagnosis,$t->medicine,$t->cost,$t->veterinarian]; }
    public function query() { return TreatmentLog::query()->with('animal')->orderBy('treatment_date','desc'); }
}