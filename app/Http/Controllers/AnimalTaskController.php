<?php

namespace App\Http\Controllers;

use App\Models\AnimalTask;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AnimalTaskController extends Controller
{
    /**
     * Mark a task as completed.
     */
    public function markCompleted(AnimalTask $task): RedirectResponse
    {
        // Only allow STAF or PETERNAK roles (handled in routes or policies, but adding a check here for safety)
        if (!in_array(auth()->user()->role, ['STAF', 'PETERNAK', 'PEMILIK'])) {
            abort(403);
        }

        $task->update([
            'status' => 'COMPLETED',
        ]);

        return back()->with('success', 'Tugas berhasil ditandai sebagai selesai.');
    }
}
