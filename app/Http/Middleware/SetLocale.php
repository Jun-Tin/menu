<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

use Closure;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('locale') && in_array(session()->get('locale'), ['en', 'zh_hk', 'zh_cn'])) {
            App::setLocale($request->session()->get('locale'));
        } else {
            $request->session()->put('locale', Config::get('app.fallback_locale'));
            App::setLocale(Config::get('app.fallback_locale'));
        }
        return $next($request);
    }
}
