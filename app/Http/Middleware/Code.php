<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\{Place, User, Store};
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
        if ($request->header('placeid') || $request->header('userid') || $request->header('storeid') && $request->header('code')) {
            if ($request->header('placeid')) {
                // 获取座位
                $place = Place::find($request->header('placeid'));
                $name = $place->name;
                $id = $place->id;
                $store_id = $place->store_id;
            }

            if ($request->header('userid')) {
                // 获取用户
                $user = User::find($request->header('userid'));
                $name = $user->account;
                $id = $user->id;
                $store_id = $user->store_id;
            }

            if ($request->header('storeid') && $request->header('category')) {
                // 获取门店
                $store = Store::find($request->header('storeid'));
                $name = 'store_'.$request->header('category');
                // switch ($request->header('category')) {
                //     case 'screen':
                //         $name = $store->name.'_screen';
                //         break;
                //     case 'line':
                //         $name = $store->name.'_line';
                //         break;
                //     case 'book':
                //         $name = $store->name.'_book';
                //         break;
                // }
                $id = $store->id;
                $store_id = $store->id;
            }
            // 执行动作，判断该值是否存在redis数组
            if (substr(Redis::get($name.'_'.$store_id.'_'.$id), 0, 20) != $request->header('code')) {
                return response()->json(['error' => ['message' => [__('messages.illegal')]], 'status' => 404]);
            }
        } else {
            return response()->json(['error' => ['message' => [__('messages.illegal')]], 'status' => 404]);
        }
        // 检查门店是否上线状态
        $active = Store::where('id', $store_id)->value('active');
        if (!$active) {
            return response()->json(['error' => ['message' => [__('messages.offline')]], 'status' => 404]);
        }
        return $next($request);
    }
}
