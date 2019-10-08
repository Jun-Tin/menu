<?php

namespace App\Models;

use Validator;
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
        'name', 'email', 'password', 'store_id', 'account', 'area_code', 'phone', 'pro_password', 'image_id', 'gender', 'birthday', 'post', 'entry_time'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'pro_password', 'remember_token',
    ];

    /** 【 多字段验证 】 */ 
    public function findForPassport($username)
    {
        return $this->orWhere('email', $username)->orWhere('phone', $username)->orWhere('name',$username)->orWhere('account',$username)->first();
    }

    /** 【 一对一图片关联关系 】 */
    public function image()
    {
        return $this->hasOne(Image::class);
    } 

    /** 【 一对多图片关联关系 】 */ 
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /** 【 一对多门店关联关系 】 */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    /** 【 一对一门店关联关系 】 */ 
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    /** 【 一对多标签关联关系 】 */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /** 【 一对多预约关联关系 】 */
    public function books()
    {
        return $this->hasMany(Book::class, 'create_by', 'id');
    } 

    /** 【 自定义验证规则 ] */ 
    public function validatorUserRegister(array $data, string $type)
    {
        switch ($type) {
            case 'register':
                return Validator::make($data, [
                    'name' => 'required',
                    'password' => 'required',
                    'phone' => 'unique:users'
                ], [
                    'name.required' => '姓名不能为空',
                    'password.required' => '密码不能为空',
                    'phone.unique' => '手机号码已存在'
                ]);
                break;
            case 'password':
                return Validator::make($data, [
                    'password' => 'required',
                    'comfirmPassword' => 'required|same:password',
                ], [
                    'password.required' => '密码不能为空',
                    'comfirmPassword.required' => '确认密码不能为空',
                    'comfirmPassword.same' => '密码与确认密码不匹配',
                ]);
                break;
            case 'update':
                return Validator::make($data, [
                    'phone' => 'unique:users,phone,' .$data['id'],
                ], [
                    'phone.unique' => '手机号码已存在'
                ]);
                break;
        }
    }

    /** 【 生成随机数 】 */ 
    public function random()
    {
        return str_pad(random_int(1, 99999999), 8, 0, STR_PAD_LEFT);
    }
}
