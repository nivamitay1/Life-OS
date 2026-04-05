<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Learn\CompleteResourceAction;
use App\Actions\Learn\LogSessionAction;
use App\Actions\Learn\TrackResourceAction;
use App\Enums\LearnResourceStatusEnum;
use App\Http\Requests\ConfirmCompletionRequest;
use App\Http\Requests\StoreLearnResourceRequest;
use App\Http\Requests\StoreLearnSessionRequest;
use App\Http\Resources\LearnResourceResource;
use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Services\ProgressEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearnController extends Controller
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;
        $stats = $this->progressEngine->getUserStats($userId);

        $activeResources = LearnResource::forUser($userId)
            ->where('status', LearnResourceStatusEnum::ACTIVE)
            ->orderBy('last_session_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $revisionQueue = LearnResource::forUser($userId)
            ->where('status', LearnResourceStatusEnum::COMPLETED)
            ->orderBy('completed_at', 'desc')
            ->get();

        $recentSessions = LearnSession::forUser($userId)
            ->with('resource:id,title')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'resource' => $s->resource->title ?? 'Unknown',
                'duration' => $s->duration_minutes . ' mins',
                'date' => $s->date->isoFormat('MMM Do'),
            ]);

        return Inertia::render('Learn/Index', [
            'activeResources' => LearnResourceResource::collection($activeResources),
            'revisionQueue' => LearnResourceResource::collection($revisionQueue),
            'levelData' => $stats['modules']['Learn'] ?? ['level' => 1, 'xp' => 0, 'next_level_base' => 100, 'progress_percent' => 0],
            'recentSessions' => $recentSessions,
            'draftResource' => $request->query('draftResource'),
            'draftSession' => (bool) $request->query('draftSession'),
        ]);
    }

    public function storeResource(StoreLearnResourceRequest $request, TrackResourceAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function storeSession(StoreLearnSessionRequest $request, LogSessionAction $action): RedirectResponse
    {
        $action->handle($request->user()->id, $request->validated());
        return redirect()->back();
    }

    public function confirmCompletion(ConfirmCompletionRequest $request, LearnResource $resource, CompleteResourceAction $action): RedirectResponse
    {
        $this->authorize('update', $resource);
        $action->handle($request->user()->id, $resource, $request->validated());
        return redirect()->back();
    }
}
