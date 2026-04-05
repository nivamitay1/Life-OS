<?php

declare(strict_types=1);

namespace App\Services\Omnibar\Contracts;

use App\Models\User;
use App\Services\Omnibar\OmnibarResult;

interface OmnibarCommand
{
    /**
     * The regex pattern this command responds to.
     */
    public function pattern(): string;

    /**
     * Generate a preview string for the UI helper text.
     *
     * @param array<int, string> $matches Regex match groups
     */
    public function preview(array $matches): string;

    /**
     * Execute the command and return a domain result.
     *
     * @param array<int, string> $matches Regex match groups
     */
    public function handle(User $user, array $matches): OmnibarResult;
}
