<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Areacode extends Model
{
    protected $fillable = [
    	'acname_en', 'acname_cn', 'codename', 'acnumber', 'order_number', 'show'
    ];
}
