<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearnSession extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'learn_resource_id', 'duration_minutes',
        'ending_position_label', 'units_completed', 'notes', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(LearnResource::class, 'learn_resource_id');
    }

    protected static function booted(): void
    {
        static::saved(function ($session) {
            if ($session->resource) {
                $session->resource->recalculateSnapshot();
            }
        });

        static::deleted(function ($session) {
            if ($session->resource) {
                $session->resource->recalculateSnapshot();
            }
        });
    }
}
