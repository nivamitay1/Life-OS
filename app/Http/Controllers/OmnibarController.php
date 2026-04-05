<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MoneyEntry;
use App\Services\Omnibar\OmnibarRegistry;
use App\Services\Omnibar\OmnibarResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OmnibarController extends Controller
{
    public function __construct(
        private readonly OmnibarRegistry $registry,
    ) {}

    public function dispatchCommand(Request $request): JsonResponse
    {
        $result = $this->registry->dispatch(
            $request->user(),
            $request->input('command', ''),
        );

        return $this->toJsonResponse($result);
    }

    public function undoMoney(Request $request, MoneyEntry $entry): JsonResponse
    {
        $this->authorize('delete', $entry);

        $entry->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Convert a domain OmnibarResult into an HTTP JsonResponse.
     */
    private function toJsonResponse(OmnibarResult $result): JsonResponse
    {
        return response()->json(array_filter([
            'action' => $result->action->value,
            'message' => $result->message,
            'detail' => $result->detail,
            ...$result->data,
        ]));
    }
}
