<?php

use App\Enums\ModuleEnum;
use App\Models\XpEvent;
use App\Models\User;
use App\Services\ProgressEngine;

beforeEach(function () {
    $this->engine = new ProgressEngine();
});

it('calculates levels correctly based on XP', function () {
    // Level 1: 0-49 XP
    $level1 = $this->engine->calculateLevel(0);
    expect($level1['level'])->toBe(1);
    expect($level1['progress_percent'])->toBe(0.0);

    // Level 2 starts at 50 XP (sqrt(50/50) + 1 = 2)
    $level2 = $this->engine->calculateLevel(50);
    expect($level2['level'])->toBe(2);
    expect($level2['progress_percent'])->toBe(0.0);

    // Level 3 starts at 200 XP (sqrt(200/50) + 1 = 3)
    $level3 = $this->engine->calculateLevel(200);
    expect($level3['level'])->toBe(3);
    
    // Halfway to level 3 (Level 2 is 50-199 XP. 125 is middle)
    $halfway = $this->engine->calculateLevel(125);
    expect($halfway['level'])->toBe(2);
    expect($halfway['progress_percent'])->toBe(50.0);
});

it('awards XP to a user', function () {
    $user = User::factory()->create();
    
    $event = $this->engine->addXp($user->id, ModuleEnum::MONEY, 50, 'Test reward');
    
    expect($event)->toBeInstanceOf(XpEvent::class);
    expect($event->amount)->toBe(50);
    expect($event->module_slug)->toBe(ModuleEnum::MONEY->value);
    expect($event->user_id)->toBe($user->id);
    
    $this->assertDatabaseHas('xp_events', [
        'user_id' => $user->id,
        'amount' => 50,
        'reason' => 'Test reward',
    ]);
});

it('calculates streaks accurately', function () {
    $user = User::factory()->create();
    
    // Log XP for today
    XpEvent::factory()->for($user)->create(['created_at' => now()]);
    // Log XP for yesterday
    XpEvent::factory()->for($user)->create(['created_at' => now()->subDay()]);
    // Log XP for 2 days ago
    XpEvent::factory()->for($user)->create(['created_at' => now()->subDays(2)]);
    
    // Gap: No XP 3 days ago
    
    // Log XP for 4 days ago
    XpEvent::factory()->for($user)->create(['created_at' => now()->subDays(4)]);

    $stats = $this->engine->getUserStats($user->id);
    
    expect($stats['streak'])->toBe(3);
    expect($stats['streak_max'])->toBe(3);
});

it('identifies slipping modules', function () {
    $user = User::factory()->create();
    
    // Lots of Build XP
    XpEvent::factory()->count(5)->for($user)->create([
        'module_slug' => ModuleEnum::BUILD->value,
        'amount' => 10,
        'created_at' => now(),
    ]);

    // Some Money XP
    XpEvent::factory()->for($user)->create([
        'module_slug' => ModuleEnum::MONEY->value,
        'amount' => 10,
        'created_at' => now(),
    ]);

    // A little Running XP
    XpEvent::factory()->for($user)->create([
        'module_slug' => ModuleEnum::RUNNING->value,
        'amount' => 5,
        'created_at' => now(),
    ]);

    // 0 XP for Learn - should be slipping


    $stats = $this->engine->getUserStats($user->id);
    
    expect($stats['slipping']['module'])->toBe(ModuleEnum::LEARN->value); // 0 XP
    expect($stats['strongestModule'])->toBe(ModuleEnum::BUILD->value);
});
