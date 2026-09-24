<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rehabilitation_case_id',
        'officer_id',
        'assessment_date',
        'result',
        'service_needs',
        'recommendation',
        'needs_referral',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'needs_referral' => 'boolean',
        ];
    }

    public function rehabilitationCase(): BelongsTo
    {
        return $this->belongsTo(RehabilitationCase::class);
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }
}
