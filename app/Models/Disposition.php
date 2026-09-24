<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Disposition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dispositionable_type',
        'dispositionable_id',
        'from_user_id',
        'to_work_unit_id',
        'to_user_id',
        'instructions',
        'disposed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'disposed_at' => 'datetime',
        ];
    }

    public function dispositionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toWorkUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class, 'to_work_unit_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
