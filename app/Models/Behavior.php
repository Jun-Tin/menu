<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    protected $fillable = [
        'user_id', 'target_id', 'category', 'status',
    ];

    /** 【 一对一订单关联关系 】 */
    public function order()
    {
    	return $this->hasOne(Order::class, 'id', 'target_id');
    }

    /** 【 一对一订单详情关联关系 】 */
    public function order_detail()
    {
    	return $this->hasOne(OrderDetail::class, 'id', 'target_id');
    }

}
