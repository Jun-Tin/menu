<?php

namespace App\Http\Controllers\Api;

use App\Models\{Order, Behavior, Store};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{OrderResource, OrderCollection};

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Order $order)
    {
        $order->package = $order->orders()->where('pid', 0)->get();
        $order->set_time = $order->store->set_time;
        $order->clean = Behavior::where('target_id', $request->order->id)->where('category', 'clean')->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'desc')->first();

        return (new OrderResource($order))->additional(['status' => 200]);
    }

    /** 【 客户端--订单详情 】 */ 
    public function customerIndex(Request $request, Order $order)
    {
        $order->package = $order->orders()->where('pid', 0)->get();
        $order->set_time = $order->store->set_time;

        return (new OrderResource($order))->additional(['status' => 200]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $order->update(['status' => 2]);
        // 记录员工表现
        Behavior::create([
            'user_id' => auth()->id(),
            'target_id' => $order->id,
            'category' => 'settle',
            'status' => 1,
        ]);

        return response()->json(['status' => 200, 'message' => __('messages.update')]);
    }

    /** 【 所有未完成订单 】 */
    public function orders(Request $request)
    {
        // 制作时间
        $set_time = (Store::find(auth()->user()->store_id))->set_time;
        // $order = Order::where('store_id',auth()->user()->store_id)->whereDate('created_at',date('Y-m-d'))->where('status',0)->where('finish',0)->get();
        $order = Order::where('store_id', auth()->user()->store_id)->whereIn('status', [0, 1, 2])->where('finish', 0)->get();
        $order->unfinished = $order->map(function ($item, $key){
            // 未完成的菜品
            return $item->orders()->where('status', 0)->where('category', 'm')->get();
        });
        $order->finished = $order->map(function ($item, $key){
            // 已完成的菜品
            return $item->orders()->whereIn('status', [2, 3, 4])->where('category', 'm')->get();
        });
        $order->behavior = $order->map(function ($item, $key){
            // 正在做的菜品
            return $item->orders()->where('status', 1)->where('category', 'm')->get();
        });

        $order->put('unfinished', $order->unfinished);
        $order->put('finished', $order->finished);
        $order->put('behavior', $order->behavior);

        return (new OrderCollection($order, $param='orders'))->additional(['status' => 200, 'set_time' => $set_time]);
    } 

    /** 【 送菜列表 】 */
    public function serving(Request $request)
    {
        // 制作时间
        $set_time = (Store::find(auth()->user()->store_id))->set_time;
        // $order = Order::where('store_id',auth()->user()->store_id)->whereDate('created_at',date('Y-m-d'))->where('status',0)->where('finish',0)->get();
        $order = Order::where('store_id', auth()->user()->store_id)->whereIn('status', [0, 1, 2])->where('finish', 0)->get();
        $order->finished = $order->map(function ($item, $key) use ($request){
            // 已完成的菜品
            return $item->orders()->where('status', 2)->where('category', 'm')->get();
        });
        $order->behavior = $order->map(function ($item, $key){
            // 正在送的菜品
            return $item->orders()->where('status', 3)->where('category', 'm')->get();
        });

        $order->put('finished', $order->finished);
        $order->put('behavior', $order->behavior);

        return (new OrderCollection($order, $param='serving'))->additional(['status' => 200, 'set_time' => $set_time]);
    }

    /** 【 退菜列表 】 */
    public function retreat(Request $request, Order $order)
    {
        $order->place_name = $order->place->name;
        $order->package = $order->orders()->where('status', 0)->where('pid', 0)->get();

        return (new OrderResource($order))->additional(['status' => 200]);
    }

}
