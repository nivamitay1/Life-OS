<?php

declare(strict_types=1);

namespace App\Services\Omnibar\Commands\Running;

use App\Models\User;
use App\Services\Omnibar\Contracts\OmnibarCommand;
use App\Services\Omnibar\OmnibarAction;
use App\Services\Omnibar\OmnibarResult;

class DraftRunCommand implements OmnibarCommand
{
    public function pattern(): string
    {
        return '/^run\s+(\d+(?:\.\d+)?)\s*(km|mi)$/i';
    }

    public function preview(array $matches): string
    {
        return "Will start run draft: {$matches[1]} {$matches[2]}";
    }

    public function handle(User $user, array $matches): OmnibarResult
    {
        $distance = (float) $matches[1];
        $unit = strtolower($matches[2]);

        if ($unit === 'mi') {
            $distance *= 1.60934;
        }

        return new OmnibarResult(
            action: OmnibarAction::DRAFT_RUN,
            data: ['distance' => round($distance, 2)],
        );
    }
}
