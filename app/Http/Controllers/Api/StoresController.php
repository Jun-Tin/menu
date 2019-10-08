<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, StoreCollection, PackageResource, PackageCollection, PlaceResource, UserResource, PlaceCollection, BookResource};

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
        $store->save();

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
        $store->update($request->all());

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
        return (new StoreCollection($store->packages))->additional(['status' => 200]);
    }

    /** 【 座位列表 】 */
    public function places(Request $request, Store $store)
    {
        $floor = $store->places()->where('floor', 0)->get();

        return PlaceResource::collection($floor)->additional(['status' => 200]);
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

    /** 【 预约列表 】 */
    public function book(Request $request, Store $store)
    {        
        return BookResource::collection($store->books)->additional(['status' => 200]);
    }
}
