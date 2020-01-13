<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreOff extends Model
{
    protected $table = 'store_off';

    protected $fillable = [
        'store_id', 'number'
    ];
}
