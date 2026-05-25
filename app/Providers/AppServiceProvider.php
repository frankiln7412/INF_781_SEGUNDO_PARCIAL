<?php

namespace App\Providers;

use App\Models\Movement;
use App\Policies\MovementPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // Registrar la MovementPolicy usando Gate nativo de Laravel
    protected $policies = [
        Movement::class => MovementPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
