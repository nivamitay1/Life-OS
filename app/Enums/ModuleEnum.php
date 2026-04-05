<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleEnum: string
{
    case MONEY = 'Money';
    case RUNNING = 'Running';
    case BUILD = 'Build';
    case LEARN = 'Learn';
}
