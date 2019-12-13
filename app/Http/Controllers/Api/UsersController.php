<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Models\{User, Store, Place, Bill};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\{UserResource, BehaviorResource, BillResource};

class UsersController extends Controller
{
    /**【 登录 】*/
    public function login(Request $request, User $user){
        $account = $request->account;

        if (method_exists($user, 'findForPassport')) {
            $user = (new $user)->findForPassport($account);
        } else {
            $user = (new $user)->where('name', $account)->first();
        }

        if (! $user) {
            return response()->json(['error' => ['message' => ['用户不存在！']], 'status' => 401]);
        }

        if ($user->post == 'boss') {
            // 判定条件
            $status = Auth::attempt(['phone' => $user->phone, 'password' => $request->password]);
        } else {
            $status = Auth::attempt(['account' => $user->account, 'password' => $request->password]);
        }
        if($status){
            if ($user->post == $request->identity || $user->post == 'manager' || $user->post == 'boss') {
                if ($user->post == $request->identity) {
                    $scopes = $user->post;
                } else if($user->post == 'manager') {
                    $scopes = 'manager';
                } else {
                    $scopes = 'boss';
                }
                // 删除之前的token
                // DB::table('oauth_access_tokens')->where('user_id',$user->id)->where('name','MyApp')->update(['revoked'=>1]);
                DB::table('oauth_access_tokens')->where('user_id', $user->id)->where('name', 'MyApp')->delete();
                // 获取新的token
                $success['token'] =  $user->createToken('MyApp', [$scopes])->accessToken;
                return response()->json(['success' => $success, 'status' => 200, 'message' => '登录成功！']);
            }
            return response()->json(['error' => ['message' => ['没有权限登录！']], 'status' => 203]);
        }
        return response()->json(['error' => ['message' => ['密码错误！']], 'status' => 401]);
    }
 
    /**【 注册 】*/
    public function register(Request $request, User $user)
    {   
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['error' => ['message' => ['验证码已失效']], 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['error' => ['message' => ['验证码错误']], 'status' => 402]);
        }
        
        $data = $request->all();
        $data['phone'] = $verifyData['phone'];

