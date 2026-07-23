<?php

namespace App\Services;

use App\Models\Animal;

class CurrentAnimalValueResolver
{
    public const STATUS_ACTUAL = 'ACTUAL';
    public const STATUS_ESTIMATED = 'ESTIMATED';
    public const STATUS_ASSUMED = 'ASSUMED';
    public const STATUS_UNKNOWN = 'UNKNOWN';

    /**
     * Determine priority rank (higher is better).
     */
    public function getPriorityRank(?string $status): int
    {
        return match (strtoupper((string) $status)) {
            self::STATUS_ACTUAL => 4,
            self::STATUS_ESTIMATED => 3,
            self::STATUS_ASSUMED => 2,
            default => 1,
        };
    }

    /**
     * Resolve attribute value and status for an animal.
     */
    public function resolveAttribute(Animal $animal, string $field): array
    {
        $value = $animal->getAttribute($field);
        $status = $animal->getAttribute($field . '_status')
            ?? ($animal->confidence === 'HIGH' ? self::STATUS_ACTUAL : ($value !== null ? self::STATUS_ASSUMED : self::STATUS_UNKNOWN));

        return [
            'field'  => $field,
            'value'  => $value,
            'status' => $value === null ? self::STATUS_UNKNOWN : $status,
            'rank'   => $this->getPriorityRank($status),
        ];
    }
}
