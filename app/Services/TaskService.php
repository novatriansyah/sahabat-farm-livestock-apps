<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalTask;
use Carbon\Carbon;

class TaskService
{
    /**
     * Generate tasks for a newly arrived animal (Purchase).
     */
    public function generateArrivalTasks(Animal $animal): void
    {
        $tasks = [
            ['title' => 'Give forage only (no concentrate) for 24h', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
            ['title' => 'Give Gula Merah + Asam Jawa drink', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
            ['title' => 'Quarantine Entry Check', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
        ];

        foreach ($tasks as $task) {
            AnimalTask::create([
                'animal_id' => $animal->id,
                'title' => $task['title'],
                'type' => $task['type'],
                'status' => 'PENDING',
                'due_date' => $task['due_date'],
            ]);
        }
    }

    /**
     * Generate routine tasks (Mockup for Scheduled Task runner).
     */
    public function generateRoutineTasks(Animal $animal): void
    {
        // Monthly Check
        AnimalTask::create([
            'animal_id' => $animal->id,
            'title' => 'Routine Health Check (Weight & Temp)',
            'type' => 'ROUTINE',
            'status' => 'PENDING',
            'due_date' => Carbon::now()->endOfMonth(),
        ]);
    }
}
