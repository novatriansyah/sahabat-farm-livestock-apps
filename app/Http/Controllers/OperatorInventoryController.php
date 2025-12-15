<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\MasterLocation;
use Illuminate\View\View;

class OperatorInventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::paginate(20);
        $locations = MasterLocation::all();

        return view('operator.inventory', compact('items', 'locations'));
    }
}
