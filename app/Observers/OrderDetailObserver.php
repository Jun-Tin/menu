<?php 
namespace App\Observers;

use App\Models\OrderDetail;
use GatewayWorker\Lib\Gateway;

class OrderDetailObserver
{
	public function created(OrderDetail $orderdetail)
	{
		if ($orderdetail->category != 'p') {
			Gateway::sendToGroup('chef_'.$orderdetail->store_id, json_encode(array('type' => 'new_dishes', 'message' => __('messages.new_dishes'), 'place_name' => $orderdetail->place->name, 'menu_name' => $orderdetail->pid? $orderdetail->packageMenu->name: $orderdetail->menu->name, 'remark' => $orderdetail->remark, 'created_at' => $orderdetail->created_at->format('Y/m/d H:i:s')), JSON_UNESCAPED_UNICODE));
		}
	}
} 