<?php

namespace App\Http\Controllers\Api;

use App\Models\{Line, Store};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{LineResource, LineCollection};
use GatewayWorker\Lib\Gateway;

class LinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $collection = $user->store->areas->map(function ($item) use ($user){
            $item->lines = $item->lines()->where('store_id', $user->store_id)->where('status', '<>', 2)->get();
            return $item;
        })->sortBy('status');
        return (new LineCollection($collection))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Line $line)
    {
        $line->fill($request->all());
        // 获取门店区域
        $store = Store::find($request->store_id);
        $collection = $store->areas->map(function ($item) use ($request){
            if ($item->section_left && $item->section_right) {
                if ($item->section_left <= $request->number && $item->section_right >= $request->number) {
                    return $item->only(['id', 'sign']);
                }
            } else {
                if ($item->section_left <= $request->number) {
                    return $item->only(['id', 'sign']);
                }
            }
        })->filter()->values()->toArray()[0];
        $line->area_id = $collection['id'];
        $code = Line::where('store_id',$request->store_id)->where('area_id', $line->area_id)->orderBy('id', 'DESC')->value('code');
        if ($code) {
            $num = substr($code, 1);
            $number = (int)$num + 1;
            $line->code = $collection['sign']. sprintf("%03d", $number);
        } else {
            $line->code = $collection['sign'].'001';
        }
        $line->save();

        Gateway::sendToGroup('waiter_'.$store->id, json_encode(array('type' => 'lining', 'message' => '更新排队列表！'), JSON_UNESCAPED_UNICODE));

        return (new LineResource($line))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Line $line)
    {
        switch ($request->category) {
            case 'd':
                // 找到上一位号码，改成正在叫号状态
                $upLine = line::where('area_id', $line->area_id)->where('status', 2)->orderBy('id', 'DESC')->first();
                if (empty($upLine)) {
                    return response()->json(['error' => ['message' => [__('messages.up')]], 'status' => 202]);
                }
                $upLine->update(['status' => 1]);
                // 将自身恢复成未叫号状态
                $line->update(['status' => 0]);
                $message = __('messages.rollback');
                break;
            case 'u':
                // 找到下一位号码，改成正在叫号状态
                $downLine = line::where('area_id', $line->area_id)->where('status', 0)->orderBy('id', 'ASC')->first();
                if (empty($downLine)) {
                    return response()->json(['error' => ['message' => [__('messages.down')]], 'status' => 202]);
                }
                $downLine->update(['status' => 1]);
                // 将自身修改成已叫号状态
                $line->update(['status' => 2]);
                $message = __('messages.cut');
                break;
            case 'c':
                $line->update(['status' => 1]);
                $message = __('messages.call');
                break;
        }

        Gateway::sendToGroup('waiter_'.$line->store_id, json_encode(array('type' => 'lining', 'message' => '更新排队列表！'), JSON_UNESCAPED_UNICODE));

        return (new LineResource($line))->additional(['status' => 200, 'message' => $message]);
    }

    /** 【 大屏幕列表 】 */
    public function screen(Request $request, Store $store)
    {
        $store = Store::find($request->header('storeid'));
        $collection = $store->areas->map(function ($item) use ($store){
            $item->lines = $item->lines()->where('store_id', $store->id)->where('status', '<>', 2)->get();
            return $item;
        })->sortBy('status');

        return (new LineCollection($collection))->additional(['qrcode' => $store->area, 'status' => 200]);
    } 
}
