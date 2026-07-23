<?php

namespace App\Jobs;

use App\Events\SourceDataCorrected;
use App\Services\RecalculationOrchestrator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecalculateDerivedValuesJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public SourceDataCorrected $event) {}

    public function handle(RecalculationOrchestrator $orchestrator): void
    {
        $orchestrator->handleCorrection($this->event);
    }
}
