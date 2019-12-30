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
        $collection = $user->store->areas->map(function ($item){
            $item->lines = $item->lines()->where('status', '<>', 4)->get();
            return $item;
        });
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

        return (new LineResource($line))->additional(['status' => 200, 'message' => '创建成功！']);
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
        $line->fill($request->all());
        $line->update();

        Gateway::sendToGroup('waiter_'.$line->store_id, json_encode(array('type' => 'lining', 'message' => '更新排队列表！'), JSON_UNESCAPED_UNICODE));

        return (new LineResource($line))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 大屏幕列表 】 */
    public function screen(Request $request, Store $store)
    {
        $store = Store::find($request->header('storeid'));
        $collection = $store->areas->map(function ($item){
            $item->lines = $item->lines()->where('status', '<>', 4)->get();
            return $item;
        });

        return (new LineCollection($collection))->additional(['status' => 200]);
    } 
}
