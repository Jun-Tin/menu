<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time', 'clean', 'settle', 'active', 'days', 'actived_at', 'after_start', 'after_end', 'type_id', 'condition', 'interval', 'language_id', 'currency_id', 'line', 'chef'
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

    /** 【 一对多排队关联关系 】 */
    public function lines()
    {
        return $this->hasMany(Line::class);
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

    /** 【 一对一门店区域链接关联关系 】 */
    public function area()
    {
        return $this->hasOne(StoreArea::class, 'store_id', 'id');
    }

    /** 【 一对多门店区域关联关系 】 */
    public function areas(){
        return $this->hasMany(Area::class);
    }

    /** 【 一对多门店营业时间关联关系 】 */
    public function offs()
    {
        return $this->hasMany(StoreOff::class);
    } 

    /** 【 一对一语言关联关系 】 */ 
    public function language()
    {
        return $this->belongsTo(Language::class);
    }  

    /** 【 一对一门店类型关联关系 】 */
    public function type() 
    {
        return $this->belongsTo(Type::class);
    }

    /** 【 一对一货币关联关系 】 */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    } 

    /** 【 周数统计 】 */
    public function get_week($year) {
        $year_start = $year . "-01-01";
        $year_end = $year . "-12-31";
        $startday = strtotime($year_start);
        if (intval(date('N', $startday)) >= '5') {
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期
        } else {
            $startday = strtotime("-1 monday", strtotime($year_start)); //获取年第一周的日期
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期
     
        $endday = strtotime($year_end);
        if (intval(date('N', $endday)) > '3') {
            $endday = strtotime("next sunday", strtotime($year_end));
        } else {
            $endday = strtotime("last sunday", strtotime($year_end));
        }
        $num = intval(date('W', $endday));
        for ($i = 1; $i <= $num; $i++) {
            $j = $i -1;
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week"));
     
            $end_day = date("Y-m-d", strtotime("$start_date +6 day"));
            
            $week_array[] = array(
                'period' => $i,
                'startday' => $start_date,
                'endday' => $end_day
                
            );
        }
        return $week_array;
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param Date $startdate 开始日期
     * @param Date $enddate  结束日期
     * @return Array
     */
    public function getDateFromRange($startdate, $enddate){
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;
        // 保存每天日期
        $date = array();
        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }
        return $date;
    }
 
}
