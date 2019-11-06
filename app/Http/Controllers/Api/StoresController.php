<?php

namespace App\Http\Controllers\Api;

use App\Models\{Store, Tag, Image, Place, Order};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, StoreCollection, PlaceResource, UserResource, BookResource, MenuCollection, MenuResource};

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
        return (new StoreCollection($store->menus()->where('category','m')->get()))->additional(['status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function packages(Store $store)
    {
        return (new MenuCollection($store->menus()->where('category','p')->get()))->additional(['status' => 200]);
    }

    /** 【 座位列表 】 */
    public function places(Request $request, Store $store)
    {
        $floor = $store->places()->where('floor', 0)->get();

        return PlaceResource::collection($floor)->additional(['status' => 200]);
    }

    /** 【 座位列表--按人数筛选 】 */
    public function scrPlaces(Request $request, Store $store)
    {
        $floor = $store->places()->where('floor', 0)->get();

        return PlaceResource::collection($floor)->additional(['status' => 200]);
    }

    /** 【 座位列表--退菜 】 */
    public function retreatPlaces(Request $request, Store $store)
    {
        $floor = $store->places()->where('floor', 0)->get();

        $new = $floor->map(function ($item,$key){
            $item->place = Place::where('floor',$item->id)->get();
            $item->place->map(function ($item){
                $item->order = Order::where('place_id',$item->id)->where('status',0)->whereDate('created_at',date('Y-m-d'))->orderBy('created_at','desc')->first();

            });
            return $item;
        });
        return response()->json(['data' => $new->all(), 'status' => 200]);
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

    /** 【 售罄菜品列表 】 */
    public function saleOut(Request $request, Store $store)
    {
        return (new MenuCollection($store->menus()->where('status',0)->get()))->additional(['status' => 200]);
    }

    /** 【 菜品列表--全部 】 */ 
    public function totalMenus(Request $request, Store $store)
    {
        $all = $store->tags()->where('pid',0)->where('category', 'class')->get();
        $new = $all->map(function ($item, $key){
            $item->menus = Tag::find($item->id)->menus()->where('category','m')->where('status',1)->get();
            $item->menus->map(function ($item){
                $item->image = Image::find($item->image_id);
            });
            return $item;
        });

        return response()->json(['data' => $new->all(), 'status' => 200]);
    }

    /** 【 客户端--门店详情 】 */ 
    public function customerShow(Store $store)
    {
        return (new StoreResource($store))->additional(['status'=>200]);
    }

    /** 【 客户端--菜品列表--全部 】 */ 
    public function customerMenus(Request $request, Store $store)
    {
        $all = $store->tags()->where('pid',0)->where('category', 'class')->get();
        $new = $all->map(function ($item, $key){
            $item->menus = Tag::find($item->id)->menus()->where('category','m')->where('status',1)->get();
            $item->menus->map(function ($item){
                $item->image = Image::find($item->image_id);
            });
            return $item;
        });

        return response()->json(['data' => $new->all(), 'status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function customerPackages(Store $store)
    {
        return (new MenuCollection($store->menus()->where('category','p')->get()))->additional(['status' => 200]);
    }
}
