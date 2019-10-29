<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order', 'store_id', 'place_id', 'price', 'final_price', 'number', 'final_number', 'status', 'sitter'
    ];

    /** 【 一对多订单详情关联关系 】 */
    public function orders()
    {
    	return $this->hasMany(OrderDetail::class, 'order_order', 'order');
    }

    /** 【 一对一座位关联关系】 */ 
    public function place()
    {
    	return $this->hasOne(Place::class, 'id', 'place_id');
    }
}
