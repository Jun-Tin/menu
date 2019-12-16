<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'title', 'order', 'operate', 'accept', 'target', 'execute', 'type', 'number', 'method'
    ];
}
