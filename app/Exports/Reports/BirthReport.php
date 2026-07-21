<?php

namespace App\Exports\Reports;

use App\Models\BreedingEvent;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BirthReport implements FromQuery, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'KELAHIRAN'; }
    public function headings(): array { return ['dam_tag_id','event_date','offspring_count','gender','breed','notes']; }
    public function map($e): array { return [$e->animal?->tag_id,$e->event_date?->format('Y-m-d'),$e->offspring_count,$e->animal?->gender,$e->animal?->breed?->name,$e->notes]; }
    public function query() { return BreedingEvent::query()->whereIn('event_type',['LAHIR','LAHIR_TUNGGAL','LAHIR_KEMBAR'])->with('animal.breed'); }
}