<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkoutStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case SKIPPED = 'skipped';
    case PARTIALLY_COMPLETED = 'partially_completed';
}
