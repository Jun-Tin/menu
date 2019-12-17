<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time', 'clean', 'settle', 'active', 'days', 'actived_at'
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

    /** 【 周数统计 】 */
    public function get_week($year) {
        $year_start = $year . "-01-01";
        $year_end = $year . "-12-31";
        $startday = strtotime($year_start);
        if (intval(date('N', $startday)) != '1') {
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期
     
        $endday = strtotime($year_end);
        if (intval(date('W', $endday)) == '7') {
            $endday = strtotime("last sunday", strtotime($year_end));
        }
        // 1546790400
dd(date('oW', strtotime('2013-12-31')));
        $num = intval(date('W', $endday));
        for ($i = 1; $i <= $num; $i++) {
            $j = $i -1;
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week"));
     
            $end_day = date("Y-m-d", strtotime("$start_date +6 day"));
     
            $week_array[$i] = array (
                str_replace("-",
                ".",
                $start_date
            ), str_replace("-", ".", $end_day));
        }

        return $week_array;
    }
 
}
