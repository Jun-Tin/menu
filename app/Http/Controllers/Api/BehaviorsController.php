<?php

namespace App\Http\Controllers\Api;

use App\Models\{Behavior, OrderDetail};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BehaviorResource;
use GatewayWorker\Lib\Gateway;
use Carbon\Carbon;

class BehaviorsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Behavior $behavior)
    {
        // dd(Carbon::parse('2020-01-18')->dayOfWeek);
        $user = auth()->user();
        // 避免重复创建
        // $first = $behavior->where('target_id', $request->target_id)->where('category', $request->category)->first();
        // if ($first && $first->category != 'clean') {
        //     return response()->json(['error' => ['message' => ['非法操作！']], 'status' => 404]);
        // }
        
        $behavior->fill($request->all());
        $behavior->user_id = $user->id;
        $behavior->status = 0;

        switch ($request->category) {
            // 清洁座位
            case 'clean':
                $behavior->order->update(['finish' => 1]);
                // 查询门店设置清理桌子状态的规则
                $store = $user->store;
                // 判断规则
                if ($store->clean && !$store->settle) {
                    // 恢复桌子状态 -- 无人
                    $behavior->order->place->update(['status' => 0]);
                } else {
                    if ($behavior->order->status == 2) {
                        // 恢复桌子状态 -- 无人
                        $behavior->order->place->update(['status' => 0]);
                    }
                }
                $behavior->status = 1;
                break;
            // 上菜
            case 'serving':
                // 修改订单菜品状态 -- 上菜状态
                $behavior->order_detail->update(['status' => 3]);
                $count = OrderDetail::where('store_id', $user->store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
                Gateway::sendToGroup('waiter_'.$user->store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
                break;
            // 退菜
            case 'retreat':
                $behavior->status = 1;
                // 修改订单菜品状态 -- 退菜状态
                $behavior->order_detail->update(['status' => 5]);
                // 修改原订单价格，数量
                $order = $behavior->order_detail->order;
                if ($order->status == 3) {
                    return response()->json(['error' => ['message' => [__('messages.illegals')]], 'status' => 201]);
                }
                if ($order->final_price - $behavior->order_detail->price == 0) {
                    $order->status = 3;
                    // 桌子恢复状态 -- 无人
                    $order->place->update(['status' => 0]);
                }

                // 判断是否属于套餐内的单品
                if ($behavior->order_detail->pid) {
                    $collection = OrderDetail::where('pid', $behavior->order_detail->pid)->select('status')->get()->map(function ($item){
                        return $item->status;
                    })->flatten();

                    $status = $collection->every(function ($value, $key) {
                        return $value = 5;
                    });
                    if ($status) {
                        // 修改套餐状态
                        OrderDetail::where('id', $behavior->order_detail->pid)->update(['status' => 5]);
                        // 套餐数量 -1
                        $order->final_number = $order->final_number - 1;
                    }

                    $order->final_price = $order->final_price - OrderDetail::where('pid', $behavior->order_detail->pid)->value('price');
                } else {
                    $order->final_price = $order->final_price - $behavior->order_detail->price;
                    $order->final_number = $order->final_number - 1;
                }
                
                $order->save();

                Gateway::sendToGroup('chef_'.$user->store_id, json_encode(array('type' => 'retreat', 'message' => '退菜了！'), JSON_UNESCAPED_UNICODE));
                break;
            // 做菜
            case 'cooking':
                $order_detail = $behavior->order_detail;
                // 判断是否是套餐内的单品
                if ($order_detail->pid) {
                    // 修改套餐状态正在做
                    OrderDetail::where('id', $order_detail->pid)->update(['status' => 1]);
                }
                // 修改订单菜品状态--做菜状态
                $order_detail->update(['status' => 1]);
                break;
            // 撤销
            case 'backout':
                $order_detail = $behavior->order_detail;
                // 判断是否是套餐内的单品
                if ($order_detail->pid) {               
                    // 修改套餐状态
                    OrderDetail::where('id', $order_detail->pid)->update(['status' => 0]);
                }
                // 修改菜单内容状态 -- 撤销状态
                $order_detail->update(['status' => 0]);
                // 订单完成数量 -1
                $order_detail->order->decrement('finish_number');
                // 将原先制作的记录删除
                Behavior::where('target_id', $request->target_id)->where('category', 'cooking')->delete();
                $behavior->status = 1;
                break;
            // 结账
            case 'settle':
                // 修改原订单状态 -- 已支付
                $behavior->order->update(['status' => 2, 'payment_method' => $request->payment_method, 'paid_at' => Carbon::now()]);
                // 查询门店设置清理桌子状态的规则
                $store = $user->store;
                // 判断规则
                if (!$store->clean && $store->settle) {
                    // 恢复桌子状态 -- 无人
                    $behavior->order->place->update(['status' => 0]);
                } else {
                    if ($behavior->order->finish) {
                        // 恢复桌子状态 -- 无人
                        $behavior->order->place->update(['status' => 0]);
                    }
                }
                $behavior->status = 1;
                break;
        }
        $behavior->save();

        return (new BehaviorResource($behavior))->additional(['status' => 200, 'message' => __('messages.do')]);
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
            $behavior->update(['status' => 1]);

            return (new BehaviorResource($behavior))->additional(['status' => 200, 'message' => __('messages.update')]);
        }
        return response()->json(['error' => ['message' => [__('messages.illegal')]], 'status' => 404]);
    }
}
