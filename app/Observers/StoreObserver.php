<?php 
namespace App\Observers;

use App\Models\{Store, Tag, Area};

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

		// 创建入座区域
		$area = [
			[
				'store_id' => $store->id,
				'name' => 'A',
				'section_left' => 1,
				'section_right' => 4,
				'section_number' => 4,
				'sign' => 'A',
				'show' => '1-4'
			],
			[
				'store_id' => $store->id,
				'name' => 'B',
				'section_left' => 5,
				'section_right' => 8,
				'section_number' => 4,
				'sign' => 'B',
				'show' => '5-8'
			],
			[
				'store_id' => $store->id,
				'name' => 'C',
				'section_left' => 9,
				'section_right' => 12,
				'section_number' => 4,
				'sign' => 'C',
				'show' => '9-12'
			],
			[
				'store_id' => $store->id,
				'name' => 'D',
				'section_left' => 12,
				'section_right' => NULL,
				'section_number' => NULL,
				'sign' => 'D',
				'show' => '12-'
			]
		];

		foreach ($area as $key => $value) {
			Area::create([
				'store_id' => $value['store_id'],
				'name' => $value['name'],
				'section_left' => $value['section_left'],
				'section_right' => $value['section_right'],
				'section_number' => $value['section_number'],
				'sign' => $value['sign'],
				'show' => $value['show']
			]);
		}
	}
} 