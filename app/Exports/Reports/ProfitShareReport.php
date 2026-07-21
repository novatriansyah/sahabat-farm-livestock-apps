<?php
namespace App\Exports\Reports;
use App\Models\Animal;
use App\Models\MasterPartner;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class ProfitShareReport implements FromArray, WithTitle, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}
    public function title(): string { return 'BAGI HASIL'; }
    public function headings(): array { return ['partner','total_animals','total_purchase_price','total_hpp','est_profit_share_30pct']; }
    public function array(): array {
        $rows = [];
        $partners = MasterPartner::all();
        foreach($partners as $p) {
            $total = Animal::where('partner_id',$p->id)->where('is_active',true)->count();
            $purchase = Animal::where('partner_id',$p->id)->where('is_active',true)->sum('purchase_price');
            $hpp = Animal::where('partner_id',$p->id)->where('is_active',true)->sum('current_hpp');
            $profit = ($purchase - $hpp) * 0.3;
            $rows[] = [$p->name,$total,$purchase,$hpp,$profit];
        }
        return $rows;
    }
}