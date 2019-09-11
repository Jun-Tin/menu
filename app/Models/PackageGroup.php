<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageGroup extends Model
{
    protected $table = 'package_group';

    protected $fillable = [
        'package_id', 'target_id', 'pid', 'fill_price', 'order_number'
    ];
}
