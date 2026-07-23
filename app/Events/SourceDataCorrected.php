<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class SourceDataCorrected
{
    use Dispatchable;

    public function __construct(
        public string $animalId,
        public array $changedFields,
        public ?string $effectiveDate = null,
        public ?int $oldPartnerId = null,
        public ?int $newPartnerId = null,
        public ?int $oldLocationId = null,
        public ?int $newLocationId = null,
        public ?string $correlationId = null,
        public ?string $actor = null
    ) {
        $this->correlationId = $correlationId ?? (string) \Illuminate\Support\Str::uuid();
    }
}
