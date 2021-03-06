<?php 
namespace App\Observers;

use App\Models\{OrderDetail, Tag};
use GatewayWorker\Lib\Gateway;

class OrderDetailObserver
{
	public function created(OrderDetail $orderdetail)
	{
		if ($orderdetail->category != 'p') {
			if (empty(json_decode($orderdetail->tags_id,true))) {
				$remark = $orderdetail->remark;
			} else {
				$string = '';
				foreach (json_decode($orderdetail->tags_id,true) as $key => $value) {
					$string .= Tag::where('id', $value)->value('name').'、';
				}
				$remark = $string. $orderdetail->remark;
			}
			Gateway::sendToGroup('chef_'.$orderdetail->store_id, json_encode(array('type' => 'new_dishes', 'message' => '新菜品通知！', 'place_name' => $orderdetail->place->name, 'menu_name' => $orderdetail->pid? $orderdetail->packageMenu->name: $orderdetail->menu->name, 'menu_name_en' => $orderdetail->pid? $orderdetail->packageMenu->name_en: $orderdetail->menu->name_en, 'remark' => $remark, 'created_at' => $orderdetail->created_at->format('Y/m/d H:i:s')), JSON_UNESCAPED_UNICODE));
		}
	}
} 