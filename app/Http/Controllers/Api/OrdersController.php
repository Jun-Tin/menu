<?php

namespace App\Http\Controllers\Api;

use App\Models\{Order, Menu, Tag, Behavior, Store};
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
        $orders = $order->orders;
        $order->set_time = (Store::find($order->store_id))->set_time;
        $order->details = $orders->map(function ($item, $key){
            $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
            $item->category = (Menu::find($item->menu_id, ['category']))->category;

            if ($item->menus_id) {
                $item->menus_name = Menu::find(json_decode($item->menus_id))->pluck('name');
            }

            if ($item->tags_id) {
                foreach (json_decode($item->tags_id) as $k => $value) {
                    $name[] = Tag::find($value)->pluck('name');
                }
                $item->tags_name = $name;
            }
            $item->fill_price = json_decode($item->fill_price);
            $item->remark = json_decode($item->remark);

            return $item;
        });
        $order->clean = Behavior::where('target_id',$request->order->id)->where('category','clean')->whereDate('created_at',date('Y-m-d'))->orderBy('created_at','desc')->first();

        return (new OrderResource($order))->additional(['status'=>200]);
    }

    /** 【 客户端--订单详情 】 */ 
    public function customerIndex(Request $request, Order $order)
    {
        $orders = $order->orders;
        $order->set_time = (Store::find($order->store_id))->set_time;
        $order->details = $orders->map(function ($item, $key){
            $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
            $item->category = (Menu::find($item->menu_id, ['category']))->category;

            if ($item->menus_id) {
                $item->menus_name = Menu::find(json_decode($item->menus_id))->pluck('name');
            }

            if ($item->tags_id) {
                foreach (json_decode($item->tags_id) as $k => $value) {
                    $name[] = Tag::find($value)->pluck('name');
                }
                $item->tags_name = $name;
            }
            $item->fill_price = json_decode($item->fill_price);
            $item->remark = json_decode($item->remark);

            return $item;
        });

        return (new OrderResource($order))->additional(['status'=>200]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $order->update(['status'=>2]);
        // 记录员工表现
        Behavior::create([
            'user_id' => auth()->id(),
            'target_id' => $order->id,
            'category' => 'settle',
            'status' => 1,
        ]);

        return response()->json(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /** 【 所有未完成订单 】 */
    public function orders(Request $request)
    {
        // 制作时间
        $set_time = (Store::find(auth()->user()->store_id))->set_time;
        $order = Order::where('store_id',auth()->user()->store_id)->whereDate('created_at',date('Y-m-d'))->where('status',0)->where('finish',0)->get();
        $order = $order->map(function ($item, $key){
            $item->details = $item->orders;
            return $item->only(['details']);
        });
        // 合并成一维数组
        $data = $order->flatten();
        $data->map(function ($item, $key){
            $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
            $item->category = (Menu::find($item->menu_id, ['category']))->category;

            if ($item->menus_id) {
                $item->menus_name = Menu::find(json_decode($item->menus_id))->pluck('name');
            }

            if ($item->tags_id) {
                foreach (json_decode($item->tags_id) as $k => $value) {
                    $name[] = Tag::find($value)->pluck('name');
                }
                $item->tags_name = $name;
            }
            $item->fill_price = json_decode($item->fill_price);
            $item->remark = json_decode($item->remark);

            return $item;
        });

        return response()->json(['data'=>$data->all(), 'status'=>200, 'set_time'=>$set_time]);
    } 
}
