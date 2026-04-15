<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\BuildLog;
use App\Models\BuildProject;
use App\Models\BuildTask;
use App\Models\LearnResource;
use App\Models\LearnSession;
use App\Models\MoneyEntry;
use App\Models\MoneyGoal;
use App\Models\MoneySubscription;
use App\Models\RunningPlan;
use App\Models\RunningRun;
use App\Models\User;
use App\Models\XpEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Demo User
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Money Module Seeding
        MoneySubscription::factory()->count(3)->for($user)->create();
        MoneyGoal::factory()->count(2)->for($user)->create([
            'target_amount' => 5000,
            'current_amount' => 1250,
        ]);
        MoneyEntry::factory()->count(20)->for($user)->create();

        // 3. Build Module Seeding
        $projects = BuildProject::factory()->count(2)->for($user)->create();
        foreach ($projects as $project) {
            BuildTask::factory()->count(5)->for($user)->create([
                'build_project_id' => $project->id
            ]);
            BuildLog::factory()->count(3)->for($user)->create([
                'build_project_id' => $project->id
            ]);
        }

        // 4. Running Module Seeding
        RunningPlan::factory()->for($user)->create([
            'status' => \App\Enums\PlanStatusEnum::ACTIVE,
            'goal_type' => 'marathon',
        ]);
        RunningRun::factory()->count(10)->for($user)->create();

        // 5. Learn Module Seeding
        $resources = LearnResource::factory()->count(3)->for($user)->create();
        foreach ($resources as $resource) {
            LearnSession::factory()->count(4)->for($user)->create([
                'learn_resource_id' => $resource->id
            ]);
        }

        // 6. System Seeding
        XpEvent::factory()->count(50)->for($user)->create();
        Badge::factory()->count(3)->for($user)->create();
    }
}
