<?php

namespace App\Http\Controllers\Api;

use App\Models\{Menu, MenuTag, Tag};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{MenuResource, MenuCollection, StoreCollection, TagResource};
use GatewayWorker\Lib\Gateway;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Menu $menu)
    {        
        return (new MenuResource($menu))->additional(['status' => 200]);
    }

    /** 【 菜品详情--客户端 】 */ 
    public function customerIndex(Menu $menu)
    {
        return (new MenuResource($menu))->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);
        $menu->fill($request->all());
        $menu->save();
        
        if ($ids) {
            // 获取菜品跟标签关系
            $order_number = MenuTag::orderBy('id', 'desc')->value('id');
            for ($i = 0; $i < count($ids); $i++) { 
                $menu->tags()->attach($ids[$i], ['order_number' => $order_number+ $i+1]);
            }
        }
        
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.store')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        if ($request->category == 'm') {
            $ids = json_decode($request->ids);
            $menu->tags()->detach();
            if ($ids) {
                // 获取菜品跟标签关系
                $order_number = MenuTag::orderBy('id', 'desc')->value('id');
                for ($i = 0; $i < count($ids); $i++) { 
                    $menu->tags()->attach($ids[$i], ['order_number' => $order_number+ $i+1]);
                }
            }
        }
        $menu->update($request->all());

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);
        // 循环删除
        foreach ($ids as $key => $value) {
            $menu = $menu::find($value);
            $menu->tags()->detach();
            $menu->delete();
        }

        return response()->json(['message' => __('messages.destroy'), 'status' => 200]);
    }

    /** 【 修改菜品 —— 不修改标签关系 】 */
    public function edit(Request $request, Menu $menu)
    {
        $menu->update($request->all());
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.update')]);
    } 

    /** 【 菜品售罄、恢复 -- 多选 】 */
    public function saleStatus(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);

        switch ($request->type) {
            case 'out':
                $menu::whereIn('id', $ids)->update(['status' => 0]);
                break;
            
            default:
                $menu::whereIn('id', $ids)->update(['status' => 1]);
                break;
        }
        return response()->json(['message' => __('messages.update'), 'status' => 200]);
    }

    /** 【 菜品售罄、恢复 -- 单选 】 */
    public function soldStatus(Request $request, Menu $menu)
    {
        $user = auth()->user();
        switch ($request->type) {
            case 'out':
                $menu->update(['status' => 0]);
                break;
            default:
                $menu->update(['status' => 1]);
                break;
        }

        switch ($user->post) {
            case 'waiter':
                Gateway::sendToGroup('chef_'.$user->store_id, json_encode(array('type' => 'saleStatus', 'message' => '菜品销售状态改变！', JSON_UNESCAPED_UNICODE)));
                break;
            case 'chef':
                Gateway::sendToGroup('waiter_'.$user->store_id, json_encode(array('type' => 'saleStatus', 'message' => '菜品销售状态改变！', JSON_UNESCAPED_UNICODE)));
                break;
        }
        return response()->json(['message' => __('messages.update'), 'status' => 200]);
    }

    /** 【 新套餐 -- 添加标签 】 */
    public function addTags(Request $request, Menu $menu)
    {
        // 获取菜品跟标签关系
        $order_number = MenuTag::orderBy('id', 'desc')->value('id');
        $menu->tags()->wherePivot('pid', 0)->attach(0, ['pid' => 0, 'order_number' => $order_number+1]);

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.add')]);
    } 

    /** 【 新套餐 -- 修改标签 】 */ 
    public function editTags(Request $request, Menu $menu)
    {
        $menu->menuTag()->where('id', $request->tags_id)->update(['name' => $request->name]);

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /** 【 新套餐 -- 排序标签 】 */
    public function orderTags(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);

        // 循环嵌入
        foreach ($ids as $key => $value) {
            MenuTag::where('id', $value['id'])->update(['order_number' => $value['order_number']]);
        }

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.sort')]);
    }

    /** 【 新套餐 -- 删除标签 】 */
    public function subTags(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);

        MenuTag::whereIn('id', $ids)->delete();
        MenuTag::whereIn('pid', $ids)->delete();
    
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.destroy')]);
    }

    /** 【 新套餐 -- 添加菜品 】 */
    public function addMenus(Request $request, Menu $menu)
    {
        $data = json_decode($request->data, true);
        $menuTag = MenuTag::find($request->id);
        $menu = $menu::find($menuTag->menu_id);

        // 先解除原有数据
        $menu->menus($request->id)->detach();
        foreach ($data as $key => $value) {
            if ($value['id']) {
                $menu->menus($request->id)->attach($value['id'], ['pid' => $request->id, 'fill_price' => $value['fill_price']]);
            }
        }

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.update')]);
    }

    /** 【 新套餐 -- 删除菜品 】 */
    public function subMenus(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);
        MenuTag::whereIn('id', $ids)->delete();
    
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => __('messages.destroy')]);
    }

    /** 【 新套餐 -- 获取菜品列表 】 */ 
    public function getMenus(Request $request, Menu $menu)
    {
        $menuTag = MenuTag::find($request->id);
        $menus = $menu::find($menuTag->menu_id)->menus($request->id)->get();

        return (new StoreCollection($menus))->additional(['status' => 200]);
    }

    /** 【 修改菜品排序 】 */
    public function upDown(Request $request)
    {   
        // 操作的序列号
        $order_number1 = MenuTag::where('id', $request->pivot_id)->value('order_number');
        // 交换的序列号
        $order_number2 = MenuTag::where('id', $request->pivot_ids)->value('order_number');
        MenuTag::where('id', $request->pivot_id)->update(['order_number' => $order_number2]);
        MenuTag::where('id', $request->pivot_ids)->update(['order_number' => $order_number1]);
        $tag = Tag::find($request->taget_id);
        return (TagResource::collection($tag->menus()->where('category', 'm')->where('status', 1)->get()))->additional(['status' => 200]);
    }
}