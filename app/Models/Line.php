<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    protected $fillable = [
        'store_id', 'area_id', 'code', 'number', 'name', 'phone', 'status'
    ];
}
