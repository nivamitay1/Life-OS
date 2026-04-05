<?php

declare(strict_types=1);

namespace App\Services\Omnibar\Commands\Learn;

use App\Models\LearnResource;
use App\Models\User;
use App\Services\Omnibar\Contracts\OmnibarCommand;
use App\Services\Omnibar\OmnibarAction;
use App\Services\Omnibar\OmnibarResult;

class DraftLearnSessionCommand implements OmnibarCommand
{
    public function pattern(): string
    {
        return '/^(?:read|study)\s+(\d+)\s*m(?:in(?:ute)?s?)?\s+(?:of|on)\s+(.+)$/i';
    }

    public function preview(array $matches): string
    {
        return "Will log study session: {$matches[1]} mins · {$matches[2]}";
    }

    public function handle(User $user, array $matches): OmnibarResult
    {
        $duration = (int) $matches[1];
        $searchTerm = strtolower(trim($matches[2]));

        $activeResources = LearnResource::forUser($user->id)
            ->where('status', 'active')
            ->get();

        $bestMatch = null;
        $bestScore = -1;

        foreach ($activeResources as $resource) {
            similar_text(strtolower($resource->title), $searchTerm, $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestMatch = $resource;
            }
        }

        if ($bestMatch && $bestScore > 75) {
            return new OmnibarResult(
                action: OmnibarAction::DRAFT_LEARN,
                data: ['resource_id' => $bestMatch->id, 'duration' => $duration],
            );
        }

        return new OmnibarResult(
            action: OmnibarAction::DRAFT_LEARN,
            data: ['ambiguous' => true, 'duration' => $duration],
        );
    }
}
