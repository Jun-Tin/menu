<?php

namespace App\Http\Controllers\Api;

use App\Models\{Package, PackageGroup};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{PackageResource, PackageCollection, MenuCollection};

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

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '创建成功！']);
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
            PackageGroup::where('package_id',$value)->delete();
            $package->delete();
        }

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 添加标签 】 */
    public function addTags(Request $request, Package $package)
    {
        $package->tags()->wherePivot('pid',0)->attach($request->target_id, ['pid' => 0, 'order_number' => $request->order_number]);

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '添加成功！']);
    }

    /** 【 排序标签 】 */
    public function orderTags(Request $request, Package $package)
    {
        $ids = json_decode($request->ids, true);

        // $package->tags()->detach(); // 先删除原有关系
        // 循环嵌入
        foreach ($ids as $key => $value) {
            // $package->tags()->wherePivot('pid',0)->attach($value['id'], ['pid' => 0, 'order_number' => $value['order_number']]);
            PackageGroup::where('id',$value['id'])->update(['order_number' => $value['order_number']]);
        }

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '排序成功！']);
    }

    /** 【 删除标签 】 */
    public function subTags(Request $request, Package $package)
    {
        $ids = json_decode($request->ids, true);

        foreach ($ids as $key => $value) {
            // $package->tags()->wherePivot('pid',0)->detach($value);
            // $package->tags()->wherePivot('pid',0)->detach($value);
            PackageGroup::where('id',$value)->delete();
            PackageGroup::where('pid',$value)->delete();
        }
    
        // return response()->json(['message' => '删除成功！', 'status' => 200]);
        return (new PackageResource($package))->additional(['status' => 200, 'message' => '删除成功！']);
    }

    /** 【 添加菜品 】 */
    public function addMenus(Request $request, Package $package)
    {
        $data = json_decode($request->data, true);
        $packagegroup = PackageGroup::find($request->id);
        $package = $package::find($packagegroup->package_id);

        // 先解除原有数据
        $package->menus($request->id)->detach();
        foreach ($data as $key => $value) {
            $package->menus($request->id)->attach($value['id'], ['pid' => $request->id, 'fill_price' => $value['fill_price']]);
        }

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '添加成功！']);
    }

    /** 【 排序菜品 】 */
    public function orderMenus(Request $request, Package $package)
    {
        $ids = json_decode($request->ids, true);
        // $packagegroup = PackageGroup::find($request->id);
        // $package = $package::find($packagegroup->package_id);

        // 循环修改
        foreach ($ids as $key => $value) {
            $packagegroup = PackageGroup::find($value['id']);
            $packagegroup->order_number = $value['order_number'];
            $packagegroup->save();
        }

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '排序成功！']);
    }

    /** 【 修改菜品 】 */
    public function editMenus(Request $request, Package $package)
    {
        $packagegroup = PackageGroup::find($request->id);
        $package = Package::find($packagegroup->package_id);

        PackageGroup::where('id',$request->id)->update([
            'target_id' => $request->target_id,
            'fill_price' => $request->fill_price,
        ]);

        return (new PackageResource($package))->additional(['status' => 200, 'message' => '添加成功！']);
    }

    /** 【 删除菜品 】 */
    public function subMenus(Request $request, Package $package)
    {
        $ids = json_decode($request->ids, true);

        foreach ($ids as $key => $value) {
            PackageGroup::where('id',$value)->delete();
        }
    
        // return response()->json(['message' => '删除成功！', 'status' => 200]);
        return (new PackageResource($package))->additional(['status' => 200, 'message' => '删除成功！']);
    }

    /** 【 获取菜品列表 】 */ 
    public function getMenus(Request $request, Package $package)
    {
        $packagegroup = PackageGroup::find($request->id);
        $menus = Package::find($packagegroup->package_id)->menus($request->id)->get();

        return (new MenuCollection($menus))->additional(['status' => 200, 'message' => '获取成功！']);
    }
}
