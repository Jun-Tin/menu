<?php 
namespace App\Observers;

use App\Models\{Store, Tag};

class StoreObserver
{
	public function saved(Store $store)
	{
		//  分类
		$class = array('推荐', '前菜', '主食', '酒水/饮料');
		
		// 插入默认值
		foreach ($class as $key => $value) {
			Tag::create([
				'pid' => 0,
				'user_id' => $store->user_id,
				'name' => $value,
				'category' => 'class',
			]);
		}
		
		// 偏好
		$perfer = array(
			'口味集合' => array('清淡', '甜味', '辣味', '黑椒'),
			'规格集合' => array('大', '中', '小'),
		);
		// 插入默认值
		foreach ($perfer as $key => $value) {
			$tag[$key] = Tag::firstOrCreate([
				'pid' => 0,
				'user_id' => $store->user_id,
				'name' => $key,
				'category' => 'perfer',
			]);
			foreach ($value as $k => $v) {
				Tag::firstOrCreate([
					'pid' => $tag[$key]->id,
					'user_id' => $store->user_id,
					'name' => $v,
					'category' => 'perfer',
				]);
			}
			
		}
	}
} 