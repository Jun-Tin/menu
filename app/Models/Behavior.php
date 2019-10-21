<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    protected $fillable = [
        'user_id', 'target_id', 'category', 'status',
    ];
}
