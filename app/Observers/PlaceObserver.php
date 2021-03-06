<?php 
namespace App\Observers;

use App\Models\{Place, Image};
use Illuminate\Support\Facades\{Auth, Storage, File, Crypt, Redis};
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlaceObserver
{
	public function created(Place $place)
	{
		if ($place->floor != 0) {
			switch ($place->store->language->id) {
				case 1:
					$set = 'Seat';
					break;
				case 2:
					$set = '座位';
					break;
				default:
					$set = '座位';
					break;
			}
			$encrypted = substr(Crypt::encryptString('place_'.$place->id.'_'.$place->id.'_code'), 0, 15);
			$dir = public_path('images/qrcodes/'.$place->store_id. '/place/' .$place->floor);
	        if (!is_dir($dir)) {
	            File::makeDirectory($dir, 0777, true);
	        }
	        $filename = $set.$place->id. '.png';
	        // 判断图片是否存在
	        if (file_exists($dir. '/' .$filename)) {
	            unlink($dir. '/' .$filename);
	        }
	        // 保存二维码
	        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate(env('APP_CLIENT').$place->store_id.'/'.$place->id.'/'.$encrypted, $dir. '/'. $filename);
	        // 返回url链接
	        $url = env('APP_URL').'/images/qrcodes/'. $place->store_id. '/place/' .$place->floor. '/' .$filename;
	        // 保存在数据库
	        $image = Image::create([
	            'user_id' => auth()->user()->id,
	            'type' => 'qrcodes/'.$place->store_id.'/place',
	            'path' => $url,
	            'link' => env('APP_CLIENT').$place->store_id.'/'.$place->id.'/'.$encrypted,
	        ]);

			$place->update([
				'name' => $set.$place->id,
				'number' => 1,
				'image_id' => $image->id,
			]);
	        // 设置redis缓存
        	Redis::set('place_'.$place->id.'_'.$place->store_id.'_'.$place->id, $encrypted);
		}
	}
} 