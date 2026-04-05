<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkoutTypeEnum: string
{
    case EASY = 'easy';
    case WORKOUT = 'workout';
    case LONG = 'long';
    case INTERVAL = 'interval';
}
