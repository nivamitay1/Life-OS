<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TaskStatusEnum;
use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildTask extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'build_project_id', 'title', 'status', 'is_blocker',
    ];

    protected $casts = [
        'status' => TaskStatusEnum::class,
        'is_blocker' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(BuildProject::class, 'build_project_id');
    }
}
