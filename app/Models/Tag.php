<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'pid', 'user_id', 'name', 'category'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    /** [ 一对多标签关联关系 ]*/
    public function menus()
    {
    	return $this->belongsToMany(Menu::class, 'menu_tag', 'tag_id', 'menu_id')->withTimestamps();
    }
}
