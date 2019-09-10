<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
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
    public function store(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);
        $menu->fill($request->all());
        $menu->save();
        
        foreach ($ids as $key => $value) {
            $menu->tags()->sync($value, false);
        }
        
        return (new MenuResource($menu))->additional(['status' => 200, 'message' => '创建成功！']);
    }

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
    public function update(Request $request, Menu $menu)
    {
        $ids = json_decode($request->ids);

        $menu->update($request->all());
        $menu->tags()->detach();//先删除关系

        foreach ($ids as $key => $value) {
            $menu->tags()->sync($value, false);
        }

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
            // $menu->tags()->detach();
            // $menu->delete();
            // $menu::find($value)->tags()->detach();
            // $menu->delete();
            $menu->tags()->detach();
            $menu->delete();
        }

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }
}
