<?php

namespace App\Http\Controllers;

use App\Models\MatingColony;
use App\Models\Animal;
use App\Models\MasterLocation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MatingColonyController extends Controller
{
    public function index()
    {
        $query = MatingColony::with(['sire', 'location', 'members.dam'])->orderBy('start_date', 'desc');
        
        if (auth()->user()->role === 'MITRA') {
            $query->where('partner_id', auth()->user()->partner_id);
        }
        
        $colonies = $query->paginate(10);
        return view('mating_colonies.index', compact('colonies'));
    }

    public function create()
    {
        $sires = Animal::where('gender', 'JANTAN')
            ->where('is_active', true)
            ->where('health_status', 'SEHAT')
            ->when(auth()->user()->role === 'MITRA', function($q) {
                $q->where('partner_id', auth()->user()->partner_id);
            })
            ->get();
            
        $locations = MasterLocation::all();
        
        // Find dams that are not currently in an active colony
        $activeDams = DB::table('mating_colony_members')
            ->join('mating_colonies', 'mating_colony_members.mating_colony_id', '=', 'mating_colonies.id')
            ->where('mating_colonies.status', 'AKTIF')
            ->pluck('mating_colony_members.dam_id');

        $dams = Animal::where('gender', 'BETINA')
            ->where('is_active', true)
            ->whereNotIn('health_status', ['TERJUAL', 'MATI'])
            ->whereNotIn('id', $activeDams)
            ->when(auth()->user()->role === 'MITRA', function($q) {
                $q->where('partner_id', auth()->user()->partner_id);
            })
            ->get();
            
        return view('mating_colonies.create', compact('sires', 'locations', 'dams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sire_id' => 'required|exists:animals,id',
            'location_id' => 'required|exists:master_locations,id',
            'start_date' => 'required|date',
            'dam_ids' => 'required|array|min:1',
            'dam_ids.*' => 'exists:animals,id'
        ]);

        // Inbreeding Check
        $sire = Animal::findOrFail($validated['sire_id']);
        $dams = Animal::whereIn('id', $validated['dam_ids'])->get();

        $inbreedingWarnings = [];
        foreach ($dams as $dam) {
            if ($dam->sire_id && $dam->sire_id === $sire->id) {
                $inbreedingWarnings[] = "{$dam->tag_id} adalah anak dari pejantan {$sire->tag_id}.";
            }
            if ($sire->sire_id && $dam->sire_id === $sire->sire_id) {
                $inbreedingWarnings[] = "{$dam->tag_id} dan pejantan {$sire->tag_id} memiliki Bapak yang sama.";
            }
            if ($sire->dam_id && $dam->dam_id && $dam->dam_id === $sire->dam_id) {
                $inbreedingWarnings[] = "{$dam->tag_id} dan pejantan {$sire->tag_id} memiliki Induk yang sama.";
            }
        }

        if (count($inbreedingWarnings) > 0 && !$request->has('force_inbreeding')) {
            return back()->withErrors(['Inbreeding terdeteksi: ' . implode(' ', $inbreedingWarnings) . ' Centang kotak "Paksa Simpan" atau hapus indukan berisiko.'])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $colony = MatingColony::create([
                'name' => $validated['name'],
                'sire_id' => $validated['sire_id'],
                'location_id' => $validated['location_id'],
                'start_date' => $validated['start_date'],
                'partner_id' => auth()->user()->partner_id,
                'status' => 'AKTIF'
            ]);

            foreach ($validated['dam_ids'] as $damId) {
                $colony->members()->create([
                    'dam_id' => $damId,
                    'joined_date' => $colony->start_date,
                    'status' => 'KAWIN'
                ]);
            }
        });

        return redirect()->route('mating-colonies.index')->with('success', 'Koloni kawin berhasil dibuat.');
    }

    public function show(MatingColony $matingColony)
    {
        $matingColony->load(['sire', 'location', 'members.dam']);
        return view('mating_colonies.show', compact('matingColony'));
    }

    public function update(Request $request, MatingColony $matingColony)
    {
        if ($request->has('complete')) {
            $matingColony->update([
                'status' => 'SELESAI',
                'end_date' => Carbon::now()
            ]);
            
            $matingColony->members()->where('status', 'KAWIN')->update([
                'status' => 'SIAP',
                'left_date' => Carbon::now()
            ]);
            
            return redirect()->route('mating-colonies.show', $matingColony)->with('success', 'Koloni berhasil diselesaikan.');
        }
        
        return redirect()->back();
    }
}
