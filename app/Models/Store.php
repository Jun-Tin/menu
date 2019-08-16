<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id','name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time'
    ];

    /** [ 一对一图片关联关系 ] */ 
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }
}
