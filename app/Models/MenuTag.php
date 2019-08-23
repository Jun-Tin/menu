<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class MenuTag extends Model
{
    use SoftDeletes;
	protected $table = ['menu_tag'];
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'store_id','name', 'image_id', 'original_price', 'special_price', 'level'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
