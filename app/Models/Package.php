<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'store_id', 'name', 'image_id', 'original_price', 'special_price', 'level'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    /** [ 多对多标签关联关系 ]*/
    public function menus()
    {
    	// return $this->belongsToMany(Tag::class, 'menu_tag', 'menu_id', 'tag_id')->withPivot('menu_id', 'tag_id')->withTimestamps();
    	return $this->belongsToMany(Menu::class, 'package_menu', 'package_id', 'menu_id')->withTimestamps();
    }
}
