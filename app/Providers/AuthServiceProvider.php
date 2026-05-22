<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\Review;
use App\Policies\GamePolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Game::class => GamePolicy::class,
        Review::class => ReviewPolicy::class,
    ];

    public function boot(): void
    {
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);
    }
}
