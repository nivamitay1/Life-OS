<?php

declare(strict_types=1);

namespace App\Enums;

enum BillingCycleEnum: string
{
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';
}
