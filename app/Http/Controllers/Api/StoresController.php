<?php

namespace App\Http\Controllers\Api;

use App\Models\{Store, User, MenuTag, Bill, Period, Place};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, StoreCollection, PlaceCollection, UserResource, BookResource, MenuCollection, TagCollection, StatisticsResource, AreaResource, StoreAreaResource, LanguageResource};
use Carbon\Carbon;

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
        $store->fill($request->all());
        $store->user_id = auth()->id();
        // switch ($request->category) {
        //     case 0:
                // 先给定默认值，后期做修改（删除）
                $store->clean = 1;
                $store->settle = 1;
                // break;
        //     case 1:
        //         $store->open = $request->open;
        //         break;
        // }
        $store->save();

        return (new StoreResource($store))->additional(['status' => 200, 'message' => __('messages.store')]);
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

        return (new StoreResource($store))->additional(['status' => 200, 'message' => __('messages.update')]);
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

        return response()->json(['message' => __('messages.destroy'), 'status' => 200]);
    }

    /** 【 门店区域列表 】 */
    public function area(Request $request, Store $store)
    {
        return (AreaResource::collection($store->areas))->additional(['area' => $store->area, 'status' => 200]); 
    } 

    /** 【 门店预约二维码 】 */
    public function bookQrcode(Request $request, Store $store)
    {
        return (new StoreAreaResource($store->area))->additional(['status' => 200]);
    }

    /** 【 刷新门店二维码 -- 排队/大屏幕 】 */ 
    public function refresh(Request $request, Store $store, Place $place)
    {
        switch ($request->category) {
            case 'screen':
                $data = [
                    'type' => 'store',
                    'name' => $store->name.'_screen',
                    'store_id' => $store->id,
                    'category' => 'screen',
                ];
                break;
            case 'line':
                $data = [
                    'type' => 'store',
                    'name' => $store->name.'_line',
                    'store_id' => $store->id,
                    'category' => 'line',
                ];
                break;
            case 'book':
                $data = [
                    'type' => 'store',
                    'name' => $store->name.'_book',
                    'store_id' => $store->id,
                    'category' => 'book',
                ];
                break;
        }
        $result = $place->updateQrcode($data,$store->id);
        $store->area->update([
            $request->category.'_qrcode' => $result['qrcode'],
            $request->category.'_link' => $result['link']
        ]);

        return response()->json(['message' => __('messages.refresh'), 'status' => 200]);
    }

    /** 【 激活门店 】 */
    public function active(Request $request, Store $store, User $user)
    {
        $user = auth()->user();
        // 上线周期
        $period = Period::find($request->id);
        if ($user->coins - $period->number <0) {
            return response()->json(['error' => ['message' => [__('messages.coins')]], 'status' => 202]);
        }
        $user->decrement('coins', $period->number);
        if ($store->days == 0 && empty($store->actived_at)) {
            $days = $period->days+1;
            $store->increment('days', $days);
        } else {
            $days = $period->days;
            $store->increment('days', $days);
        }
        $store->update(['active' => 1, 'actived_at' => Carbon::now()->toDateTimeString()]);
        
        // 写入记录
        Bill::create([
            'title' => '激活门店',
            'order' => 'Act'.date('YmdHis').$user->random(),
            'operate' => $user->id,
            'accept' => '系统',
            'target' => $store->id,
            'execute' => 0,
            'type' => 0,
            'number' => $period->number,
            'method' => 7,
            'category' => 1,
        ]);

        return (new StoreResource($store))->additional(['status' => 200, 'message' => __('messages.upline')]);
    } 

    /** 【 菜品列表 】 */
    public function menus(Store $store)
    {
        return (new StoreCollection($store->menus()->where('category', 'm')->orderBy('id', 'desc')->get()))->additional(['status' => 200]);
    }

    /** 【 套餐列表 】 */
    public function packages(Request $request, Store $store)
    {
        $where[] = ['category', 'p'];
        if ($request->type == 'in') {
            $where[] = ['status', 1];
        }
        return (new MenuCollection($store->menus()->where($where)->orderBy('id', 'desc')->get()))->additional(['status' => 200]);
    }

    /** 【 座位列表 （按人数筛选） 】 */
    public function places(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param='places'))->additional(['status' => 200]);
    }

    /** 【 座位列表--按人数筛选 】 */
    public function scrPlaces(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param='places'))->additional(['status' => 200]);
    }

    /** 【 座位列表--退菜 】 */
    public function retreatPlaces(Request $request, Store $store)
    {
        // $param 自定义格外参数，用于resource区别不同数据
        return (new PlaceCollection($store->places()->where('floor', 0)->get(), $param='retreat'))->additional(['status' => 200]);
    } 

    /** 【 员工列表 -- 服务员 】 */
    public function users(Store $store)
    {
        return UserResource::collection($store->users()->where('post', 'waiter')->get())->additional(['status' => 200]);
    }

    /** 【 员工列表 -- 后厨 】 */
    public function chef(Store $store)
    {
        return (new UserResource($store->users()->where('post', 'chef')->first()))->additional(['status' => 200]);
    }

    /** 【 删除座位--整层 】 */
    public function delete(Request $request, Store $store)
    {
        Storage::disk('qrcodes')->deleteDirectory($store->id.'/'.$request->floor);

        $store->places()->where('id', $request->floor)->delete();
        $store->places()->where('floor', $request->floor)->delete();

        return response()->json(['message' => __('messages.destroy'), 'status' => 200]);
    }

    /** 【 预约列表 】 */
    public function book(Request $request, Store $store)
    {        
        return BookResource::collection($store->books)->additional(['status' => 200]);
    }

    /** 【 手机号码 】 */
    public function phone(Request $request, Store $store)
    {
        $collection = collect([$store->lines, $store->books])->collapse();
        return BookResource::collection($collection->unique('phone')->values())->additional(['status' => 200]);
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
        return response()->json(['status' => 200, 'message' => __('messages.do')]);
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
        return (new MenuCollection($store->menus()->where($where)->orderBy('id', 'desc')->get()))->additional(['status' => 200]);
    }

    /** 【 收入报表 】 */ 
    // 总月
    public function totalMonthIncome(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='totalMonthIncome'))->additional(['status' => 200]);
    }
    // 每月
    public function eachMonthIncome(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='eachMonthIncome'))->additional(['status' => 200]);
    }
    // 每天
    public function eachDayIncome(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='eachDayIncome'))->additional(['status' => 200]);
    }
    // 总周
    public function totalWeekIncome(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='totalWeekIncome'))->additional(['status' => 200]);
    }
    // 每周
    public function eachWeekIncome(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='eachWeekIncome'))->additional(['status' => 200]);
    }

    /** 【 客人报表 】 */
    // 时段
    public function guestMoment(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='guestMoment'))->additional(['status' => 200]);
    } 

    /** 【 菜品报表 】 */
    // 排行
    public function menuRank(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='menuRank'))->additional(['status' => 200]);
    } 

    /** 【 桌位报表 】 */
    // 排行
    public function placeRank(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='placeRank'))->additional(['status' => 200]);
    }

    /** 【 员工报表 】 */
    // 服务
    public function staffService(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='staffService'))->additional(['status' => 200]);
    } 

    /** 【 后厨报表 】 */
    // 出菜时间
    public function menuServed(Request $request, Store $store)
    {
        return (new StatisticsResource($store, $param='menuServed'))->additional(['status' => 200]);
    }

    /** 【 获取门店设置语言 】 */
    public function language(Request $request, Store $store)
    {
        // 通过全局辅助函数...
        session(['locale' => $store->language->name_en]);
        return (new LanguageResource($store->language))->additional(['status' => 200]);
    }

    /** 【 获取门店设置币种 】 */
    public function currency(Request $request, Store $store)
    {
        return (new CurrenyResource($store->currency))->additional(['status' => 200]);
    }



    /** 【 定时计算上线天数 -- 每天递减1 】 */ 
    public function computeDays()
    {
        Store::where('active', 1)->get()->map(function ($item){
            if (($item->days -1) <= 0) {
                $item->update(['active' => 0, 'days' => 0, 'actived_at' => NULL]);
            } else {
                $item->decrement('days', 1);
            }
        });
    }
}
