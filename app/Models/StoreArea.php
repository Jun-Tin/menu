<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreArea extends Model
{
    protected $table = 'store_area';

    protected $fillable = [
        'store_id', 'screen_link', 'screen_qrcode', 'line_link', 'line_qrcode', 'book_link', 'book_qrcode'
    ];
}
