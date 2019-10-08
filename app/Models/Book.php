<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'store_id', 'place_id', 'create_by', 'name', 'area_code', 'phone', 'gender', 'date', 'meal_time', 'meal_number', 'type', 'lock_in', 'lock_out', 'status'
    ];

    /** 【 一对一座位关联关系 】 */
    public function place()
    {
    	return $this->hasOne(Place::class, 'id', 'place_id');
    } 
}
