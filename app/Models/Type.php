<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'name_cn', 'name_hk', 'name_en', 'show'
    ];

}
