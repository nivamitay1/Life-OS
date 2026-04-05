<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardAggregator;
use App\Services\ProgressEngine;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ProgressEngine $progressEngine,
        private readonly DashboardAggregator $aggregator,
    ) {}

    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $stats = $this->progressEngine->getUserStats($userId);
        $priorities = $this->aggregator->getPriorities($userId);
        $badges = $this->aggregator->getBadges($userId);

        return Inertia::render('Dashboard', [
            'priorities' => $priorities,
            'suggestedAction' => $priorities[0] ?? null,
            'badges' => $badges,
            'streaks' => [
                'current' => $stats['streak'],
                'max' => $stats['streak_max'],
                'todayCompleted' => $stats['streak'] > 0,
            ],
            'stats' => [
                'overallLevel' => $stats['overall']['level'],
                'overallXp' => $stats['overall']['xp'],
            ],
        ]);
    }
}
