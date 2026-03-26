<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DashboardSetting;
use Illuminate\Support\Facades\Auth;

class DashboardSettingController extends Controller
{
    public function index()
    {
        $userSettings = DashboardSetting::where('user_id', Auth::id())
            ->get()
            ->keyBy('component_name');

        $availableComponents = [
            'metrics' => 'Metrik Utama (Populasi, ADG, Penjualan, Laba)',
            'additional_stats' => 'Statistik Tambahan (HPP, Pakan, Obat, Kematian)',
            'charts_demographics' => 'Grafik Demografi & Kandang',
            'charts_financial' => 'Grafik Keuangan',
            'charts_mortality' => 'Grafik Tren Kematian',
            'charts_performance' => 'Grafik Performa & Biaya',
            'charts_biomass' => 'Grafik Biomassa',
        ];

        return view('settings.dashboard', compact('userSettings', 'availableComponents'));
    }

    public function store(Request $request)
    {
        $components = $request->input('components', []);
        
        // Reset all for the user? Or just update what's sent.
        // Let's iterate over ALL available to handle unchecked checkboxes (which aren't sent in POST)
        $availableKeys = [
            'metrics', 'additional_stats', 'charts_demographics', 
            'charts_financial', 'charts_mortality', 'charts_performance', 
            'charts_biomass'
        ];

        foreach ($availableKeys as $key) {
            DashboardSetting::updateOrCreate(
                ['user_id' => Auth::id(), 'component_name' => $key],
                [
                    'is_visible' => isset($components[$key]['is_visible']),
                    'order' => $components[$key]['order'] ?? 0
                ]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan tampilan berhasil disimpan.');
    }
}
