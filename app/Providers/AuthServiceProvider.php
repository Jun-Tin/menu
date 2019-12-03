<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        // accessToken有效期
        // Passport::tokensExpireIn(now()->addDays(15));
        // accessRefushToken有效期
        // Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::tokensCan([
            'place-orders' => 'Place orders',
            'waiter' => 'waiter',
            'chef' => 'chef',
            'manager' => 'manager',
            'boss' => 'boss',
        ]);
    }
}
