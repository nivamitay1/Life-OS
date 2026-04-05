<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::controller(\App\Http\Controllers\OmnibarController::class)->prefix('omnibar')->group(function () {
        Route::post('/dispatch', 'dispatchCommand')->name('omnibar.dispatch');
        Route::post('/undo-money/{entry}', 'undoMoney')->name('omnibar.undoMoney');
    });

    Route::controller(\App\Http\Controllers\MoneyController::class)->prefix('money')->name('money.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/goals', 'storeGoal')->name('goal.store');
        Route::patch('/goals/{goal}', 'updateGoal')->name('goal.update');
        Route::post('/goals/{goal}/claim', 'claimGoal')->name('goal.claim');
        Route::delete('/goals/{goal}', 'destroyGoal')->name('goal.destroy');
        Route::post('/subscriptions', 'storeSubscription')->name('subscription.store');
        Route::patch('/subscriptions/{subscription}', 'updateSubscription')->name('subscription.update');
        Route::patch('/subscriptions/{subscription}/toggle', 'toggleSubscription')->name('subscription.toggle');
        Route::delete('/subscriptions/{subscription}', 'destroySubscription')->name('subscription.destroy');
        Route::post('/seed-blueprint', 'seedBlueprint')->name('seed-blueprint');
    });

    Route::controller(\App\Http\Controllers\RunningController::class)->prefix('running')->name('running.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/seed-template', 'seedTemplate')->name('seed');
        Route::post('/start-plan/{template}', 'startPlan')->name('plan.start');
        
        // Custom Plan & Workout CRUD
        Route::post('/custom-plan', 'storeCustomPlan')->name('custom-plan.store');
        Route::delete('/plans/{plan}', 'destroyPlan')->name('plan.destroy');
        Route::post('/weeks/{week}/workouts', 'storeWorkout')->name('workout.store');
        Route::patch('/workouts/{workout}', 'updateWorkout')->name('workout.update');
        Route::delete('/workouts/{workout}', 'destroyWorkout')->name('workout.destroy');
        Route::patch('/workouts/{workout}/status', 'updateWorkoutStatus')->name('workout.status');
    });

    Route::controller(\App\Http\Controllers\BuildController::class)->prefix('projects')->name('build.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/project', 'storeProject')->name('project.store');
        Route::get('/{project}', 'show')->name('project.show');
        Route::post('/task', 'storeTask')->name('task.store');
        Route::patch('/tasks/{task}', 'updateTask')->name('task.update');
        Route::delete('/tasks/{task}', 'destroyTask')->name('task.destroy');
        Route::post('/seed-blueprint', 'seedBlueprint')->name('seed-blueprint');
    });

    Route::controller(\App\Http\Controllers\LearnController::class)->prefix('learn')->name('learn.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/resources', 'storeResource')->name('resource.store');
        Route::post('/sessions', 'storeSession')->name('session.store');
        Route::post('/resources/{resource}/complete', 'confirmCompletion')->name('resource.complete');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
