<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'title', 'number', 'days', 'show', 'order_number'
    ];
}
