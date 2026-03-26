<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync dashboard alerts into database notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing notifications...');

        $this->syncWeaningAlerts();
        $this->syncSeparationAlerts();
        $this->syncVaccineAlerts();
        $this->syncMatingAlerts();
        $this->syncLowStockAlerts();

        $this->info('Sync completed.');
    }

    protected function syncWeaningAlerts()
    {
        $cempeId = \App\Models\MasterPhysStatus::where('name', 'Cempe')->value('id');
        $animals = \App\Models\Animal::where('is_active', true)
            ->where('current_phys_status_id', $cempeId)
            ->whereDate('birth_date', '<=', \Carbon\Carbon::now()->subDays(35))
            ->get();

        foreach ($animals as $animal) {
            $days = \Carbon\Carbon::parse($animal->birth_date)->diffInDays(now());
            $this->notifyRelevantUsers($animal, "Siap Sapih ({$days} Hari)! Cempe ini sudah mendekati usia sapih.", 'info');
        }
    }

    protected function syncSeparationAlerts()
    {
        $animals = \App\Models\Animal::where('is_active', true)
            ->whereDate('birth_date', '<=', \Carbon\Carbon::now()->subDays(60))
            ->whereHas('physStatus', function($q) { $q->where('name', 'Cempe'); })
            ->get();

        foreach ($animals as $animal) {
            $months = number_format(\Carbon\Carbon::parse($animal->birth_date)->floatDiffInMonths(now()), 1, ',', '.');
            $this->notifyRelevantUsers($animal, "Waktunya Sapih (Pisah Induk)! Cempe ini sudah berusia {$months} bulan.", 'warning');
        }
    }

    protected function syncVaccineAlerts()
    {
        $logs = \App\Models\TreatmentLog::where('type', 'VACCINE')
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [\Carbon\Carbon::now(), \Carbon\Carbon::now()->addDays(14)])
            ->with('animal')
            ->get();

        foreach ($logs as $log) {
            $date = \Carbon\Carbon::parse($log->next_due_date)->format('d M');
            $this->notifyRelevantUsers($log->animal, "Jadwal Vaksinasi/Obat ({$log->notes}) Terjadwal pada {$date}.", 'info');
        }
    }

    protected function syncMatingAlerts()
    {
        $events = \App\Models\BreedingEvent::where('status', 'MENUNGGU')
            ->whereDate('mating_date', '<=', \Carbon\Carbon::now()->subDays(60))
            ->with(['dam', 'sire'])
            ->get();

        foreach ($events as $event) {
            $this->notifyRelevantUsers($event->dam, "Waktunya Pisah Pejantan! Pasangan ini (Sire: {$event->sire->tag_id}) sudah disatukan > 2 bulan.", 'warning');
        }
    }

    protected function syncLowStockAlerts()
    {
        $items = \App\Models\InventoryItem::where('current_stock', '<', 10)->get();
        $adminUsers = \App\Models\User::where('role', 'PEMILIK')->get();

        foreach ($items as $item) {
            foreach ($adminUsers as $user) {
                $stockVal = number_format($item->current_stock, 1, ',', '.');
                $message = "Peringatan Stok Low! {$item->name} sisa {$stockVal} {$item->unit}.";
                
                $exists = $user->unreadNotifications()
                    ->where('data->message', $message)
                    ->exists();

                if (!$exists) {
                     $user->notify(new \App\Notifications\AnimalStatusNotification(null, $message, 'danger', route('inventory.index')));
                }
            }
        }
    }

    protected function notifyRelevantUsers($animal, $message, $type = 'info')
    {
        if (!$animal) return;

        $users = \App\Models\User::whereIn('role', ['PEMILIK', 'PETERNAK'])->get();
        
        if ($animal->partner_id) {
            $partnerUsers = \App\Models\User::where('partner_id', $animal->partner_id)->get();
            $users = $users->concat($partnerUsers)->unique('id');
        }

        $userIds = $users->pluck('id');

        // Get IDs of users who already have this specific unread notification
        $notifiedUserIds = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->whereIn('notifiable_id', $userIds)
            ->whereNull('read_at')
            ->where('data->animal_id', $animal->id)
            ->where('data->message', $message)
            ->pluck('notifiable_id');

        // Filter out users who have already been notified
        $usersToNotify = $users->whereNotIn('id', $notifiedUserIds);

        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\AnimalStatusNotification($animal, $message, $type));
        }
    }
}
