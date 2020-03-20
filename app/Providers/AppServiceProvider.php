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
        \App\Models\Place::observe(\App\Observers\PlaceObserver::class);
        \App\Models\OrderDetail::observe(\App\Observers\OrderDetailObserver::class);
        \App\Models\Line::observe(\App\Observers\LineObserver::class);
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
