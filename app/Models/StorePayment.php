<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePayment extends Model
{
    protected $fillable = [
        'store_id', 'payment_id', 'client_id', 'client_secret'
    ];

    /** 【 反向一对一支付类型关联关系 】 */
    public function payment_method()
    {
    	return $this->belongsTo(PaymentMethod::class, 'payment_id', 'id');
    } 
}
