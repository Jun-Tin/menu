<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qrcode extends Model
{
    protected $fillable = [
        'image', 'link'
    ];
}
