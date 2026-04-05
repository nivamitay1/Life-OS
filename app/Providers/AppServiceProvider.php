<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Omnibar\Commands\Learn\DraftLearnSessionCommand;
use App\Services\Omnibar\Commands\Money\LogExpenseCommand;
use App\Services\Omnibar\Commands\Money\LogIncomeCommand;
use App\Services\Omnibar\Commands\Navigation\NavigateCommand;
use App\Services\Omnibar\Commands\Running\DraftRunCommand;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->tag([
            NavigateCommand::class,
            LogExpenseCommand::class,
            LogIncomeCommand::class,
            DraftRunCommand::class,
            DraftLearnSessionCommand::class,
        ], 'omnibar.commands');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Vite::prefetch(concurrency: 3);
        \Illuminate\Database\Eloquent\Model::unguard();
    }
}
