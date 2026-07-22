<?php

namespace App\Traits;

use App\Models\MasterPartner;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait PartnerScopeTrait
{
    /**
     * Resolve scoped partner ID based on authenticated user role and request inputs.
     * 
     * @param Request $request
     * @return string|null Null means ALL partners (Owner only), string is resolved partner_id.
     * @throws ValidationException
     */
    protected function resolvePartnerScope(Request $request): ?string
    {
        $user = $request->user();

        // If user is MITRA role, force partner_id from their account/relationship
        if ($user && $user->hasRole('MITRA')) {
            $partnerId = $user->partner_id ?? $user->partner?->id;
            if (!$partnerId) {
                // Try matching by email or user_id in master_partners table if partner_id property is missing
                $partnerId = MasterPartner::where('id', $user->partner_id)
                    ->orWhere('contact_info', 'like', "%{$user->email}%")
                    ->value('id');
            }

            if (!$partnerId) {
                throw ValidationException::withMessages([
                    'partner_id' => ['Akun Mitra tidak terhubung dengan master partner mana pun.'],
                ]);
            }

            return (string) $partnerId;
        }

        // For PEMILIK / Admin roles: check optional partner_id filter
        $requestedPartnerId = $request->input('partner_id');

        if (!empty($requestedPartnerId) && $requestedPartnerId !== 'ALL' && $requestedPartnerId !== 'all') {
            $exists = MasterPartner::where('id', $requestedPartnerId)->exists();
            if (!$exists) {
                throw ValidationException::withMessages([
                    'partner_id' => ['Mitra yang dipilih tidak valid atau tidak ditemukan.'],
                ]);
            }
            return (string) $requestedPartnerId;
        }

        return null; // ALL partners
    }
}
