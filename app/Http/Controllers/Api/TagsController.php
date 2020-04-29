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
        return (new TagCollection($store->tags()->where('category', $request->category)->where('pid', $request->pid)->get()))->additional(['status' => 200]);
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
        if ($request->category == 'class') {
           $tag->order_number = Tag::max('id')+ 1;
        }
        $tag->save();

        return (new TagResource($tag))->additional(['status' => 200, 'message' => __('messages.store')]);
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

        return (new TagResource($tag))->additional(['status' => 200, 'message' => __('messages.update')]);
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

        return response()->json(['status' => 200, 'message' => __('messages.destroy')]);
    }

    /** 【 菜品列表--标签 】 */ 
    public function menus(Request $request, Tag $tag)
    {
        return (TagResource::collection($tag->menus()->where('status', 1)->get()))->additional(['status' => 200]);
    }

    /** 【 排序标签 】 */
    public function order(Request $request, Tag $tag)
    {
        // 操作的序列号
        $order_number1 = Tag::where('id', $request->id)->value('order_number');
        // 交换的序列号
        $order_number2 = Tag::where('id', $request->ids)->value('order_number');
        Tag::where('id', $request->id)->update(['order_number' => $order_number2]);
        Tag::where('id', $request->ids)->update(['order_number' => $order_number1]);
        return response()->json(['status' => 200, 'messages' => __('messages.update')]);
    } 
}
