<?php

declare(strict_types=1);

namespace App\Services\Omnibar\Commands\Navigation;

use App\Models\User;
use App\Services\Omnibar\Contracts\OmnibarCommand;
use App\Services\Omnibar\OmnibarAction;
use App\Services\Omnibar\OmnibarResult;

class NavigateCommand implements OmnibarCommand
{
    public function pattern(): string
    {
        return '/^(?:go\s+to|open)\s+(money|running|build|learn|dashboard|home)/i';
    }

    public function preview(array $matches): string
    {
        $dest = ucfirst(strtolower($matches[1]));
        return "Will open: {$dest}";
    }

    public function handle(User $user, array $matches): OmnibarResult
    {
        $dest = strtolower($matches[1]);
        $url = match ($dest) {
            'money' => route('money.index'),
            'running' => route('running.index'),
            'build' => route('build.index'),
            'learn' => route('learn.index'),
            default => route('dashboard'),
        };

        return new OmnibarResult(
            action: OmnibarAction::NAVIGATE,
            data: ['url' => $url],
        );
    }
}
