<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time', 'clean', 'settle', 'active'
    ];
    
    /**【 一对一图片关联关系 】*/ 
    public function image()
    {
    	return $this->hasOne(Image::class, 'id', 'image_id');
    }

    /**【 一对多菜品关联关系 】*/
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**【 一对多座位关联关系 】*/ 
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**【 一对多员工关联关系 】*/
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** 【 一对多标签关联关系 】 */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /** 【 一对多预约关联关系】 */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /** 【 一对多订单关联关系 】 */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** 【 一对多远层订单详情关联关系 】 */
    public function order_details()
    {
        return $this->hasManyThrough(OrderDetail::class, Order::class, 'store_id', 'order_order', 'id', 'order');
    } 
}
