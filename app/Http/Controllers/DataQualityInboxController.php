<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\DataQualityIssue;
use App\Services\MissingDataGovernanceService;
use App\Services\RecalculationOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataQualityInboxController extends Controller
{
    protected MissingDataGovernanceService $governanceService;
    protected RecalculationOrchestrator $orchestrator;

    public function __construct(
        MissingDataGovernanceService $governanceService,
        RecalculationOrchestrator $orchestrator
    ) {
        $this->governanceService = $governanceService;
        $this->orchestrator = $orchestrator;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'OPEN');
        $query = DataQualityIssue::query();

        // Tenant Isolation
        $user = auth()->user();
        if ($user && $user->role === 'MITRA') {
            $partnerAnimalIds = Animal::where('partner_id', $user->partner_id)->pluck('id');
            $query->whereIn('animal_id', $partnerAnimalIds);
        }

        if ($status !== 'ALL') {
            $query->where('status', $status);
        }

        $issues = $query->orderBy('updated_at', 'desc')->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($issues);
        }

        return view('data_quality.inbox', compact('issues'));
    }

    public function resolve(Request $request, string $id)
    {
        $validated = $request->validate([
            'data' => 'required|array',
        ]);

        $user = $request->user();
        $issue = DataQualityIssue::findOrFail($id);

        // Tenant Policy Check
        if ($user && $user->role === 'MITRA') {
            $animal = Animal::find($issue->animal_id);
            if ($animal && $animal->partner_id != $user->partner_id) {
                abort(403, 'Akses ditolak untuk isu mitra lain.');
            }
        }

        $issue = $this->governanceService->completeData($id, $validated['data'], $user);

        // Trigger Recalculation Orchestrator
        if ($issue->animal_id) {
            \App\Events\SourceDataCorrected::dispatch(
                (string) $issue->animal_id,
                array_keys($validated['data']),
                date('Y-m-d'),
                null,
                null,
                null,
                null,
                (string) \Illuminate\Support\Str::uuid(),
                $user->name ?? 'User Inbox'
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data berhasil dilengkapi dan isu ditutup.',
                'issue' => $issue,
            ]);
        }

        return redirect()->route('data-quality-inbox.index')->with('success', 'Data berhasil dilengkapi dan rekalkulasi diproses.');
    }
}
