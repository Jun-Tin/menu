<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Redis, Crypt, File};
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Place extends Model
{
    protected $fillable = [
        'store_id', 'name', 'number', 'floor', 'image_id', 'status'
    ];

    /** 【 一对一图片关联关系 】 */
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }

    /** 【 一对多购物车关联关系 】 */
    public function shopcarts()
    {
    	return $this->hasMany(Shopcart::class);
    } 

    /** 【 一对一订单关联关系 】 */
    public function order()
    {
        return $this->hasOne(Order::class);
    } 

    /** 【 更新二维码信息 】 */
    public function updateQrcode($data, $id)
    {
        $encrypted = substr(Crypt::encryptString($data['name'].'_'.$id.'_code'), 0, 15);
        $filename = $data['name'] . '.png';
        switch ($data['type']) {
            case 'place':
                $dir = public_path('images/qrcodes/'.$data['store_id']. '/place/' .$data['floor']);
                // 判断图片是否存在
                if (file_exists($dir. '/' .$filename)) {
                    unlink($dir. '/' .$filename);
                }
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate(env('APP_CLIENT').$data['store_id'].'/'.$id.'/'.$encrypted, $dir. '/'. $filename);
                $qrcode = true;
                $link = true;
                break;
            case 'waiter':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/user');
                if (!is_dir($dir)) {
                    File::makeDirectory($dir, 0777, true);
                }
                $link = env('APP_STAFF').$id.'/'.$encrypted;
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($link, $dir. '/'. $filename);
                $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/user/'. $filename;
                break;
            case 'chef':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/user');
                if (!is_dir($dir)) {
                    File::makeDirectory($dir, 0777, true);
                }
                $link = env('APP_CHEF').$id.'/'.$encrypted;
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($link, $dir. '/'. $filename);
                $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/user/'. $filename;
                break;
            case 'store':
                $dir = public_path('images/qrcodes/'. $data['store_id']. '/screen/');
                if (!is_dir($dir)) {
                    File::makeDirectory($dir, 0777, true);
                }
                switch ($data['category']) {
                    case 'screen':
                        $encrypted = substr(Crypt::encryptString($data['name']. '_'. $data['store_id']. '_'. $data['store_id']. '_code'), 0, 15);
                        $screen = 'screen.png';
                        // 判断图片是否存在
                        if (file_exists($dir. '/'. $screen)) {
                            unlink($dir. '/'. $screen);
                        }
                        // 保存二维码
                        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate(env('APP_SCREEN'). $data['store_id']. '/screen/'. $encrypted, $dir. '/'. $screen);
                        $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/screen/'. $screen;
                        $link = env('APP_SCREEN'). $data['store_id']. '/screen/'. $encrypted;
                        break;
                    case 'line':
                        $encrypted = substr(Crypt::encryptString($data['name']. '_'. $data['store_id']. '_'. $data['store_id']. '_code'), 0, 15);
                        $line = 'line.png';
                        if (file_exists($dir. '/'. $line)) {
                            unlink($dir. '/'. $line);
                        }
                        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate(env('APP_LINE'). $data['store_id']. '/line/'. $encrypted, $dir. '/'. $line);
                        $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/screen/'. $line;
                        $link = '';
                        break;
                }

                break;
            default :
                $qrcode = '';
                $link = '';
                break;
        }
        // 设置redis缓存
        Redis::set($data['name'].'_'.$data['store_id'].'_'.$id, $encrypted);
        return [
            'qrcode' => $qrcode,
            'link' => $link,
        ];
    } 
}
