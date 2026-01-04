<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPartner;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PartnerController extends Controller
{
    public function index(): View
    {
        $partners = MasterPartner::paginate(10);
        return view('admin.partners.index', compact('partners'));
    }

    public function create(): View
    {
        return view('admin.partners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
        ]);

        MasterPartner::create($validated);

        return redirect()->route('partners.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit(MasterPartner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function show(MasterPartner $partner): View
    {
        $partner->load('animals');
        $animals = $partner->animals()->with(['breed', 'location'])->paginate(10);

        return view('admin.partners.show', compact('partner', 'animals'));
    }

    public function update(Request $request, MasterPartner $partner): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
        ]);

        $partner->update($validated);

        return redirect()->route('partners.index')->with('success', 'Data Mitra berhasil diperbarui.');
    }

    public function destroy(MasterPartner $partner): RedirectResponse
    {
        // Check if partner has animals
        if ($partner->animals()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus Mitra yang memiliki hewan ternak. Harap pindahkan atau jual ternak terlebih dahulu.');
        }

        $partner->delete();
        return redirect()->route('partners.index')->with('success', 'Mitra berhasil dihapus.');
    }
}
