<?php

namespace App\Http\Controllers;

use App\Models\DataQualityIssue;
use App\Services\MissingDataGovernanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataQualityInboxController extends Controller
{
    protected MissingDataGovernanceService $governanceService;

    public function __construct(MissingDataGovernanceService $governanceService)
    {
        $this->governanceService = $governanceService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'OPEN');
        $query = DataQualityIssue::query();

        if ($status !== 'ALL') {
            $query->where('status', $status);
        }

        $issues = $query->orderBy('updated_at', 'desc')->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($issues);
        }

        return view('data_quality.inbox', compact('issues'));
    }

    public function resolve(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        $user = $request->user();
        $issue = $this->governanceService->completeData($id, $validated['data'], $user);

        return response()->json([
            'message' => 'Data berhasil dilengkapi dan isu ditutup.',
            'issue' => $issue,
        ]);
    }
}
