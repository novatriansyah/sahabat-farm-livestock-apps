<?php
namespace App\Exports\Reports;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class AuditReport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'AUDIT'; }
    public function headings(): array { return ['entity','total','new_last_30d','notes']; }
    public function array(): array {
        $animals = \App\Models\Animal::count();
        $animalsNew = \App\Models\Animal::where('created_at','>=',now()->subDays(30))->count();
        $weights = \App\Models\WeightLog::count();
        $treatments = \App\Models\TreatmentLog::count();
        $events = \App\Models\BreedingEvent::count();
        return [
            ['Animals',$animals,$animalsNew,'Active: '.\App\Models\Animal::where('is_active',true)->count()],
            ['Weight Logs',$weights,'-','Latest: '.\App\Models\WeightLog::latest()->first()?->created_at?->format('Y-m-d')],
            ['Treatments',$treatments,'-','-'],
            ['Breeding Events',$events,'-','-'],
        ];
    }
}