<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = [
        'order_order', 'store_id', 'menu_id', 'category', 'place_id', 'pid', 'menus_id', 'tags_id', 'fill_price', 'number', 'price', 'original_price', 'remark', 'status'
    ];

    /** 【 一对多订单详情关联关系 】 */
    public function behavior()
    {
    	return $this->hasOne(Behavior::class, 'target_id', 'id');
    }

    /** 【 一对一订单关联关系 】 */ 
    public function order()
    {
    	return $this->hasOne(Order::class, 'order', 'order_order');
    }
}
