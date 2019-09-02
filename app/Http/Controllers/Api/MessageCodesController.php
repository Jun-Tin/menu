<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\{EasySms, PhoneNumber};
use App\Http\Controllers\Controller;

class MessageCodesController extends Controller
{
    /** [获取验证码] */
    public function store(Request $request)
    {
        // 根据区号不同，发送国际短信
        $phone = new PhoneNumber($request->phone, $request->area_code);
        // 生成四位随机数 左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        $sms = app('easysms');
        if ( app()->environment('local')) {
            $code = '1234';
        }

        try {
            $sms->send($phone, [
                    'data' => [
                        $code,
                        '1',
                    ],
                    'template'  => '127203',
            ]);
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
            $message = $exception->getException('qcloud')->getMessage() ?: '短信发送异常';
            return response()->json(['message' =>$message, 'status' => 404]);
        }

        $key = 'smsCode_'.str_random(15);
        $expiredAt = now()->addMinutes(10);
        // 缓存码十分钟后过期
        \Cache::put($key, [
                            'phone' => $request->phone, 
                            'area_code'=>$request->area_code, 
                            'code' => $code
                        ], $expiredAt);

        return response()->json(['success' => [
                                            'key' => $key,
                                            'expired_at' => $expiredAt->toDateTimeString()
                                ], 
                                'status' => 200 ,
                                'message' => '发送成功！']);
    }
}
