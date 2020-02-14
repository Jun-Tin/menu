<?php 
namespace App\Observers;

use App\Models\Line;
use GatewayWorker\Lib\Gateway;

class LineObserver
{
	public function created(Line $line)
	{
		Gateway::sendToGroup('waiter_'.$line->store_id, json_encode(array('type' => 'new_lines', 'message' => '新排队通知！', 'id' => $line->id, 'name' => $line->name, 'number' => $line->number, 'phone' => $line->phone, 'code' => $line->code, 'created_at' => $line->created_at->format('Y/m/d H:i:s')), JSON_UNESCAPED_UNICODE));
	}
} 