<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'store_id', 'name', 'image_id', 'original_price', 'special_price', 'level', 'type'
    ];

    /** 【 一对一图片关联关系 】*/ 
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }
    
    /** 【 多对多标签关联关系 】*/
    public function tags()
    {
    	return $this->belongsToMany(Tag::class, 'menu_tag', 'menu_id', 'tag_id')->withTimestamps();
    }
}
