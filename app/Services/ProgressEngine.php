<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ModuleEnum;
use App\Models\XpEvent;
use Illuminate\Support\Carbon;

class ProgressEngine
{
    /**
     * Calculate level based on XP using a quadratic curve.
     *
     * @return array{level: int, xp: int, current_level_base: int, next_level_base: int, next_relative: int, xp_relative: int, progress_percent: float}
     */
    public function calculateLevel(int $xp): array
    {
        $xp = max(0, $xp);

        $level = (int) floor(sqrt($xp / 50)) + 1;

        $currentLevelXpBase = (int) (pow($level - 1, 2) * 50);
        $nextLevelXpBase = (int) (pow($level, 2) * 50);

        $progress = $nextLevelXpBase > $currentLevelXpBase
            ? (($xp - $currentLevelXpBase) / ($nextLevelXpBase - $currentLevelXpBase)) * 100
            : 100;

        return [
            'level' => $level,
            'xp' => $xp,
            'current_level_base' => $currentLevelXpBase,
            'next_level_base' => $nextLevelXpBase,
            'next_relative' => $nextLevelXpBase - $currentLevelXpBase,
            'xp_relative' => $xp - $currentLevelXpBase,
            'progress_percent' => min(100.0, max(0.0, $progress)),
        ];
    }

    /**
     * Award XP to a user for a specific module.
     */
    public function addXp(int $userId, ModuleEnum $module, int $amount, string $reason): XpEvent
    {
        return XpEvent::create([
            'user_id' => $userId,
            'module_slug' => $module->value,
            'amount' => $amount,
            'reason' => $reason,
        ]);
    }

    /**
     * Get comprehensive stats for a user across all modules.
     *
     * @return array{overall: array, modules: array, strongestModule: string, strongestXp: string, streak: int, streak_max: int, slipping: array}
     */
    public function getUserStats(int $userId): array
    {
        $events = XpEvent::forUser($userId)->get();

        $totalXp = (int) $events->sum('amount');
        $overall = $this->calculateLevel($totalXp);

        $moduleStats = [];
        $strongestModule = 'None';
        $strongestXp = -1;

        foreach (ModuleEnum::cases() as $module) {
            $modXp = (int) $events->where('module_slug', $module->value)->sum('amount');
            $moduleStats[$module->value] = $this->calculateLevel($modXp);

            if ($modXp > $strongestXp) {
                $strongestXp = $modXp;
                $strongestModule = $module->value;
            }
        }

        $streak = $this->calculateStreak($userId);
        $slipping = $this->getSlippingModule($userId);

        return [
            'overall' => $overall,
            'modules' => $moduleStats,
            'strongestModule' => $strongestModule === 'None' ? 'Newbie' : $strongestModule,
            'strongestXp' => '+' . $strongestXp . ' XP total',
            'streak' => $streak['current'],
            'streak_max' => $streak['max'],
            'slipping' => $slipping,
        ];
    }

    /**
     * @return array{current: int, max: int}
     */
    private function calculateStreak(int $userId): array
    {
        $dates = XpEvent::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($e) => Carbon::parse($e->created_at)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        if (empty($dates)) {
            return ['current' => 0, 'max' => 0];
        }

        $currentStreak = 0;
        $checkDate = Carbon::today();

        if (!in_array($checkDate->format('Y-m-d'), $dates)) {
            $checkDate->subDay();
            if (!in_array($checkDate->format('Y-m-d'), $dates)) {
                return ['current' => 0, 'max' => $this->computeMaxStreak($dates)];
            }
        }

        foreach ($dates as $date) {
            if ($date === $checkDate->format('Y-m-d')) {
                $currentStreak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return [
            'current' => $currentStreak,
            'max' => max($currentStreak, $this->computeMaxStreak($dates)),
        ];
    }

    /**
     * @param string[] $dates Unique dates in descending order
     */
    private function computeMaxStreak(array $dates): int
    {
        if (empty($dates)) {
            return 0;
        }

        $sorted = array_values(array_unique($dates));
        sort($sorted);

        $maxStreak = 1;
        $current = 1;

        for ($i = 1; $i < count($sorted); $i++) {
            $prev = Carbon::parse($sorted[$i - 1]);
            $curr = Carbon::parse($sorted[$i]);

            if ($prev->diffInDays($curr) === 1) {
                $current++;
                $maxStreak = max($maxStreak, $current);
            } else {
                $current = 1;
            }
        }

        return $maxStreak;
    }

    /**
     * @return array{module: string, reason: string}
     */
    private function getSlippingModule(int $userId): array
    {
        $recentEvents = XpEvent::forUser($userId)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        $minXp = PHP_INT_MAX;
        $slippingMod = 'None';

        foreach (ModuleEnum::cases() as $module) {
            $xp = (int) $recentEvents->where('module_slug', $module->value)->sum('amount');
            if ($xp < $minXp) {
                $minXp = $xp;
                $slippingMod = $module->value;
            }
        }

        if ($minXp === PHP_INT_MAX) {
            return ['module' => 'All', 'reason' => 'Start logging!'];
        }

        return [
            'module' => $slippingMod,
            'reason' => "Only {$minXp} XP recently",
        ];
    }
}
