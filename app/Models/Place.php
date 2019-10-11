<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
