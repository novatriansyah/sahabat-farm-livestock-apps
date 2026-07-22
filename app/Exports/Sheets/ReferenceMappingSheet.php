<?php

namespace App\Exports\Sheets;

use App\Models\MasterBreed;
use App\Models\MasterLocation;
use App\Models\MasterPartner;
use App\Models\MasterPhysStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ReferenceMappingSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize
{
    public function title(): string
    {
        return 'REFERENCE_MAPPING';
    }

    public function collection(): Collection
    {
        $rows = collect([]);

        foreach (MasterBreed::all() as $b) {
            $rows->push(['ENTITY' => 'BREED', 'ID' => $b->id, 'NAME' => $b->name, 'CODE' => $b->code ?? '']);
        }
        foreach (MasterLocation::all() as $l) {
            $rows->push(['ENTITY' => 'LOCATION', 'ID' => $l->id, 'NAME' => $l->name, 'CODE' => '']);
        }
        foreach (MasterPartner::all() as $p) {
            $rows->push(['ENTITY' => 'PARTNER', 'ID' => $p->id, 'NAME' => $p->name, 'CODE' => '']);
        }
        foreach (MasterPhysStatus::all() as $s) {
            $rows->push(['ENTITY' => 'PHYSICAL_STATUS', 'ID' => $s->id, 'NAME' => $s->name, 'CODE' => '']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['ENTITY', 'ID', 'NAME', 'CODE'];
    }
}
