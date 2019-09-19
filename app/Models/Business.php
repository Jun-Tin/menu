<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'store_id', 'category', 'start_time', 'end_time'
    ];
}
