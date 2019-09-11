<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'pid', 'user_id', 'name', 'category'
    ];

    /** 【 多对多标签关联关系 】 */
    public function menus()
    {
    	return $this->belongsToMany(Menu::class, 'menu_tag', 'tag_id', 'menu_id')->withTimestamps();
    	// return $this->belongsToMany(Menu::class, 'package_group', 'package_id', 'target_id')->withPivot('id', 'fill_price')->wherePivot('pid', $value)->withTimestamps()->orderBy('order_number');
    }
}
