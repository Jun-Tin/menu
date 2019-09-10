<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{PackageResource, PackageCollection};

class PackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Package $package)
    {
        return (new PackageResource($package))->additional(['status' => 200]);
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
    public function store(Request $request, Package $package)
    {
        $package->fill($request->all());
        $package->save();
        // group_concat
        // $package->save();
        // dd(json_decode($request->foods,true));
        // $groups = json_decode($request->foods,true);

        // 循环对应不同差价
        // for ($i=0; $i < count($groups) ; $i++) {
        //     $package->groups()->attach([explode(',', $request->menu_id)[$i] => ['fill_price' => explode(',', $request->fill_price)[$i]]]);
        // }

        return (new PackageResource($package))->additional(['status' => 200]);
    }

    /** 【套餐菜品设置】 */
    // public function create()
    // {

    // } 

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, Package $package)
    {
        $package->update($request->all());
        // $package->menus()->detach();//先删除关系
        // 循环对应不同差价
        // for ($i=0; $i < count(explode(',', $request->menu_id)) ; $i++) {
        //     $package->menus()->attach([explode(',', $request->menu_id)[$i] => ['fill_price' => explode(',', $request->fill_price)[$i]]]);
        // }

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Package $package)
    {
        $ids = json_decode($request->ids);
        // 循环删除
        foreach ($ids as $key => $value) {
            $package = $package::find($value);
            $package->menus()->detach();
            $package->delete();
        }

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 套餐嵌入标签 】 */
    public function addTags(Request $request, Package $package)
    {
        // dd($package);
        $package->tags()->attach($request->target_id, ['pid' => 0, 'order_number' => $request->order_number]);

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 删除嵌入标签 】 */
    public function subTags(Request $request, Package $package)
    {
        $package->tags()->detach($request->target_id);
    
        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 嵌入标签排序 】 */
    public function orderTags(Request $request, Package $package)
    {
        $ids = json_decode($request->ids, true);

        $package->tags()->detach(); // 先删除原有关系
        // 循环嵌入
        foreach ($ids as $key => $value) {
            $package->tags()->attach($value['id'], ['pid' => 0, 'order_number' => $value['order_number']]);
        }

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '修改成功！']);
    }
}
