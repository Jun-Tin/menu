<?php 
namespace App\Observers;

use App\Models\{Behavior, Place, Order, OrderDetail};

class BehaviorObserver
{
	public function updated(Behavior $behavior)
	{
		// 获取原订单数据
		switch ($behavior->category) {
			case 'clean':
				$order = Order::find($behavior->target_id);
				// 更新原订单已完成打扫
				Order::where('id',$order->id)->update(['finish'=>1]);
				// 修改座位状态--无人状态
				Place::where('id',$order->place_id)->update(['status'=>0]);
				break;
			case 'serving':
				OrderDetail::where('id',$behavior->target_id)->update(['status'=>4]);
				break;
		}
	}
} 