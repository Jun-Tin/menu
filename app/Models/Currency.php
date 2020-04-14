<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'name_cn', 'name_hk', 'name_en', 'unit', 'code', 'order_number'
    ];
}