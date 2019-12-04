<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{Redis, Crypt};
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
        $encrypted = substr(Crypt::encryptString($data['name'].'_'.$id.'_code'),0,15);
        $filename = $data['name'] . '.png';
        switch ($data['type']) {
            case 'place':
                $dir = public_path('images/qrcodes/'.$data['store_id']. '/' .$data['floor']);
                $env = env('APP_CLIENT');
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($env.$data['store_id'].'/'.$id.'/'.$encrypted, $dir. '/'. $filename);
                break;
            case 'staff':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/store');
                $env = env('APP_STAFF');
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($env.$id.'/'.$encrypted, $dir. '/'. $filename);
                break;
            case 'chef':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/store');
                $env = env('APP_CHEF');
                QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($env.$id.'/'.$encrypted, $dir. '/'. $filename);
                break;
        }
        // $dir = public_path('images/qrcodes/'.$data['store_id']. '/' .$data['floor']);
        // QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate(env('APP_CLIENT').$data['store_id'].'/'.$id.'/'.$encrypted, $dir. '/'. $filename);
        // 设置redis缓存
        Redis::set($data['name'].'_'.$id, $encrypted);
    } 
}
