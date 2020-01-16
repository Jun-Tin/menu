<?php

namespace App\Http\Controllers\Api;

use App\Models\Areacode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreacodeResource;

class AreacodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Areacode $areacode)
    {
        return (new AreacodeResource($areacode::where('show', 1)->orderBy('order_number', 'desc')->get()))->additional(['status' => 200]);
    }

    /** 【 切换语言 】 */
    public function changeLocale(Request $request)
    {
        if (!in_array($request->locale, ['en', 'zh_cn', 'zh_hk'])) {
        	return response()->json(['error' => ['message' => __('messages.set_fail')], 'status' => 202]);
        }
        // 设置session值
        // session()->put('locale', $request->locale);
        // 通过全局辅助函数...
		session(['locale' => $request->locale]);

        return response()->json(['status' => 200, 'message' => __('messages.set_success')]);
    } 
}
