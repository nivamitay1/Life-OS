<?php

declare(strict_types=1);

namespace App\Services\Omnibar;

enum OmnibarAction: string
{
    case NAVIGATE = 'navigate';
    case MONEY_INSERT = 'money_insert';
    case DRAFT_RUN = 'draft_run';
    case DRAFT_LEARN = 'draft_learn';
    case UNKNOWN = 'unknown';
    case ERROR = 'error';
}
