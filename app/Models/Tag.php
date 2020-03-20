<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'pid', 'store_id', 'name', 'category'
    ];

    /** 【 多对多标签关联关系 】 */
    public function menus()
    {
    	return $this->belongsToMany(Menu::class, 'menu_tag', 'target_id', 'menu_id')->withPivot('id', 'order_number')->withTimestamps()->orderBy('order_number', 'desc')->orderBy('id', 'desc');
    }

    /** 【 一对多菜品关联关系 】 */ 
    // public function menu()
    // {
    // 	return $this->hasManyThrough(Menu::class, 'menu_tag', 'tag_id', );
    // }
}
