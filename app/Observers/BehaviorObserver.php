<?php 
namespace App\Observers;

use App\Models\{Behavior, Place, Order, OrderDetail, User};
use GatewayWorker\Lib\Gateway;

class BehaviorObserver
{
	public function updated(Behavior $behavior)
	{
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

				$store_id = (User::find($behavior->user_id))->store_id;
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type'=>'update serving','message'=>'更新上菜消息！'), JSON_UNESCAPED_UNICODE));
				break;
			case 'cooking':
				$OrderDetail = OrderDetail::find($behavior->target_id);
				// 判断是否属于套餐内的单品
				if ($OrderDetail->pid) {
					// 获取套餐内单品状态
					$all = OrderDetail::where('pid',$OrderDetail->pid)->get();
					if ($all->min('status') != 0) {
						// 修改套餐状态
						OrderDetail::where('id',$OrderDetail->pid)->update(['status'=>2]);
					}
				}
				// 修改菜品状态
				$OrderDetail->update(['status'=>2]);
				// 获取原订单号
				$order = OrderDetail::where('id',$behavior->target_id)->value('order_order');
				// 获取原订单信息
				$data = Order::where('order',$order)->first();
				// 完成个数 == 最终个数
				if ($data->finish_number + 1 == $data->final_number) {
					Order::where('order',$order)->update([
						'finish_number' => $data->finish_number + 1,
						'status' => 1 
					]);
				} else {
					Order::where('order',$order)->increment('finish_number');
				}

                // 将原先撤销的记录删除
                Behavior::where('target_id',$behavior->target_id)->where('category','backout')->delete();

				$store_id = (User::find($behavior->user_id))->store_id;
				Gateway::sendToGroup('waiter_'.$store_id, json_encode(array('type'=>'serving','message'=>'上菜了！'), JSON_UNESCAPED_UNICODE));
				break;
		}
	}
} 