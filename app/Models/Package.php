<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'store_id', 'name', 'image_id', 'original_price', 'special_price', 'level'
    ];

    /** 【 一对一图片关联关系 】 */ 
    public function image()
    {
        return $this->hasOne(Image::class, 'id', 'image_id');
    }

    // /** 【 多对多菜品关联关系 】 */
    // public function menus()
    // {
    // 	return $this->belongsToMany(Menu::class, 'package_menu', 'package_id', 'menu_id')->withPivot('fill_price')->withTimestamps();
    // }

    /** 【 多对多标签关联关系 】 */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'package_group', 'package_id', 'target_id')->withPivot('id', 'order_number')->wherePivot('pid', 0)->withTimestamps()->orderBy('order_number');
    }

    /** 【 解除所有多对多关联关系】 */
    public function allTags()
    {
        return $this->belongsToMany(Tag::class, 'package_group', 'package_id', 'target_id');
    }

    /** 【 多对多标签嵌入菜品关联关系 】 */
    public function menus($value)
    {
        return $this->belongsToMany(Menu::class, 'package_group', 'package_id', 'target_id')->withPivot('id', 'fill_price', 'order_number')->wherePivot('pid', $value)->withTimestamps()->orderBy('order_number');
    }
}
