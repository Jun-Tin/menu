<?php

namespace App\Models;

use Validator;
use App\Models\Bill;
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
        'name', 'email', 'password', 'store_id', 'account', 'area_code', 'phone', 'pro_password', 'image_id', 'gender', 'birthday', 'post', 'entry_time', 'link', 'qrcode', 'created_by', 'coins'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'pro_password', 'remember_token',
    // ];

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
        return $this->hasOne(Store::class, 'id', 'store_id');
    }

    /** 【 一对多标签关联关系 】 */
    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    /** 【 一对多行为关联关系 】 */
    public function behaviors()
    {
        return $this->hasMany(Behavior::class);
    }

    /** 【 一对多下线（客户）关联关系 】 */
    public function users()
    {
        return $this->hasMany(self::class, 'created_by', 'id');
    } 

    /** 【 一对多账单关联关系 】 */
    public function bills()
    {
        return $this->hasMany(Bill::class, 'operate', 'id');
    } 

    /** 【 一对一我的上线关联关系 】 */
    public function user()
    {
        return $this->hasOne(self::class, 'id', 'created_by');
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
                    'name.required' => __('messages.name_required'),
                    'password.required' => __('messages.password_required'),
                    'phone.unique' => __('messages.phone_unique')
                ]);
                break;
            case 'password':
                return Validator::make($data, [
                    'password' => 'required',
                    'comfirmPassword' => 'required|same:password',
                ], [
                    'password.required' => __('messages.password_required'),
                    'comfirmPassword.required' => __('messages.comfirmPassword_required'),
                    'comfirmPassword.same' => __('messages.comfirmPassword_same'),
                ]);
                break;
            case 'update':
                return Validator::make($data, [
                    'phone' => 'required|unique:users,phone,' .$data['id'],
                ], [
                    'phone.required' => __('messages.phone_required'),
                    'phone.unique' => __('messages.phone_unique'),
                ]);
                break;
            case 'updated':
                return Validator::make($data, [
                    'phone' => 'required',
                ], [
                    'phone.required' => __('messages.phone_required'),
                ]);
                break;
        }
    }

    /** 【 生成随机数 】 */ 
    public function random()
    {
        return str_pad(random_int(1, 99999999), 8, 0, STR_PAD_LEFT);
    }

    /**
     * 后台列表-select-option
     */
    public static function getSelectOptions()
    {
        $options = User::select('id','name as text')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }
}
