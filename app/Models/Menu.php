<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'store_id', 'name', 'image_id', 'original_price', 'special_price', 'level', 'type', 'category', 'status'
    ];

    /** 【 一对一图片关联关系 】*/ 
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }
    
    /** 【 多对多标签关联关系 】*/
    public function tags()
    {
    	return $this->belongsToMany(Tag::class, 'menu_tag', 'menu_id', 'target_id')->withPivot('id', 'order_number')->withTimestamps()->orderBy('order_number');
    }

    /** 【 多对多标签嵌入菜品关联关系 】 */
    public function menus($value)
    {
        return $this->belongsToMany(self::class, 'menu_tag', 'menu_id', 'target_id')->withPivot('id', 'fill_price', 'order_number')->wherePivot('pid', $value)->withTimestamps()->orderBy('order_number');
    }

    /** 【 一对多标签关联关系 】 */
    public function menuTag()
    {
        return $this->hasMany(MenuTag::class, 'menu_id', 'id');
    } 
}
