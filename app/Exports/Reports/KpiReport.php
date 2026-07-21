<?php
namespace App\Exports\Reports;
use App\Models\Animal;
use App\Models\BreedingEvent;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class KpiReport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'KPI'; }
    public function headings(): array { return ['metric','value','benchmark','status']; }
    public function array(): array {
        $totalDams = Animal::where('gender','BETINA')->where('is_active',true)->count();
        $births = BreedingEvent::whereIn('event_type',['LAHIR','LAHIR_TUNGGAL','LAHIR_KEMBAR'])->count();
        $offspring = BreedingEvent::whereIn('event_type',['LAHIR','LAHIR_TUNGGAL','LAHIR_KEMBAR'])->sum('offspring_count');
        return [
            ['Total Indukan',$totalDams,'-','-'],
            ['Total Kelahiran',$births,'-','-'],
            ['Lambing Rate',round($births>0?($offspring/$totalDams*100):0,1).'%','150-175%',($offspring/$totalDams)>=1.5?'BAIK':'PERLU PERHATIAN'],
            ['Fertility Rate',round($totalDams>0?($births/$totalDams*100):0,1).'%','>90%',($births/$totalDams)>=0.9?'BAIK':'PERLU PERHATIAN'],
            ['Prolificacy',round($births>0?($offspring/$births):0,2),'1.6-1.8',($offspring/$births)>=1.6?'BAIK':'PERLU PERHATIAN'],
        ];
    }
}