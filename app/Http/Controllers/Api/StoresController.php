<?php

namespace App\Http\Controllers\Api;

use App\Models\{Store, User, MenuTag, Bill};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, StoreCollection, PlaceCollection, UserResource, BookResource, MenuCollection, TagCollection, StatisticsResource};

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
        return (new StoreCollection($user->stores))->additional(['status' => 200]);
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
        // 先给定默认值，后期做修改（删除）
        $store->clean = 1;
        $store->settle = 1;

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

    /** 【 激活门店 】 */
    public function active(Request $request, Store $store, User $user)
    {
        $user = auth()->user();
        $user->decrement('coins', 3);
        $store->update(['active' => 1]);

        // 写入记录
        Bill::create([
            'title' => '激活门店',
            'order' => 'Act'.date('YmdHis').$user->random(),
            'operate' => $user->id,
            'accept' => '系统',
            'execute' => 0,
            'type' => 0,
            'number' => 3,
            'method' => 7,
        ]);

        return (new StoreResource($store))->additional(['status' => 200, 'message' => '激活成功！']);
    } 

    /** 【 菜品列表 】 */
    public function menus(Store $store)
    {
        return (new StoreCollection($store->menus()->where('category', 'm')->get()))->additional(['status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function packages(Request $request, Store $store)
    {
        $where[] = ['category', 'p'];
        if ($request->type == 'in') {
            $where[] = ['status', 1];
        }
        return (new MenuCollection($store->menus()->where($where)->get()))->additional(['status' => 200]);
    }

    /** 【 座位列表 （按人数筛选） 】 */
    public function places(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param = 'places'))->additional(['status' => 200]);
    }

    /** 【 座位列表--按人数筛选 】 */
    public function scrPlaces(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param = 'places'))->additional(['status' => 200]);
    }

    /** 【 座位列表--退菜 】 */
    public function retreatPlaces(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param = 'retreat'))->additional(['status' => 200]);
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
        return (new StoreCollection($store->menus()->where('status', 0)->get()))->additional(['status' => 200]);
    }

    /** 【 菜品列表--全部 】 */ 
    public function totalMenus(Request $request, Store $store)
    {
        return (new TagCollection($store->tags()->where('pid', 0)->where('category', 'class')->get()))->additional(['status' => 200]);
    }

    /** 【 在售、售罄菜品数量 】 */
    public function searchMenus(Store $store)
    {
        $all_number = $store->menus->filter(function ($item){
            if (MenuTag::where('menu_id', $item->id)->first()) {
                return $item;
            }
        })->count();
        $in_number = $store->menus()->where('status', 1)->get()->filter(function ($item){
            if (MenuTag::where('menu_id', $item->id)->first()) {
                return $item;
            }
        })->count();
        $out_number = $store->menus()->where('status', 0)->get()->filter(function ($item){
            if (MenuTag::where('menu_id', $item->id)->first()) {
                return $item;
            }
        })->count();
        return response()->json(['status' => 200, 'in_number' => $in_number, 'out_number' => $out_number, 'all_number' => $all_number]);
    }

    /** 【 门店售罄菜品一键恢复 】 */
    public function returnMenus(Store $store)
    {
        $store->menus()->update(['status' => 1]);
        return response()->json(['status' => 200, 'message' => '操作成功！']);
    } 

    /** 【 客户端--门店详情 】 */ 
    public function customerShow(Store $store)
    {
        return (new StoreResource($store))->additional(['status' => 200]);
    }

    /** 【 客户端--菜品列表--全部 】 */ 
    public function customerMenus(Request $request, Store $store)
    {
        return (new TagCollection($store->tags()->where('pid', 0)->where('category', 'class')->get()))->additional(['status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function customerPackages(Request $request, Store $store)
    {
        $where[] = ['category', 'p'];
        if ($request->type == 'in') {
            $where[] = ['status', 1];
        }
        return (new MenuCollection($store->menus()->where($where)->get()))->additional(['status' => 200]);
    }

    /** 【 客人数量 】 */
    public function guestNumber(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='guestNumber'))->additional(['status' => 200]);
    }

    /** 【 客人时刻 】 */ 
    public function guestMoment(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='guestMoment'))->additional(['status' => 200]);
    }

    /** 【 菜品排行 】 */
    public function menuRank(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='menuRank'))->additional(['status' => 200]);
    } 

    /** 【 金额排行 】 */ 
    public function moneyRank(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='menuRank'))->additional(['status' => 200]);
    }

    /** 【 桌位数量 】 */
    public function placeNumber(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='placeNumber'))->additional(['status' => 200]);
    }

    /** 【 占位时间 】 */
    public function placeHolder(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='placeHolder'))->additional(['status' => 200]);
    } 

    /** 【 出菜时间 】 */
    public function menuServed(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='menuServed'))->additional(['status' => 200]);
    }

    /** 【 员工表现 -- part one 】 */
    public function staffBehaviorPartOne(Request $request, Store $store)
    {
        if ($request->has('id')) {
            switch ($request->id) {
                case '0':
                    $where = [];
                    break;
                case '1':
                    $where[] = ['post', 'waiter'];
                    break;
                case '2':
                    $where[] = ['post', 'chef'];
                    break;
                case '3':
                    $where[] = ['post', 'manager'];
                    break;
            }
        }

        $users = $store->users()->where($where)->select('id', 'name', 'account', 'post')->get();
        
        // 构造数组
        $post = [
            [
                'id' => 0,
                'post' => '所有',
            ],
            [
                'id' => 1,
                'post' => 'waiter',
            ],
            [
                'id' => 2,
                'post' => 'chef',
            ],
            [
                'id' => 3,
                'post' => 'manager',
            ]
        ];
        return response()->json(['data' => $users, 'post' => $post, 'status' => 200]);
    }

    /** 【 员工表现 -- part two 】 */
    public function staffBehaviorPartTwo(Request $request, Store $store)
    {
        // 初始化变量
        $behaviors = array();
        $user = array();
        if ($request->has('id')) {
            $user = User::find($request->id);

            if ($request->exists('start_time') && $request->exists('end_time')) {
                $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];

                $behaviors = $user->behaviors()->whereBetween('created_at', $time)->selectRaw('category, count(*) as value')->groupBy('category')->get();
            }
        }
        return response()->json(['data' => $behaviors, 'user' => $user, 'status' => 200]);
    }

    /** 【 员工表现 -- part three 】 */
    public function staffBehaviorPartThree(Request $request, Store $store)
    {
        // 初始化变量
        $behaviors = array();
        $behaviorsList = array();
        if ($request->has('id')) {
            $user = User::find($request->id);

            if ($request->exists('start_time') && $request->exists('end_time')) {
                $time = [$request->start_time.' 00:00:00', $request->end_time.' 23:59:59'];

                $behaviors = $user->behaviors()->whereBetween('created_at', $time)->selectRaw('category, count(*) as value')->groupBy('category')->get()->map(function ($item) use ($request){
                    if ($request->category == $item['category']) {
                        return $item;
                    }
                })->filter()->values();

                $behaviorsList = $user->behaviors()->whereBetween('created_at', $time)->where('category', $request->category)->select('created_at')->get();
            }
        }
        return response()->json(['data' => $behaviorsList, 'title' => $behaviors, 'status' => 200]);
    }

    /** 【 财务报表 -- 收入 】 */
    public function income(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='income'))->additional(['status' => 200]);
    } 
}
