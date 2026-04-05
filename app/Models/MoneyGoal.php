<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyGoal extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'name', 'target_amount', 'current_amount',
        'target_date', 'color', 'is_claimed',
    ];

    protected $casts = [
        'target_date' => 'date',
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'is_claimed' => 'boolean',
    ];
}
