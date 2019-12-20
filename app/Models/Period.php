<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'title', 'number', 'days', 'discount', 'show', 'order_number'
    ];
}
