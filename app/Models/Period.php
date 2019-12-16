<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = [
        'title', 'number', 'show', 'order_number'
    ];
}
