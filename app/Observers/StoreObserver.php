<?php 
namespace App\Observers;

use App\Models\{Store, Tag, Area, StoreArea, User};
use Illuminate\Support\Facades\{Auth, Storage, File, Crypt, Redis};
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StoreObserver
{
	public function created(Store $store)
	{
		// switch ($store->category) {
		// 	case 0:
				// 创建默认厨师账号
				$user = User::create([
					'name' => '后厨管理员',
					'store_id' => $store->id,
					'account' => str_pad(random_int(1, 99999999), 8, 0, STR_PAD_LEFT),
					'gender' => 1,
					'post' => 'chef',
					'password' => bcrypt('secret'),
					'pro_password' => 'secret',
				]);

				$encrypted = substr(Crypt::encryptString($user->account.'_'.$user->id.'_code'), 0, 15);
		        $filename = $user->account . '.png';
		        $dir = public_path('images/qrcodes/'.$store->id.'/user');
		        if (!is_dir($dir)) {
		            File::makeDirectory($dir, 0777, true);
		        }
		        $link = env('APP_CHEF').$user->id.'/'.$encrypted;
		        $qrcode = env('APP_URL').'/images/qrcodes/'. $store->id. '/user/'. $filename;
		        // 判断图片是否存在
		        if (file_exists($dir. '/' .$filename)) {
		            unlink($dir. '/' .$filename);
		        }
		        // 保存二维码
		        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($link, $dir. '/'. $filename);
		        // 设置redis缓存
		        Redis::set($user->account.'_'.$store->id.'_'.$user->id, $encrypted);
		        $user->update([
		        	'qrcode' => $qrcode,
					'link' => $link,
		        ]);
			// }
			// public function saved(Store $store)
			// {
				//  分类
				$class = array('推荐', '前菜', '主食', '酒水/饮料');
				
				// 插入默认值
				foreach ($class as $key => $value) {
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
						'section_left' => 13,
						'section_right' => NULL,
						'section_number' => NULL,
						'sign' => 'D',
						'show' => '13-'
					]
				];

				foreach ($area as $key => $value) {
					Area::updateOrCreate([
						'store_id' => $value['store_id'],
						'name' => $value['name'],
						'section_left' => $value['section_left'],
						'section_right' => $value['section_right'],
						'section_number' => $value['section_number'],
						'sign' => $value['sign'],
						'show' => $value['show']
					]);
				}

				$dir = public_path('images/qrcodes/'. $store->id. '/screen/');
		        if (!is_dir($dir)) {
		            File::makeDirectory($dir, 0777, true);
		        }

		        $data = [
		        	[
		        		'filename' => 'screen.png',
		        		'type' => 'screen',
		        		'link' => env('APP_SCREEN')
		        	],
		        	[
		        		'filename' => 'line.png',
		        		'type' => 'line',
		        		'link' => env('APP_LINE')
		        	],
		        	[
		        		'filename' => 'book.png',
		        		'type' => 'book',
		        		'link' => env('APP_BOOK')
		        	]
		        ];

		        for ($i=0; $i < count($data); $i++) { 
		        	$code[$i] = substr(Crypt::encryptString($store->name. '_'.$data[$i]['type'].'_'. $store->id. '_'. $store->id. '_code'), 0, 15);
		        	// 判断图片是否存在
			        if (file_exists($dir. '/'. $data[$i]['filename'])) {
			            unlink($dir. '/'. $data[$i]['filename']);
			        }
			        // 保存二维码
			        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($data[$i]['link']. $store->id. '/screen/'. $code[$i], $dir. '/'. $data[$i]['filename']);
			        // 设置redis缓存
			    	Redis::set($store->name. '_'.$data[$i]['type'].'_'. $store->id. '_'. $store->id, $code[$i]);

		        }

		        StoreArea::updateOrCreate([
		        	'store_id' => $store->id,
		        	'screen_link' => env('APP_SCREEN'). $store->id. '/screen/'. $code[0],
		        	'screen_qrcode' => env('APP_URL').'/images/qrcodes/'. $store->id. '/screen/'. $data[0]['filename'],
		        	'line_qrcode' => env('APP_URL').'/images/qrcodes/'. $store->id. '/screen/'. $data[1]['filename'],
		        	'book_qrcode' => env('APP_BOOK').'images/qrcodes/'. $store->id. '/screen/'. $data[2]['filename'],
		        ]);
				
				// break;

			// case 1:
			// 	dd($store);
			// 	break;
		// }
	}
} 