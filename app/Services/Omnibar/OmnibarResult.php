<?php

declare(strict_types=1);

namespace App\Services\Omnibar;

/**
 * Domain result object describing the outcome of an Omnibar command.
 *
 * Decoupled from HTTP — the controller converts this to a JsonResponse.
 */
final class OmnibarResult
{
    /**
     * @param array<string, mixed> $data Additional payload (entry_id, distance, resource_id, url, etc.)
     */
    public function __construct(
        public readonly OmnibarAction $action,
        public readonly array $data = [],
        public readonly ?string $message = null,
        public readonly ?string $detail = null,
    ) {}
}
