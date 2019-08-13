<?php

namespace App\Http\Controllers\Api;

use DB;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;

class UsersController extends Controller
{
    /**
     * 自定义验证规则
     */ 
    protected function validatorUserRegister(array $data, string $type)
    {
        switch ($type) {
            case 'register':
                return Validator::make($data, [
                    'name' => 'required',
                    'password' => 'required',
                    // 'email' => 'required|email|unique:users',
                ], [
                    'name.required' => '姓名不能为空',
                    'password.required' => '密码不能为空',
                    // 'email.required' => '邮箱不能为空',
                    // 'email.email' => '请输入有效邮箱',
                    // 'email.unique' => '此邮箱已被注册',
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
        }
    }

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(User $user){
        $account = request('account');

        if (method_exists($user, 'findForPassport')) {
            $user = (new $user)->findForPassport($account);
        } else {
            $user = (new $user)->where('email', $account)->first();
        }

        if (! $user) {
            return response()->json(['error'=>'Unauthorised', 'status' => 401]);
        } else {
            if(Auth::attempt(['phone' => $user->phone, 'password' => request('password')])){
                // 删除之前的token
                // DB::table('oauth_access_tokens')->where('user_id',$user->id)->where('name','MyApp')->update(['revoked'=>1]);
                DB::table('oauth_access_tokens')->where('user_id',$user->id)->where('name','MyApp')->delete();
                // 获取新的token
                $success['token'] =  $user->createToken('MyApp')->accessToken;
                return response()->json(['success' => $success, 'status' => 200]);
            }
            return response()->json(['error'=>'Unauthorised', 'status' => 401]);
        }
    }
 
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {   
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['message' => '验证码已失效', 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['message' => '验证码错误' , 'status' => 402]);
        }

        $validator = $this->validatorUserRegister($request->all(), 'register');
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors(), 'status' => 401]);
        }

        $user = User::create([
            'name' => $request->name,
            // 'email' => $request->email,
            'area_code' => $verifyData['area_code'],
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
            'pro_password' => $request->password
        ]);

        // 清除验证码缓存
        \Cache::forget($request->key);

        return response()->json(['success'=> [
                                    'name' => $user->name,
                                    'token' => $user->createToken('MyApp')->accessToken
                                ]
                                , 'status' => 200]);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function member()
    {
        $user = auth()->user();
        // dd($user->images());
        // return response()->json(['data' => $user, 'status' => 200]);
        // 数据单个
        return (new UserResource($user))->additional(['status'=>200]);
        // 数据集合
        // return (new UserCollection($user->images()->get()))->additional(['status' => 200]);
        // return (new UserCollection(User::all()))
                // ->additional(['meta' => ['key' => 'value']]);
    }

    /**
     * 忘记密码
     */ 
    public function forgotPassWord(Request $request, User $user)
    {
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['message' => '验证码已失效', 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['message' => '验证码错误' , 'status' => 402]);
        }

        $validator = $this->validatorUserRegister($request->all(), 'password');
        
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors(), 'status' => 401]);
        }

        $attributes['pro_password'] = $request->password;
        $attributes['password'] = $user->setPasswordAttribute($request->password);
        
        $user = User::where('phone', $verifyData['phone'])->first();
        // $user::->update($attributes);
        User::where('phone', $verifyData['phone'])->update($attributes);

        // 清除验证码缓存
        \Cache::forget($request->key);

        return response()->json(['success'=> [
                                    'name' => $user->name,
                                    'message' => '密码修改成功！'
                                ]
                                , 'status' => 200]);
    }
}
