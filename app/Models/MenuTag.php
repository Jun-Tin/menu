<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTag extends Model
{
    protected $table = 'menu_tag';

    protected $fillable = [
        'menu_id', 'target_id', 'pid', 'fill_price', 'order_number'
    ];
}
