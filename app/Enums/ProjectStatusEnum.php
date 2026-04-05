<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}
