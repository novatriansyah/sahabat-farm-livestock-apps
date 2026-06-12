<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\FarmSetting;
use App\Models\MasterBreed;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogueController extends Controller
{
    public function index(Request $request): View
    {
        $query = Animal::forSale()
            ->with(['photos', 'breed.category', 'latestWeightLog'])
            ->when($request->breed_id, fn($q, $v) => $q->where('breed_id', $v))
            ->when($request->search, fn($q, $v) => $q->where('sale_description', 'like', "%{$v}%"))
            ->latest();

        $animals = $query->paginate(12);

        // Only breeds that have at least one for-sale animal
        $breeds = MasterBreed::whereHas('animals', function ($q) {
            $q->where('is_for_sale', true)->where('is_active', true);
        })->get();

        $whatsapp = FarmSetting::get('whatsapp_number', '');

        return view('pages.catalogue', compact('animals', 'breeds', 'whatsapp'));
    }
}
