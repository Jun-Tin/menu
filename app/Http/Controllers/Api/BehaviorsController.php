<?php

namespace App\Http\Controllers\Api;

use App\Models\{Behavior, Place, Order, OrderDetail};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BehaviorResource;
use GatewayWorker\Lib\Gateway;

class BehaviorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request, Behavior $behavior)
    {
        // 避免重复创建
        $first = $behavior->where('target_id',$request->target_id)->where('category',$request->category)->first();
        if ($first) {
            return response()->json(['error' => ['message' => ['非法操作！']], 'status' => 404]);
        }
        
        $behavior->fill($request->all());
        $behavior->user_id = auth()->id();
        $behavior->status = 0;

        switch ($request->category) {
            // 清洁座位
            case 'clean':
                $order = Order::find($request->target_id);
                // 修改座位状态--打扫状态
                Place::where('id',$order->place_id)->update(['status'=>2]);
                break;
            // 上菜
            case 'serving':
                // 修改订单菜品状态--上菜状态
                OrderDetail::where('id',$request->target_id)->update(['status'=>3]);
                break;
            // 退菜
            case 'retreat':
                // 修改状态
                Behavior::where('id',$behavior->id)->update(['status'=>1]);
                // 修改订单菜品状态--退菜状态
                OrderDetail::where('id',$request->target_id)->update(['status'=>5]);
                // 修改原订单价格，数量
                $OrderDetail = OrderDetail::where('id',$request->target_id)->first();
                $order = Order::where('order',$OrderDetail->order_order)->first();
                if ($order->status == 3) {
                    return response()->json(['error' => ['message' => ['非法操作，订单已取消！']], 'status' => 201]);
                }
                if ($order->final_price - $OrderDetail->price == 0) {
                    $order->status = 3;
                }

                $order->final_price = $order->final_price - $OrderDetail->price;
                $order->final_number = $order->final_number - 1;
                $order->save();

                $store_id = (User::find($behavior->user_id))->store_id;
                // Gateway::sendToGroup('chef_'.$store_id, json_encode(array('type'=>'retreat','message'=>'退菜了！'), JSON_UNESCAPED_UNICODE));
                break;
            // 做菜
            case 'cooking':
                // 修改订单菜品状态--做菜状态
                OrderDetail::where('id',$request->target_id)->update(['status'=>1]);
                break;
            // 撤销
            case 'backout':
                // 修改菜单内容状态--撤销状态
                OrderDetail::where('id',$request->target_id)->update(['status'=>0]);
                $behavior->status = 1;
                break;
        }
        $behavior->save();

        return (new BehaviorResource($behavior))->additional(['status' => 200]);
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
    public function update(Request $request, Behavior $behavior)
    {
        if (auth()->id() == $behavior->user_id) {
            $behavior->update(['status'=>1]);

            return (new BehaviorResource($behavior))->additional(['status' => 200, 'message' => '修改成功！']);
        }
        return response()->json(['error' => ['message' => ['非法操作！']], 'status' => 404]);
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
}
