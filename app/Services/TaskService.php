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
            ['title' => 'Berikan hijauan saja (tanpa konsentrat) selama 24 jam', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
            ['title' => 'Berikan minuman Gula Merah + Asam Jawa', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
            ['title' => 'Pengecekan Masuk Karantina', 'type' => 'ARRIVAL', 'due_date' => Carbon::now()],
        ];

        foreach ($tasks as $task) {
            AnimalTask::create([
                'animal_id' => $animal->id,
                'title' => $task['title'],
                'type' => $task['type'],
                'status' => 'MENUNGGU',
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
            'title' => 'Pemeriksaan Kesehatan Rutin (Berat & Suhu)',
            'type' => 'ROUTINE',
            'status' => 'MENUNGGU',
            'due_date' => Carbon::now()->endOfMonth(),
        ]);
    }
}
