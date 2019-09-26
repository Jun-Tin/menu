<?php

namespace App\Models;

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
        return $this->hasMany(Menu::class)->where('category','m');
    }

    /**【 一对多套餐关联关系 】*/ 
    public function packages()
    {
        return $this->hasMany(Menu::class)->where('category','p');
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

    /** 【 一对多标签关联关系 】 */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /** 【 远程一对多关联关系 】 */
    public function manyBook()
    {
        return $this->hasManyThrough(Book::class, Place::class, 'store_id', 'place_id');
    }

    /** 【 一对多预约关联关系】 */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
