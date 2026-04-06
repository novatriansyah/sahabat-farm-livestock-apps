<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\AnimalTask;
use Carbon\Carbon;

class TaskService
{
    /**
     * Generate tasks based on an event type.
     */
    private function generateTasksForEvent(Animal $animal, string $eventType): void
    {
        $templates = \App\Models\MasterSop::where('event_type', $eventType)
            ->where('is_active', true)
            ->get();

        foreach ($templates as $template) {
            AnimalTask::create([
                'animal_id' => $animal->id,
                'title' => $template->title,
                'type' => $template->task_type,
                'status' => 'MENUNGGU',
                'due_date' => Carbon::now()->addDays($template->due_days_offset),
            ]);
        }
    }

    /**
     * Generate tasks for a newly arrived animal (Purchase).
     */
    public function generateArrivalTasks(Animal $animal): void
    {
        $this->generateTasksForEvent($animal, 'ARRIVAL');
    }

    /**
     * Generate tasks for a newborn animal (Birth).
     */
    public function generateBirthTasks(Animal $animal): void
    {
        $this->generateTasksForEvent($animal, 'BIRTH');
    }

    /**
     * Generate routine tasks (Mockup for Scheduled Task runner).
     */
    public function generateRoutineTasks(Animal $animal): void
    {
        $this->generateTasksForEvent($animal, 'ROUTINE');
    }
}
