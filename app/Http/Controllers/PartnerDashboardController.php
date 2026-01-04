<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ExitLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PartnerDashboardController extends Controller
{
    public function index(\App\Services\DashboardService $dashboardService): View
    {
        $user = Auth::user();
        
        if (!$user->partner_id) {
            abort(403, 'User is not associated with any Partner account.');
        }

        // Delegate all logic to the centralized service
        // The service handles scoping by partner_id automatically
        $data = $dashboardService->getDashboardData($user->partner_id);

        // Return the same view as the main dashboard
        return view('dashboard', $data);
    }
}
