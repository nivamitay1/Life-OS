<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatusEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildProject extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'status',
    ];

    protected $casts = [
        'status' => ProjectStatusEnum::class,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(BuildTask::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BuildLog::class);
    }
}
