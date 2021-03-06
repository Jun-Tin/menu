<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shopcart extends Model
{
    protected $fillable = [
        'place_id', 'menu_id', 'category', 'menus_id', 'tags_id', 'fill_price', 'number', 'price', 'original_price', 'remark', 'sitter'
    ];
}
