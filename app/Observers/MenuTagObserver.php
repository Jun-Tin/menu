<?php 
namespace App\Observers;

use App\Models\MenuTag;

class MenuTagObserver
{
	public function created(MenuTag $menutag)
	{
		dd(123123);
		// 更新标签排序号
		$menutag->update(['order_number' => $menutag->id]);
	}
} 