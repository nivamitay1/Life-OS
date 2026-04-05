<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryTypeEnum: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
}
