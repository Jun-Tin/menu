<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 注册观察器
        \App\Models\Store::observe(\App\Observers\StoreObserver::class);
        \App\Models\Book::observe(\App\Observers\BookObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\Behavior::observe(\App\Observers\BehaviorObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
