<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\MasterPartner;

class PartnerReportPdfService
{
    /**
     * Generate HTML representation for PDF export of Partner Report.
     */
    public function generateReportData(string $partnerId, ?string $asOfDate = null): array
    {
        $asOfDate = $asOfDate ?? now()->format('Y-m-d');
        $partner = MasterPartner::find($partnerId);
        $partnerName = $partner ? $partner->name : 'Mitra';

        $animals = Animal::with(['breed', 'location', 'physStatus'])
            ->where('partner_id', $partnerId)
            ->get();

        $activeAnimals = $animals->where('is_active', true);
        $inactiveAnimals = $animals->where('is_active', false);

        $summary = [
            'partner_name'        => $partnerName,
            'partner_id'          => $partnerId,
            'as_of_date'          => $asOfDate,
            'total_registered'    => $animals->count(),
            'total_active'        => $activeAnimals->count(),
            'total_inactive'      => $inactiveAnimals->count(),
            'active_male'         => $activeAnimals->where('gender', 'JANTAN')->count(),
            'active_female'       => $activeAnimals->where('gender', 'BETINA')->count(),
            'hpp_status_label'    => 'PRELIMINARY / UNVERIFIED',
        ];

        return [
            'summary'          => $summary,
            'active_animals'   => $activeAnimals->values()->toArray(),
            'inactive_animals' => $inactiveAnimals->values()->toArray(),
        ];
    }
}
