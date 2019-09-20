<?php

namespace App\Http\Controllers\Api;

use App\Models\{Store, Business};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, StoreCollection, PackageResource, PackageCollection, PlaceResource, UserResource, PlaceCollection};

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        // return (new UserCollection($user->stores()->get()))->additional(['status' => 200]);
        // return new StoreResource($user->stores()->get());
        // return new StoreCollection($user->stores()->get());
        return StoreResource::collection($user->stores)->additional(['status' => 200]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Store $store)
    {
        $user = auth()->user();

        $store->fill($request->all());
        $store->user_id = $user->id;
        // 获取营业时间段
        $timeArr = $store->getTime($request->morning_start, $request->morning_end, $request->afternoon_start, $request->afternoon_end, $request->night_start, $request->night_end);
        $store->start_time = date('H:i',$timeArr['start_time']);
        $store->end_time = date('H:i',$timeArr['end_time']);

        $store->save();
        // 添加营业时间
        $store->addBusiness($request->morning_start, $request->morning_end, $request->afternoon_start, $request->afternoon_end, $request->night_start, $request->night_end, $store->id);

        return (new StoreResource($store))->additional(['status' => 200, 'message' => '创建成功！']);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        return (new StoreResource($store))->additional(['status' => 200]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $store->fill($request->all());
        // 获取营业时间段
        $timeArr = $store->getTime($request->morning_start, $request->morning_end, $request->afternoon_start, $request->afternoon_end, $request->night_start, $request->night_end);
        $store->start_time = date('H:i',$timeArr['start_time']);
        $store->end_time = date('H:i',$timeArr['end_time']);
 
        $store->save();

        if ($request->has('morning_start')) {
            Business::where('store_id',$store->id)->where('category',1)->update([
                'start_time' => strtotime($request->morning_start),
                'end_time' => strtotime($request->morning_end)
            ]);
        }

        if ($request->has('afternoon_start')) {
            Business::where('store_id',$store->id)->where('category',2)->update([
                'start_time' => strtotime($request->afternoon_start),
                'end_time' => strtotime($request->afternoon_end)
            ]);
        }
        
        if ($request->has('night_start')) {
            Business::where('store_id',$store->id)->where('category',3)->update([
                'start_time' => strtotime($request->night_start),
                'end_time' => strtotime($request->night_end)
            ]);
        }

        return (new StoreResource($store))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        $store->business()->delete();
        $store->delete();

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 菜品列表 】 */
    public function menus(Store $store)
    {
        return (new StoreCollection($store->menus))->additional(['status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function packages(Store $store)
    {
        return (new PackageCollection($store->packages))->additional(['status' => 200]);
        // return PackageResource::collection($store->packages)->additional(['status' => 200]);
    }

    /** 【 座位列表 】 */
    public function places(Request $request, Store $store)
    {
        $floor = $store->places()->where('floor', 0)->get();
        foreach ($floor as $key => $value) {
            $value['places'] = $store->places()->where('floor', $value->id)->get();
        }
        // return (new StoreCollection($store->places()->where('floor', $request->floor)->get()))->additional(['status' => 200]);
        return (new PlaceCollection($floor))->additional(['status' => 200]);
    }

    /** 【 员工列表 】 */
    public function users(Store $store)
    {
        return UserResource::collection($store->users()->get())->additional(['status' => 200]);
    }

    /** 【 删除座位--整层 】 */
    public function delete(Request $request, Store $store)
    {
        Storage::disk('qrcodes')->deleteDirectory($store->id.'/'.$request->floor);

        $store->places()->where('id', $request->floor)->delete();
        $store->places()->where('floor', $request->floor)->delete();

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }
}
