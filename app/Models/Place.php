<?php

namespace App\Models;

use Validator;
use Illuminate\Validation\Rule;
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

    /** 【 反向一对一门店关联关系 】 */
    public function store()
    {
        return $this->belongsTo(Store::class);
    } 

    /** 【 更新二维码信息 】 */
    public function updateQrcode($data, $id)
    {
        $encrypted = substr(Crypt::encryptString($data['name'].'_'.$id.'_code'), 0, 15);
        $filename = $data['name'] .'.png';
        switch ($data['type']) {
            case 'place':
                $dir = public_path('images/qrcodes/'.$data['store_id']. '/place/' .$data['floor']);
                $qrcode = true;
                $link = env('APP_CLIENT').$data['store_id'].'/'.$id.'/'.$encrypted;
                $data['name'] = 'place_'.$id;
                break;
            case 'waiter':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/user');
                $link = env('APP_STAFF').$id.'/'.$encrypted;
                $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/user/'. $filename;
                break;
            case 'chef':
                $dir = public_path('images/qrcodes/'.$data['store_id'].'/user');
                $link = env('APP_CHEF').$id.'/'.$encrypted;
                $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/user/'. $filename;
                break;
            case 'store':
                $dir = public_path('images/qrcodes/'. $data['store_id']. '/screen/');
                switch ($data['category']) {
                    case 'screen':
                        $filename = 'screen.png';
                        $link = env('APP_SCREEN'). $data['store_id']. '/screen/'. $encrypted;
                        $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/screen/'. $filename;
                        break;
                    case 'line':
                        $filename = 'line.png';
                        $link = env('APP_LINE'). $data['store_id']. '/line/'. $encrypted;
                        $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/screen/'. $filename;
                        break;
                    case 'book':
                        $filename = 'book.png';
                        $link = env('APP_BOOK'). $data['store_id']. '/book/'. $encrypted;
                        $qrcode = env('APP_URL').'/images/qrcodes/'. $data['store_id']. '/screen/'. $filename;
                        break;
                }
                break;
        }
        if (!is_dir($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
        // 判断图片是否存在
        if (file_exists($dir. '/' .$filename)) {
            unlink($dir. '/' .$filename);
        }
        // 保存二维码
        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate($link, $dir. '/'. $filename);
        // 设置redis缓存
        Redis::set($data['name'].'_'.$data['store_id'].'_'.$id, $encrypted);
        return [
            'qrcode' => $qrcode,
            'link' => $link,
        ];
    } 

    /** 【 自定义验证规则 】 */
    public  function validatorPlaceName($data, $id)
    {
        return Validator::make($data, [
            'name' => [
                'required',
                Rule::unique('places')->ignore($id)->where(function ($query) use($data){
                    $query->where('floor', $data['floor']);
                })
            ],
        ], [
            'name.required' => __('messages.placeName_required'),
            'name.unique' =>  __('messages.placeName_unique'),
        ]);
    } 
}
