<?php 
namespace App\Observers;

use App\Models\{Store, Tag, Business};

class StoreObserver
{
	public function saved(Store $store)
	{
		//  分类
		$class = array('推荐', '前菜', '主食', '酒水/饮料');
		
		// 插入默认值
		foreach ($class as $key => $value) {
			// 获取软删除与正常一起的记录
			Tag::updateOrCreate([
				'pid' => 0,
				'store_id' => $store->id,
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
			$tag[$key] = Tag::updateOrCreate([
				'pid' => 0,
				'store_id' => $store->id,
				'name' => $key,
				'category' => 'perfer',
			]);
			foreach ($value as $k => $v) {
				Tag::updateOrCreate([
					'pid' => $tag[$key]->id,
					'store_id' => $store->id,
					'name' => $v,
					'category' => 'perfer',
				]);
			}
		}

		// 添加默认早中晚市
		for ($i=1; $i <= 3; $i++) { 
			Business::updateOrCreate([
				'store_id' => $store->id,
				'category' => $i,
			]);
		}
	}
} 