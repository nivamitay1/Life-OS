<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryTypeEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyEntry extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'type', 'amount', 'currency', 'date', 'description',
    ];

    protected $casts = [
        'type' => EntryTypeEnum::class,
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}
