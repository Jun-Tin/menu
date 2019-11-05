<?php

namespace App\Http\Controllers\Api;

use App\Models\{Order, Menu, Tag, Behavior, Store, OrderDetail, Place};
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
        $order->package = $order->orders()->where('pid',0)->get();
        $order->set_time = Store::where('id',$order->store_id)->value('set_time');
        $order->package->map(function ($item, $key){
            $item->menu_name = Menu::where('id',$item->menu_id, ['name'])->value('name');
            $item->category = Menu::where('id',$item->menu_id, ['category'])->value('category');
            $item->details = $item->where('pid',$item->id)->get()->map(function ($item, $key){
                if ($item->menus_id) {
                    $item->menus_name = Menu::where('id',$item->menus_id)->value('name');
                }

                if (!empty(json_decode($item->tags_id,true))) {
                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                        $name[] = Tag::where('id',$value)->value('name');
                    }
                    $item->tags_name = $name;
                }
                return $item;
            });
            return $item;
        });

        $order->clean = Behavior::where('target_id',$request->order->id)->where('category','clean')->whereDate('created_at',date('Y-m-d'))->orderBy('created_at','desc')->first();

        return (new OrderResource($order))->additional(['status'=>200]);
    }

    /** 【 客户端--订单详情 】 */ 
    public function customerIndex(Request $request, Order $order)
    {
        $order->package = $order->orders()->where('pid',0)->get();
        $order->set_time = Store::where('id',$order->store_id)->value('set_time');
        $order->package->map(function ($item, $key){
            $item->menu_name = Menu::where('id',$item->menu_id, ['name'])->value('name');
            $item->category = Menu::where('id',$item->menu_id, ['category'])->value('category');
            $item->details = $item->where('pid',$item->id)->get()->map(function ($item, $key){
                if ($item->menus_id) {
                    $item->menus_name = Menu::where('id',$item->menus_id)->value('name');
                }

                if (!empty(json_decode($item->tags_id,true))) {
                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                        $name[] = Tag::where('id',$value)->value('name');
                    }
                    $item->tags_name = $name;
                }
                return $item;
            });
            return $item;
        });

        $order->clean = Behavior::where('target_id',$request->order->id)->where('category','clean')->whereDate('created_at',date('Y-m-d'))->orderBy('created_at','desc')->first();

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
        // $order = Order::where('store_id',auth()->user()->store_id)->where('status',0)->where('finish',0)->get();
        $order->unfinished = $order->map(function ($item, $key) use ($request){
            // 未完成的菜品
            return $item->orders()->where('status',0)->where('category','m')->get();
        });
        $order->finished = $order->map(function ($item, $key) use ($request){
            // 已完成的菜品
            return $item->orders()->where('status',2)->where('category','m')->get();
        });
        $order->behavior = $order->map(function ($item, $key){
            // 正在做的菜品
            return $item->orders()->where('status',1)->where('category','m')->get();
        });

        // 合并成一维数组（未完成菜品）
        $data['unfinished'] = $order->unfinished->flatten()->map(function ($item, $key){
            $item->place_name = Place::where('id',$item->place_id)->value('name');
            if ($item->pid) {
                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
            } else{
                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
            }

            if (!empty(json_decode($item->tags_id,true))) {
                foreach (json_decode($item->tags_id,true) as $k => $value) {
                    $name[] = Tag::where('id',$value)->value('name');
                }
                $item->tags_name = $name;
            }
            $item->remark = $item->remark;
            return $item;
        });

        // 合并成一维数组（已完成菜品）
        $data['finished'] = $order->finished->flatten()->map(function ($item, $key){
            $item->place_name = Place::where('id',$item->place_id)->value('name');
            if ($item->pid) {
                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
            } else{
                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
            }

            if (!empty(json_decode($item->tags_id,true))) {
                foreach (json_decode($item->tags_id,true) as $k => $value) {
                    $name[] = Tag::where('id',$value)->value('name');
                }
                $item->tags_name = $name;
            }
            $item->remark = $item->remark;
            return $item;
        });

        // 合并成一维数组（正在做菜品）
        $data['myself'] = $order->behavior->flatten()->map(function ($item, $key){
            $behavior = Behavior::where('target_id',$item->id)->where('category','cooking')->first();
            if ($behavior->user_id == auth()->id()) {
                $item->place_name = Place::where('id',$item->place_id)->value('name');
                if ($item->pid) {
                    $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                } else{
                    $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                }

                if (!empty(json_decode($item->tags_id,true))) {
                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                        $name[] = Tag::where('id',$value)->value('name');
                    }
                    $item->tags_name = $name;
                }
                $item->remark = $item->remark;
                $item->behavior = $behavior;
                return $item;
            }
        });

        return response()->json(['data'=>$data, 'status'=>200, 'set_time'=>$set_time]);
    } 

    /** 【 送菜列表 】 */
    public function serving(Request $request)
    {
        // 制作时间
        $set_time = (Store::find(auth()->user()->store_id))->set_time;
        $order = Order::where('store_id',auth()->user()->store_id)->whereDate('created_at',date('Y-m-d'))->where('status',0)->where('finish',0)->get();
        // $order = Order::where('store_id',auth()->user()->store_id)->where('status',0)->where('finish',0)->get();
        $order->finished = $order->map(function ($item, $key) use ($request){
            // 已完成的菜品
            return $item->orders()->where('status',2)->where('category','m')->get();
        });
        $order->behavior = $order->map(function ($item, $key){
            // 正在送的菜品
            return $item->orders()->where('status',3)->where('category','m')->get();
        });

        // 合并成一维数组（已完成菜品）
        $data['finished'] = $order->finished->flatten()->map(function ($item, $key){
            $item->place_name = Place::where('id',$item->place_id)->value('name');
            if ($item->pid) {
                $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
            } else{
                $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
            }

            if (!empty(json_decode($item->tags_id,true))) {
                foreach (json_decode($item->tags_id,true) as $k => $value) {
                    $name[] = Tag::where('id',$value)->value('name');
                }
                $item->tags_name = $name;
            }
            $item->remark = $item->remark;
            return $item;
        });

        // 合并成一维数组（正在送菜品）
        $data['myself'] = $order->behavior->flatten()->map(function ($item, $key){
            $behavior = Behavior::where('target_id',$item->id)->where('category','serving')->first();
            if ($behavior->user_id == auth()->id()) {
                $item->place_name = Place::where('id',$item->place_id)->value('name');
                if ($item->pid) {
                    $item->menu_name = Menu::where('id',$item->menus_id)->value('name');
                } else{
                    $item->menu_name = Menu::where('id',$item->menu_id)->value('name');
                }

                if (!empty(json_decode($item->tags_id,true))) {
                    foreach (json_decode($item->tags_id,true) as $k => $value) {
                        $name[] = Tag::where('id',$value)->value('name');
                    }
                    $item->tags_name = $name;
                }
                $item->remark = $item->remark;
                $item->behavior = $behavior;
                return $item;
            }
        });

        return response()->json(['data'=>$data, 'status'=>200, 'set_time'=>$set_time]);
    } 
}
