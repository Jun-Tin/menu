<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'area_code', 'phone', 'pro_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /** [ 多字段验证 ]*/ 
    public function findForPassport($username)
    {
        return $this->orWhere('email', $username)->orWhere('phone', $username)->first();
    }

    /** [setPasswordAttribute 修改器修改密码] */
    public function setPasswordAttribute($value)
    {
        if (strlen($value) != 60) {
            // 不等于 60 ，不做加密处理
            $value = bcrypt($value);
        }

        // $this->attributes['password'] = $value;
        return $value;
    }

    /** [ 一对多图片关联关系 ] */ 
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
