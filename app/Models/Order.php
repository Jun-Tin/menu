<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $fillable = [
        'order', 'store_id', 'place_id', 'price', 'final_price', 'number', 'final_number', 'finish_number', 'status', 'sitter', 'payment_method', 'finish', 'state', 'paid_at'
    ];

    protected $dates = ['paid_at'];

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

    /** 【 一对一门店关联关系 】 */
    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'store_id');
    } 

    protected function getordersCountAttribute($value)
    {
        return $value ?? $this->orders_count = $this->orders()->count();
    }
}
