<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AnimalPrintController extends Controller
{
    public function show(Animal $animal): View
    {
        // Generate QR Code content (e.g., URL to the operator action page)
        // For local dev, we use route. In prod, this will be the full domain.
        $url = route('operator.show', $animal->id);

        $qrCode = QrCode::size(200)->generate($url);

        return view('animals.print.show', compact('animal', 'qrCode'));
    }
}
