<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $items = InventoryItem::paginate(10);
        return view('inventory.index', compact('items'));
    }
}
