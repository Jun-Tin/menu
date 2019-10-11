<?php

namespace App\Http\Controllers\Api;

use App\Models\{Tag, Store};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{TagResource, TagCollection};

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $store = Store::find($request->store_id);

        // return (new TagCollection($user->tags()->where('category',$request->category)->where('pid',$request->pid)->get()))->additional(['status' => 200]);
        return (new TagCollection($store->tags()->where('category',$request->category)->where('pid',$request->pid)->get()))->additional(['status' => 200]);
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
    public function store(Request $request, Tag $tag)
    {
        $tag->fill($request->all());
        $tag->save();

        return (new TagResource($tag))->additional(['status' => 200, 'message' => '创建成功！']);
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
    public function update(Request $request, Tag $tag)
    {
        $tag->update($request->all());

        return (new TagResource($tag))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Tag $tag)
    {
        $ids = json_decode($request->ids);

        // 循环删除
        foreach ($ids as $key => $value) {
            $tag::where('id', $value)->first()->menus()->detach();
            $tag::where('id', $value)->delete();
        }

        return response()->json(['status' => 200, 'message' => '删除成功！']);
    }

    /** 【 菜品列表--标签 】 */ 
    public function menus(Request $request, Tag $tag)
    {
        return (new TagCollection($tag->menus()->where('category','m')->where('status',1)->get()))->additional(['status' => 200]);
    }
}
