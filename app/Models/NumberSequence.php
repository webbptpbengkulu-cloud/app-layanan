<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NumberSequence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prefix',
        'period',
        'last_number',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_number' => 'integer',
        ];
    }

    /**
     * Get next number atomically using row locking.
     */
    public static function getNextNumber(string $prefix, ?string $period = null): int
    {
        $period ??= now()->format('Ym');

        return DB::transaction(function () use ($prefix, $period) {
            $sequence = self::where('prefix', $prefix)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = self::create([
                    'prefix' => $prefix,
                    'period' => $period,
                    'last_number' => 1,
                ]);

                return 1;
            }

            $sequence->increment('last_number');

            return $sequence->last_number;
        });
    }

    /**
     * Format a generated ticket or document code.
     * Example: DTSEN-202610-00012
     */
    public static function generateCode(string $prefix, int $padLength = 5, ?string $period = null): string
    {
        $period ??= now()->format('Ym');
        $nextNumber = self::getNextNumber($prefix, $period);

        return sprintf('%s-%s-%s', $prefix, $period, str_pad((string) $nextNumber, $padLength, '0', STR_PAD_LEFT));
    }
}
