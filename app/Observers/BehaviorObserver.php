<?php 
namespace App\Observers;

use App\Models\{Behavior, Place, Order, OrderDetail, User};
use GatewayWorker\Lib\Gateway;

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
					$status = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->contains(function ($value, $key) {
					    return $value['status'] = 4;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 4]);
					}
				}

				$store_id = (User::find($behavior->user_id))->store_id;
				$count = OrderDetail::where('store_id', $store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
				break;
			// 做菜
			case 'cooking':
				$order_detail = $behavior->order_detail;
				// 判断是否属于套餐内的单品
				if ($order_detail->pid) {
					// 获取套餐内单品状态
					$status = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->contains(function ($value, $key) {
					    return $value['status'] >= 2;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 2]);
					}
				}
				// 修改菜品状态
				$order_detail->update(['status' => 2]);
				// 获取原订单信息
				$order = $order_detail->order;
				// 完成个数 == 最终个数
				if ((int)$order->finish_number + 1 == (int)$order->final_number) {
					$order->update([
						'finish_number' => (int)$order->finish_number + 1,
						'status' => 1 
					]);
				} else {
					// 判断是否属于套餐内的单品
					if ($order_detail->pid) {
						if ($status) {
							// 套餐内的单品全部完成后才 +1
							$order->increment('finish_number');
						}
					} else {
						$order->increment('finish_number');
					}
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
					$status = OrderDetail::where('pid', $order_detail->pid)->select('status')->get()->contains(function ($value, $key) {
					    return $value['status'] = 5;
					});
					if ($status) {
						// 修改套餐状态
						OrderDetail::where('id', $order_detail->pid)->update(['status' => 5]);
					}
				}

				$store_id = (User::find($behavior->user_id))->store_id;
				$count = OrderDetail::where('store_id', $store_id)->where('category', 'm')->where('status', 0)->selectRaw('count(*) as value')->get()->toArray();
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！', 'count' => $count[0]['value']), JSON_UNESCAPED_UNICODE));
				break;
		}
	}
} 