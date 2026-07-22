<?php

namespace App\Exports;

use App\Exports\Sheets\AnimalsCurrentSheet;
use App\Exports\Sheets\DataQualityIssuesSheet;
use App\Exports\Sheets\ExitDeathEventsSheet;
use App\Exports\Sheets\HealthTreatmentEventsSheet;
use App\Exports\Sheets\LocationHistorySheet;
use App\Exports\Sheets\ManifestSheet;
use App\Exports\Sheets\MediaLinksSheet;
use App\Exports\Sheets\OwnershipHistorySheet;
use App\Exports\Sheets\ParentageBirthEventsSheet;
use App\Exports\Sheets\ReferenceMappingSheet;
use App\Exports\Sheets\StatusEventsSheet;
use App\Exports\Sheets\TagHistorySheet;
use App\Exports\Sheets\WeightEventsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnimalMasterExport implements WithMultipleSheets
{
    public function __construct(
        private string $version = '2.0.0',
        private string $commitHash = 'f8a8c7cc96429eb7a74e20350b05a14975444612',
        private string $environment = 'staging'
    ) {}

    public function sheets(): array
    {
        return [
            'MANIFEST'                 => new ManifestSheet($this->version, $this->commitHash, $this->environment),
            'ANIMALS_CURRENT'          => new AnimalsCurrentSheet(),
            'PARENTAGE_BIRTH_EVENTS'   => new ParentageBirthEventsSheet(),
            'WEIGHT_EVENTS'            => new WeightEventsSheet(),
            'TAG_HISTORY'              => new TagHistorySheet(),
            'STATUS_EVENTS'            => new StatusEventsSheet(),
            'LOCATION_HISTORY'         => new LocationHistorySheet(),
            'OWNERSHIP_HISTORY'        => new OwnershipHistorySheet(),
            'EXIT_DEATH_EVENTS'        => new ExitDeathEventsSheet(),
            'HEALTH_TREATMENT_EVENTS'  => new HealthTreatmentEventsSheet(),
            'MEDIA_LINKS'              => new MediaLinksSheet(),
            'DATA_QUALITY_ISSUES'      => new DataQualityIssuesSheet(),
            'REFERENCE_MAPPING'        => new ReferenceMappingSheet(),
        ];
    }
}
