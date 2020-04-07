<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shopcart extends Model
{
    protected $fillable = [
        'place_id', 'menu_id', 'category', 'menus_id', 'tags_id', 'fill_price', 'number', 'price', 'original_price', 'remark', 'sitter'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function image()
    {
        return $this->hasManyThrough(Image::class, Menu::class, 'id', 'id', 'menu_id', 'image_id');
    }
}
