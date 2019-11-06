<?php

namespace App\Http\Controllers\Api;

use App\Models\{Menu, MenuTag};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{MenuResource, MenuCollection};

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
            foreach ($ids as $key => $value) {
                $menu->tags()->sync($value, false);
            }
        }
        
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '创建成功！']);
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
        $menu->update($request->all());

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '修改成功！']);
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

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 添加标签 】 */
    public function addTags(Request $request, Menu $menu)
    {
        $menu->tags()->wherePivot('pid',0)->attach($request->target_id, ['pid' => 0, 'order_number' => $request->order_number]);

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '添加成功！']);
    }

    /** 【 排序标签 】 */
    public function orderTags(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);

        // 循环嵌入
        foreach ($ids as $key => $value) {
            MenuTag::where('id',$value['id'])->update(['order_number' => $value['order_number']]);
        }

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '排序成功！']);
    }

    /** 【 删除标签 】 */
    public function subTags(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);

        foreach ($ids as $key => $value) {
            MenuTag::where('id',$value)->delete();
            MenuTag::where('pid',$value)->delete();
        }
    
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '删除成功！']);
    }

    /** 【 添加菜品 】 */
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

        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '保存成功！']);
    }

    /** 【 删除菜品 】 */
    public function subMenus(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids, true);

        foreach ($ids as $key => $value) {
            MenuTag::where('id',$value)->delete();
        }
    
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '删除成功！']);
    }

    /** 【 获取菜品列表 】 */ 
    public function getMenus(Request $request, Menu $menu)
    {
        $menuTag = MenuTag::find($request->id);
        $menus = $menu::find($menuTag->menu_id)->menus($request->id)->get();

        return (new MenuCollection($menus))->additional(['status' => 200, 'message' => '获取成功！']);
    }

    /** 【 菜品售罄、恢复 】 */
    public function saleStatus(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);

        $type = $request->type;

        switch ($type) {
            case 'out':
                foreach ($ids as $key => $value) {
                    $menu::where('id',$value)->update(['status' => 0]);
                }
                break;
            
            default:
                foreach ($ids as $key => $value) {
                    $menu::where('id',$value)->update(['status' => 1]);
                }
                break;
        }

        return response()->json(['message' => '修改成功！', 'status' => 200]);
    }
}