<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'store_id', 'place_id', 'name', 'area_code', 'phone', 'gender', 'date', 'meal_time', 'meal_number', 'type', 'lock_in', 'lock_out', 'status'
    ];
}
