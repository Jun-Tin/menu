<?php

namespace App\Models;

use App\Models\Image;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'store_id', 'name', 'number', 'floor', 'image_id'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    /** [ 一对一图片关联关系 ] */
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }
}
