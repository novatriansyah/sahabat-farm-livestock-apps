<?php
namespace App\Jobs;
use App\Services\ReconciliationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
class ProcessReconciliation implements ShouldQueue
{
    use Queueable;
    public function __construct(public Collection $uploadedRows, public Collection $selectedChanges) {}
    public function handle(ReconciliationService $service): void { $service->applySelected($this->selectedChanges); }
}