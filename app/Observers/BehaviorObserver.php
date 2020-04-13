<?php 
namespace App\Observers;

use App\Models\{Behavior, OrderDetail, User};
use GatewayWorker\Lib\Gateway;
use Carbon\Carbon;

class BehaviorObserver
{
	public function updated(Behavior $behavior)
	{
		switch ($behavior->category) {
			// 上菜
			case 'serving':
				$order_detail = $behavior->order_detail;
				// 修改菜品状态
				$order_detail->update(['status' => 4]);
				// 判断是否属于套餐内的单品
				if ($order_detail->pid) {
					// 获取套餐内单品状态
					$collection = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->map(function ($item){
						return $item->status;
					})->flatten();

					$status = $collection->every(function ($value, $key) {
					    return $value >= 4;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 4]);
					}
				}

				// 检测订单菜品是否全部上完
				$data = OrderDetail::where('order_order', $order_detail->order)->where('status', '<>', 5)->select('status')->get()->map(function ($item){
					return $item->status;
				})->flatten();
				$all = $data->every(function ($value, $key) {
				    return $value >= 4;
				});
				if ($all) {
					// 修改订单状态为已上完
					$order_detail->order->update(['state' => 2]);
				}

				$store_id = (User::find($behavior->user_id))->store_id;
				$count = OrderDetail::where('store_id', $store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
				break;
			// 做菜
			case 'cooking':
				$order_detail = $behavior->order_detail;
				// 修改菜品状态
				$order_detail->update(['status' => 2, 'finished_at' => Carbon::now()]);
				// 判断是否属于套餐内的单品
				if ($order_detail->pid) {
					// 获取套餐内单品状态
					$collection = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->map(function ($item){
						return $item->status;
					})->flatten();

					$status = $collection->every(function ($value, $key) {
					    return $value >= 2;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 2, 'finished_at' => Carbon::now()]);
					}
				}
				// 获取原订单信息
				$order = $order_detail->order;
				// 判断是否属于套餐内的单品
				if ($order_detail->pid) {
					if ($status) {
						// 套餐内的单品全部完成后才 +1
						$order->increment('finish_number');
					}
				} else {
					$order->increment('finish_number');
				}
				// 完成个数 == 最终个数
				if ($order->finish_number == $order->final_number) {
					$order->update(['state' => 1 ]);
				} 

                // 将原先撤销的记录删除
                Behavior::where('target_id', $behavior->target_id)->where('category', 'backout')->delete();

				$store_id = (User::find($behavior->user_id))->store_id;
				$count = OrderDetail::where('store_id', $store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type' => 'serving', 'message' => '上菜了！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
				break;
			// 退菜
			case 'retreat':
				$order_detail = $behavior->order_detail;
				// 判断是否属于套餐内的单品
				if ($order_detail->pid) {
					$collection = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->map(function ($item){
						return $item->status;
					})->flatten();

					$status = $collection->every(function ($value, $key) {
					    return $value = 5;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 5]);
					}
				}
				$order_detail->update(['status' => 5]);

				$store_id = (User::find($behavior->user_id))->store_id;
				$count = OrderDetail::where('store_id', $store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
				break;
		}	
	}
} 