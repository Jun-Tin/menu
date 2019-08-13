<?php

namespace App\Http\Requests;

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
        return [
            'name' => 'bail|required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'required',
        ];
    }


    public function messages()
    {
        return  [
            'name.required'     =>'姓名不能为空！',
            'email.required'    =>'邮箱不能为空！',
            // 'email.unique'      =>'邮箱已经被注册！',
            'password.required' =>'密码不能为空！'
        ];
    }
}
