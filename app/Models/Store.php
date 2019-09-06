<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'user_id','name', 'address', 'image_id', 'phone', 'start_time', 'end_time', 'intro', 'set_time'
    ];

    protected $hidden = [
        'deleted_at',
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
}
