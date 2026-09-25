<?php

namespace App\Actions\Dtsen;

use App\Models\DtsenPurpose;

class CheckDecileEligibility
{
    /**
     * Check if applicant decile is eligible for given DTSEN purpose.
     *
     * @return array{eligible: bool, message: string}
     */
    public static function execute(int $decile, DtsenPurpose $purpose): array
    {
        if ($decile <= 0) {
            return [
                'eligible' => false,
                'message' => 'Desil tidak valid. Data tidak ditemukan di basis data DTKS.',
            ];
        }

        if ($decile > $purpose->max_decile) {
            return [
                'eligible' => false,
                'message' => sprintf(
                    'Desil %d melebihi batas maksimal kelayakan untuk tujuan %s (Maksimal Desil %d).',
                    $decile,
                    $purpose->name,
                    $purpose->max_decile
                ),
            ];
        }

        return [
            'eligible' => true,
            'message' => sprintf(
                'Memenuhi syarat: Desil %d berada dalam ambang batas kelayakan (Maksimal Desil %d).',
                $decile,
                $purpose->max_decile
            ),
        ];
    }
}
