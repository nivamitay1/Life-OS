<?php

declare(strict_types=1);

namespace App\Enums;

enum PlanStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ABANDONED = 'abandoned';
}
