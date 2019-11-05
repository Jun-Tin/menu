<?php 
namespace App\Observers;

use App\Models\{Behavior, Place, Order, OrderDetail, User};
use GatewayWorker\Lib\Gateway;

class BehaviorObserver
{
	public function updated(Behavior $behavior)
	{
		// 获取原订单数据
		switch ($behavior->category) {
			case 'clean':
				$order = Order::find($behavior->target_id);
				// 更新原订单已完成打扫
				Order::where('id',$order->id)->update(['finish'=>2]);
				// 修改座位状态--无人状态
				Place::where('id',$order->place_id)->update(['status'=>0]);
				break;
			case 'serving':
				OrderDetail::where('id',$behavior->target_id)->update(['status'=>4]);
				break;
			case 'cooking':
				OrderDetail::where('id',$behavior->target_id)->update(['status'=>2]);
				// 获取原订单号
				$order = (OrderDetail::find($behavior->target_id))->order_order;
				// 
				$data = Order::where('order',$order)->find();
				if ($data->finish_number + 1 == $data->final_number) {
					Order::where('order',$order)->update([
						'finish_number' => $data->finish_number + 1,
						'status' => 1 
					]);
				} else {
					Order::where('order',$order)->increment('finish_number');
				}

				$store_id = (User::find($behavior->user_id))->store_id;
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type'=>'serving','message'=>'上菜了！'), JSON_UNESCAPED_UNICODE));
				break;
		}
	}
} 