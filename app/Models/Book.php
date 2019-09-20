<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        // 'store_id', 'name', 'phone', 'gender', 'date', 'meal_time', 'meal_number', 'status'
        'store_id', 'name', 'phone', 'gender'
    ];
}