        $validator = $user->validatorUserRegister($data, 'register');

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 401]);
        }

        $user = User::create([
            'name' => $request->name,
            'area_code' => $verifyData['area_code'],
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
            'pro_password' => $request->password,
            'post' => 'boss',
            'account' => $user->random(),
        ]);

        // 清除验证码缓存
        \Cache::forget($request->key);

        return response()->json(['success'=> [
                                    'name' => $user->name,
                                    'token' => $user->createToken('MyApp', ['boss'])->accessToken
                                ], 
                                'status' => 200 ,
                                'message' => '注册成功！' ]);
    }

    /**【 个人信息 】*/
    public function member()
    {
        $user = auth()->user();

        return (new UserResource($user))->additional(['status' => 200, 'identity' => $user->token()->scopes[0]]);
    }

    /**【 忘记密码 】*/ 
    public function forgotPassWord(Request $request, User $user)
    {
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['error' => ['message' => ['验证码已失效']], 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['error' => ['message' => ['验证码错误']], 'status' => 402]);
        }

        $validator = $user->validatorUserRegister($request->all(), 'password');
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 401]);
        }
        
        $user = User::where('phone', $verifyData['phone'])->first();
        User::where('phone', $verifyData['phone'])->update([
            'pro_password' => $request->password,
            'password' => bcrypt($request->password)
        ]);

        // 清除验证码缓存
        \Cache::forget($request->key);

        return response()->json(['success'=> [
                                    'name' => $user->name,
                                ], 
                                'status' => 200, 
                                'message' => '密码修改成功！']);
    }

    /**【 修改密码 / 手机号码 】*/


    /** 【 员工详情 】 */
    public function detail(User $user)
    {        
        return (new UserResource($user))->additional(['status' => 200]);
    } 
    
    /** 【 设置员工信息 】 */
    public function staff(Request $request, User $user, Place $place)
    {
        $user = User::create([
            'name' => $request->name,
            'area_code' => $request->area_code,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'entry_time' => strtotime($request->entry_time),
            'post' => $request->post,
            'password' => bcrypt($request->password),
            'pro_password' => $request->password,
            'store_id' => $request->store_id,
            'account' => $user->random(),
        ]);

        // 创建登录二维码写入登录链接
        $data = [
            // 默认类型值
            'type' => $request->post,
            'store_id' => $request->store_id,
            'name' => $user->account,
            'id' => $user->id,
        ];
        $result = $place->updateQrcode($data,$user->id);
        // 更新数据
        $user->update(['qrcode' => $result['qrcode'], 'link' => $result['link']]);
        
        return (new UserResource($user))->additional(['status' => 200,  'message' => '创建成功！']);
    }

    /** 【 修改员工信息 】 */ 
    public function edit(Request $request, User $user, Place $place)
    {
        $data = $request->all();
        $data['id'] = $user->id;
        $validator = $user->validatorUserRegister($data, 'updated');

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 401]);
        }

        $user->fill($request->all());
        $user->password = bcrypt($request->password);
        $user->pro_password = $request->password;
        $user->entry_time = strtotime($request->entry_time);

        $data['name'] = $user->account;
        $data['type'] = $request->post;
        $result = $place->updateQrcode($data,$user->id);
        $user->qrcode = $result['qrcode'];
        $user->link = $result['link'];

        $user->update();

        return (new UserResource($user))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 删除员工信息 】 */
    public function delete(User $user)
    {
        // 如果不是老板，删除员工二维码
        if ($user->post != 'boss') {
            $path = substr($user->qrcode, strripos($user->qrcode, "images"));
            unlink($path);
        }
        $user->delete();

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 员工表现 】 */ 
    public function behavior(Request $request, User $user)
    {
        $user = auth()->user();

        return (BehaviorResource::collection($user->behaviors()->orderBy('created_at', 'desc')->get()))->additional(['status' => 200]);
    }

    /** 【 退出登录 】 */
    public function logout(Request $request)
    {
        if (Auth::guard('api')->check()){
            Auth::guard('api')->user()->token()->revoke();
        }

        return response()->json(['message' => '退出成功！', 'status' => 200]);
    }

    /** 【 更换关联手机 —— 验证手机号码 】 */ 
    public function verify(Request $request, User $user)
    {
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['error' => ['message' => ['验证码已失效']], 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['error' => ['message' => ['验证码错误']], 'status' => 402]);
        }

        // 清除验证码缓存
        \Cache::forget($request->key);

        return response()->json(['message' => '验证通过！', 'status' => 200]);
    }

    /** 【 更换关联手机 —— 新手机号码 】 */ 
    public function relate(Request $request, User $user)
    {
        // 获取缓存的手机号和区号，以及验证码
        $verifyData = \Cache::get($request->key);
        if ( ! $verifyData) {
            return response()->json(['error' => ['message' => ['验证码已失效']], 'status' => 422]);
        }

        if ( ! hash_equals($verifyData['code'], $request->code)) {
            return response()->json(['error' => ['message' => ['验证码错误']], 'status' => 402]);
        }

        $data = $request->all();
        $user = auth()->user();
        $data['id'] = $user->id;
        $validator = $user->validatorUserRegister($data, 'update');

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors(), 'status' => 401]);
        }

        $user->update([
            'area_code' => $request->area_code,
            'phone' => $request->phone,
        ]);
        // 清除验证码缓存
        \Cache::forget($request->key);

        return (new UserResource($user))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 销售人员创建账号 】 */
    public function create(Request $request, User $user)
    {
        $data = $request->all();
        $validator = $user->validatorUserRegister($data, 'register');

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 401]);
        }

        $user = User::create([
            'name' => $request->name,
            'area_code' => 86,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'pro_password' => $request->password,
            'post' => 'boss',
            'account' => $user->random(),
            'created_by' => auth()->user()->id,
            'coins' => 0,
        ]);

        return response()->json(['success'=> [
                                    'name' => $user->name,
                                    // 'token' => $user->createToken('MyApp', ['boss'])->accessToken
                                ], 
                                'status' => 200 ,
                                'message' => '创建成功！' ]);

    }

    /** 【 我的客户 】 */
    public function client(Request $request, User $user)
    {
        $users = auth()->user()->users;
        return (UserResource::collection($users))->additional(['status' => 200]);
    } 

    /** 【 修改客户金币数 】*/ 
    public function update(Request $request, User $user)
    {
        $user = auth()->user();
        // 修改客户金币数
        User::where('id', $request->user_id)->increment('coins', $request->number);
        // 写入账单记录
        Bill::create([
            'title' => '充值金币',
            'order' => 'Pay'.date('YmdHis').$user->random(),
            'operate' => $user->id,
            'accept' => $request->user_id,
            'execute' => 1,
            'type' => 1,
            'number' => $request->number,
            'method' => $request->method,
        ]);
        return response()->json(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 我的账单 】 */
    public function bill(Request $request, User $user)
    {
        $user = auth()->user();
        return (BillResource::collection($user->bills))->additional(['status' => 200]);
    } 

    /** 【 免登录验证 】 */
    public function confirm(Request $request, User $user)
    {
        $user = $user::find($request->header('userid'));
        if ($user) {
            return response()->json(['success' => [ 'token' => $user->createToken('MyApp', [$user->post])->accessToken],
                                    'status' => 200,
                                    'message' => '登录成功！'
            ]);
        }
        return response()->json(['error' => ['message' => ['非法访问！']], 'status' => 404]);
    } 
}
