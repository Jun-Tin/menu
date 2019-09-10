<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case "POST":
                return [
                    'name' => 'required',
                    'password' => 'required',
                    'phone' => 'unique:users'
                ];
                break;
            case "PATCH":
                return [
                    'password' => 'required',
                    'comfirmPassword' => 'required|same:password',
                ];
                break;
        }
    }

    /**
     * 获取被定义验证规则的错误消息
     *
     * @return array
     * @translator laravelacademy.org
     */
    public function messages(){
        return [
            'name.required' => '姓名不能为空',
            'password.required' => '密码不能为空',
            'phone.unique' => '手机号码已存在',
            'password.required' => '密码不能为空',
            'comfirmPassword.required' => '确认密码不能为空',
            'comfirmPassword.same' => '密码与确认密码不匹配',
        ];
    }
}
