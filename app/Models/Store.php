<?php

namespace App\Models;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time'
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

    /**【 一对多套餐关联关系 】*/ 
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    /**【 一对多座位关联关系 】*/ 
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**【 多对多员工关联关系 】*/
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /** 【 一对多营业时间关联关系 】 */
    public function business()
    {
        return $this->hasMany(Business::class);
    }

    /** 【 获取营业时间段 】 */
    public function getTime($morning_start, $morning_end, $afternoon_start, $afternoon_end, $night_start, $night_end)
    {
        if ($morning_start) {
            $data['start_time'] = strtotime($morning_start);
        } else if ($afternoon_start){
            $data['start_time'] = strtotime($afternoon_start);
        } else {
            $data['start_time'] = strtotime($night_start);
        }

        if ($night_end) {
            $data['end_time'] = strtotime($night_end);
        } else if ($afternoon_end) {
            $data['end_time'] = strtotime($afternoon_end);
        } else {
            $data['end_time'] = strtotime($morning_end);
        }

        return $data;
    }

    /** 【 添加营业时间 】 */
    public function addBusiness($morning_start, $morning_end, $afternoon_start, $afternoon_end, $night_start, $night_end, $store_id)
    {
        if ($morning_start || $morning_end) {
            Business::where('store_id',$store_id)->where('category',1)->update([
                'start_time' => strtotime($morning_start),
                'end_time' => strtotime($morning_end)
            ]);
        }

        if ($afternoon_start || $afternoon_end) {
            Business::where('store_id',$store_id)->where('category',2)->update([
                'start_time' => strtotime($afternoon_start),
                'end_time' => strtotime($afternoon_end)
            ]);
        }

        if ($night_start || $night_end) {
            Business::where('store_id',$store_id)->where('category',3)->update([
                'start_time' => strtotime($night_start),
                'end_time' => strtotime($night_end)
            ]);
        }
    }
}
