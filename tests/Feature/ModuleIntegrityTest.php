<?php

use App\Enums\EntryTypeEnum;
use App\Enums\LearnResourceStatusEnum;
use App\Enums\PlanStatusEnum;
use App\Enums\ProjectStatusEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\WorkoutTypeEnum;
use App\Models\BuildProject;
use App\Models\LearnResource;
use App\Models\RunningPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

/**
 * Money Module
 */
it('can log a money entry and award XP', function () {
    $response = $this->post(route('money.store'), [
        'type' => EntryTypeEnum::EXPENSE->value,
        'amount' => 50,
        'date' => now()->toDateString(),
        'description' => 'Test Expense',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $this->assertDatabaseHas('money_entries', [
        'user_id' => $this->user->id,
        'amount' => -50,
    ]);
    $this->assertDatabaseHas('xp_events', [
        'user_id' => $this->user->id,
        'module_slug' => 'Money',
    ]);
});

/**
 * Build Module
 */
it('can create a project and log a task', function () {
    $response = $this->post(route('build.project.store'), [
        'name' => 'Test Project',
        'description' => 'Testing description',
        'status' => ProjectStatusEnum::ACTIVE->value,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $project = BuildProject::first();
    expect($project->name)->toBe('Test Project');

    $response = $this->post(route('build.task.store'), [
        'project_id' => $project->id,
        'title' => 'Initial Task',
        'status' => TaskStatusEnum::TODO->value,
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('build_tasks', [
        'build_project_id' => $project->id,
        'title' => 'Initial Task',
    ]);
});

/**
 * Running Module
 */
it('can log a run and find an active plan', function () {
    // Create an active plan first
    $plan = RunningPlan::factory()->for($this->user)->create([
        'status' => PlanStatusEnum::ACTIVE,
    ]);

    $response = $this->post(route('running.store'), [
        'name' => 'Morning Jog',
        'date' => now()->toDateString(),
        'distance' => 5.5,
        'duration' => '00:30:00',
        'type' => WorkoutTypeEnum::EASY->value,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $this->assertDatabaseHas('running_runs', [
        'user_id' => $this->user->id,
        'distance' => 5.5,
    ]);
});

/**
 * Learn Module
 */
it('can track a resource and log a study session', function () {
    $response = $this->post(route('learn.resource.store'), [
        'type' => 'book',
        'title' => 'Clean Code',
        'author' => 'Uncle Bob',
        'total_units' => 10,
        'unit_label' => 'chapter',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $resource = LearnResource::first();
    expect($resource)->not->toBeNull();

    $response = $this->post(route('learn.session.store'), [
        'learn_resource_id' => $resource->id,
        'duration_minutes' => 45,
        'units_completed' => 1,
        'ending_position_label' => 'Chapter 1',
        'date' => now()->toDateString(),
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('learn_sessions', [
        'learn_resource_id' => $resource->id,
        'duration_minutes' => 45,
    ]);

    // Snapshot should be updated
    $resource->refresh();
    expect($resource->current_unit)->toBe(1);
});
