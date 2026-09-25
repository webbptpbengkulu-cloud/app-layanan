<?php

namespace App\Actions\ServiceRequest;

use App\Models\NumberSequence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateTicketNumber
{
    /**
     * Generate an atomic, gapless ticket/reference number.
     */
    public static function execute(string $prefix = 'DTSEN'): string
    {
        $period = Carbon::now()->format('Ym');

        return DB::transaction(function () use ($prefix, $period) {
            $sequence = NumberSequence::where('prefix', $prefix)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = NumberSequence::create([
                    'prefix' => $prefix,
                    'period' => $period,
                    'last_number' => 1,
                ]);
                $number = 1;
            } else {
                $sequence->last_number += 1;
                $sequence->save();
                $number = $sequence->last_number;
            }

            return sprintf('%s-%s-%04d', $prefix, $period, $number);
        });
    }
}
