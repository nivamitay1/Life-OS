<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuildLog extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'user_id', 'build_project_id', 'type', 'content',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(BuildProject::class, 'build_project_id');
    }
}
