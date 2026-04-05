<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearnResource extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'type', 'title', 'author', 'status',
        'total_units', 'current_unit', 'unit_label',
        'current_position_label', 'started_at', 'last_session_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_session_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(LearnSession::class);
    }

    public function recalculateSnapshot(): void
    {
        $sumUnits = $this->sessions()->sum('units_completed');

        $latestSession = $this->sessions()->orderBy('date', 'desc')->orderBy('created_at', 'desc')->first();

        if ($this->total_units !== null) {
            $this->current_unit = min($sumUnits, $this->total_units);
        } else {
            $this->current_unit = $sumUnits;
        }

        if ($latestSession) {
            $this->current_position_label = $latestSession->ending_position_label;
            $this->last_session_at = $latestSession->date;
        } else {
            $this->current_position_label = null;
            $this->last_session_at = null;
        }

        if ($this->status === 'active' && $this->started_at === null && $this->sessions()->count() > 0) {
            $this->started_at = now();
        }

        $this->saveQuietly();
    }
}
