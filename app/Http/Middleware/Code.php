<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Place;
use Illuminate\Support\Facades\Redis;


class Code
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 获取座位名称
        $place = Place::find($request->header('id'));
        // 执行动作，判断该值是否存在redis数组
        if (Redis::get($place->name.'_'.$place->id) != $request->header('code')) {
            return response()->json(['error' => ['message' => ['非法登入！']], 'status' => 404]);
        }
        return $next($request);
    }
}
