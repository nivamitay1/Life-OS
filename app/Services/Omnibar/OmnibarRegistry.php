<?php

declare(strict_types=1);

namespace App\Services\Omnibar;

use App\Models\User;
use App\Services\Omnibar\Contracts\OmnibarCommand;
use Illuminate\Contracts\Foundation\Application;

/**
 * Registry that dispatches text commands to the first matching OmnibarCommand.
 *
 * Commands are registered via service provider tags — no reflection magic.
 */
class OmnibarRegistry
{
    /** @var OmnibarCommand[] */
    private array $commands;

    public function __construct(Application $app)
    {
        $this->commands = iterator_to_array($app->tagged('omnibar.commands'));
    }

    /**
     * Find the first command whose pattern matches the input and execute it.
     */
    public function dispatch(User $user, string $input): OmnibarResult
    {
        $input = trim($input);

        if (empty($input)) {
            return new OmnibarResult(
                action: OmnibarAction::ERROR,
                message: 'Empty command',
            );
        }

        foreach ($this->commands as $command) {
            if (preg_match($command->pattern(), $input, $matches)) {
                return $command->handle($user, $matches);
            }
        }

        return new OmnibarResult(
            action: OmnibarAction::UNKNOWN,
            message: 'Unrecognized Command',
        );
    }

    /**
     * Get a preview string for the current input (used for UI helper text).
     */
    public function preview(string $input): ?string
    {
        $input = trim($input);

        foreach ($this->commands as $command) {
            if (preg_match($command->pattern(), $input, $matches)) {
                return $command->preview($matches);
            }
        }

        return null;
    }
}
