<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillingCycleEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneySubscription extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'name', 'amount', 'billing_cycle',
        'next_billing_date', 'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_cycle' => BillingCycleEnum::class,
        'next_billing_date' => 'date',
        'is_active' => 'boolean',
    ];
}
