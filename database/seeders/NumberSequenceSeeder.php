<?php

namespace Database\Seeders;

use App\Models\NumberSequence;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NumberSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentPeriod = Carbon::now()->format('Ym');

        $sequences = [
            ['prefix' => 'DTSEN', 'period' => $currentPeriod, 'last_number' => 5],
            ['prefix' => 'PBI', 'period' => $currentPeriod, 'last_number' => 5],
            ['prefix' => 'ADU', 'period' => $currentPeriod, 'last_number' => 4],
            ['prefix' => 'RHS', 'period' => $currentPeriod, 'last_number' => 3],
            ['prefix' => 'RJK', 'period' => $currentPeriod, 'last_number' => 2],
            ['prefix' => 'RHS-REQ', 'period' => $currentPeriod, 'last_number' => 1],
            ['prefix' => 'BANSOS', 'period' => $currentPeriod, 'last_number' => 1],
        ];

        foreach ($sequences as $seq) {
            NumberSequence::updateOrCreate(
                [
                    'prefix' => $seq['prefix'],
                    'period' => $seq['period'],
                ],
                [
                    'last_number' => $seq['last_number'],
                ]
            );
        }
    }
}
