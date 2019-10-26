<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'order_order', 'menu_id', 'menus_id', 'tags_id', 'fill_price', 'number', 'price', 'original_price', 'remark', 'status'
    ];
}
