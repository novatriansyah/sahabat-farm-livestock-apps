<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\ExitLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // 1. Births (Animals born in this month)
        $birthsQuery = Animal::whereMonth('birth_date', $month)
            ->whereYear('birth_date', $year)
            ->with(['dam', 'sire', 'breed', 'weightLogs']);
        
        // 2. Deaths (Exit Logs type DEATH)
        $deathsQuery = ExitLog::where('exit_type', 'DEATH')
            ->whereMonth('exit_date', $month)
            ->whereYear('exit_date', $year)
            ->with(['animal.breed']);

        if ($request->user()->role === 'PARTNER') {
            $partnerId = $request->user()->partner_id;
            $birthsQuery->where('partner_id', $partnerId);
            $deathsQuery->whereHas('animal', function($q) use ($partnerId) {
                $q->where('partner_id', $partnerId);
            });
        }

        $births = $birthsQuery->get();
        $deaths = $deathsQuery->get();

        return view('admin.reports.index', compact('births', 'deaths', 'month', 'year'));
    }
}
