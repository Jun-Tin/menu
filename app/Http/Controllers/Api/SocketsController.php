<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GatewayWorker\Lib\Gateway;

class SocketsController extends Controller
{
    /** 【 加入端口 】 */
    public function join(Request $request)
    {
        Gateway::joinGroup($request->client_id, $request->group.'_'.$request->store_id);

        return response()->json(['status' => 200, 'message' => '加入成功！']);
    } 

    /** 【 离开端口 】 */
    public function leave(Request $request)
    {
        Gateway::leaveGroup($request->client_id, $request->group.'_'.$request->store_id);

        return response()->json(['status' => 200, 'message' => '离开成功！']);
    } 

    /** 【 测试socket通讯 】 */
    public function test(Request $request)
    {
        dd(Gateway::sendToGroup($request->group.'_'.$request->store_id, json_encode(array('type' => 'update serving', 'message' => '更新上菜消息！'), JSON_UNESCAPED_UNICODE)));
    } 


}
