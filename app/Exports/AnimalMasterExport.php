<?php

namespace App\Exports;

use App\Exports\Sheets\IndukanSheet;
use App\Exports\Sheets\AnakanSheet;
use App\Exports\Sheets\WeightHistorySheet;
use App\Exports\Sheets\TreatmentHistorySheet;
use App\Exports\Sheets\EarTagHistorySheet;
use App\Exports\Sheets\OwnershipHistorySheet;
use App\Exports\Sheets\HppHistorySheet;
use App\Exports\Sheets\MatingColonySheet;
use App\Exports\Sheets\BirthEventSheet;
use App\Exports\Sheets\SalesHistorySheet;
use App\Exports\Sheets\DataConflictSheet;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\ManifestSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnimalMasterExport implements WithMultipleSheets
{
    public function __construct(
        private array $filters = [],
        private string $commitHash = '',
        private string $environment = 'local',
    ) {}

    public function sheets(): array
    {
        return [
            'MANIFEST'         => new ManifestSheet('1.0.0', $this->commitHash, $this->environment),
            'INDUKAN'          => new IndukanSheet($this->filters),
            'ANAKAN'           => new AnakanSheet($this->filters),
            'RIWAYAT BOBOT'    => new WeightHistorySheet($this->filters),
            'RIWAYAT KESEHATAN'=> new TreatmentHistorySheet($this->filters),
            'RIWAYAT EARTAG'   => new EarTagHistorySheet(),
            'RIWAYAT PEMILIK'  => new OwnershipHistorySheet(),
            'RIWAYAT HPP'      => new HppHistorySheet($this->filters),
            'KOLONI KAWIN'     => new MatingColonySheet($this->filters),
            'KELAHIRAN'        => new BirthEventSheet($this->filters),
            'PENJUALAN'        => new SalesHistorySheet($this->filters),
            'KONFLIK DATA'     => new DataConflictSheet(),
            'REKAP'            => new SummarySheet($this->filters),
        ];
    }
}
