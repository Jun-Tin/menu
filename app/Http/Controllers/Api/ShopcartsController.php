<?php

namespace App\Http\Controllers\Api;

use App\Models\{Shopcart, Menu, Tag};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopcartResource;

class ShopcartsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => __('messages.sale_out'), 'status' => 200]);
        }
        switch ($menu->type) {
            case 'o':
                $menu_price = $menu->original_price;
                break;
            default:
                $menu_price = $menu->special_price;
                break;
        }

        $shopcart->fill($request->all());
        $shopcart->number = 1;
        if ($request->has('fill_price')) {
            $data = json_decode($request->fill_price);
            foreach ($data as $key => $value) {
                $shopcart->price += $value;
            }
        }

        $shopcart->original_price = $menu_price;
        $shopcart->price = $shopcart->price+$menu_price;
        $shopcart->category = $menu->category;
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => __('messages.add')]);
    }

    /** 【 客户端--创建购物车 】 */ 
    public function customerStore(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => __('messages.sale_out'), 'status' => 200]);
        }
        switch ($menu->type) {
            case 'o':
                $menu_price = $menu->original_price;
                break;
            default:
                $menu_price = $menu->special_price;
                break;
        }

        $shopcart->fill($request->all());
        $shopcart->number = 1;
        if ($request->has('fill_price')) {
            $data = json_decode($request->fill_price);
            foreach ($data as $key => $value) {
                $shopcart->price += $value;
            }
        }
        
        $shopcart->original_price = $menu_price;
        $shopcart->price = $shopcart->price+$menu_price;
        $shopcart->category = $menu->category;     
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => __('messages.add')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shopcart $shopcart)
    {
        // 商品单价
        $original_price = $shopcart->price/$shopcart->number;
        switch ($request->type) {
            case 'add':
                $shopcart->update([
                    'number' => $shopcart->number+1,
                    'price' => $shopcart->price+$original_price
                ]);
                break;
            default:
                if ($shopcart->number == 1) {
                    $shopcart->delete();
                }
                $shopcart->update([
                    'number' => $shopcart->number-1,
                    'price' => $shopcart->price-$original_price
                ]);
                break;
        }

        return response()->json(['status' => 200, 'message' => __('messages.update')]);
    }

    /** 【 客户端--购物车增加、减少商品】 */ 
    public function customerUpdate(Request $request, Shopcart $shopcart)
    {
        // 商品单价
        $original_price = $shopcart->price/$shopcart->number;
        switch ($request->type) {
            case 'add':
                $shopcart->update([
                    'number' => $shopcart->number+1,
                    'price' => $shopcart->price+$original_price
                ]);
                break;
            default:
                if ($shopcart->number == 1) {
                    $shopcart->delete();
                }
                $shopcart->update([
                    'number' => $shopcart->number-1,
                    'price' => $shopcart->price-$original_price
                ]);
                break;
        }

        return response()->json(['status' => 200, 'message' => __('messages.update')]);
    }

    /** 【 创建购物车（加入商品 -- 直接点击‘+’添加） 】 */ 
    public function created(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => __('messages.sale_out'), 'status' => 200]);
        }
        switch ($menu->type) {
            case 'o':
                $menu_price = $menu->original_price;
                break;
            default:
                $menu_price = $menu->special_price;
                break;
        }

        $shopcart->fill($request->all());
        $shopcart->number = 1;
        $colletion = $menu->tags()->where('category', 'perfer')->get()->map(function ($item){
            $item->tags = Tag::where('pid', $item->id)->get()->first();
            return $item->only('tags');
        });
        $colletion->map(function ($item) use ($shopcart){
            if (!empty($item['tags'])) {
                $shopcart->tags_id .= $item['tags']['id'].',';
            }
        });
        $shopcart->tags_id = '[['.substr($shopcart->tags_id, 0, -1).']]';
        $shopcart->original_price = $menu_price;
        $shopcart->price = $menu_price;
        $shopcart->category = $menu->category;
        $shopcart->remark = '[""]';
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => __('messages.add')]);
    }

    /** 【 客户端--创建购物车（加入商品 -- 直接点击‘+’添加） 】 */ 
    public function customerCreated(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => __('messages.sale_out'), 'status' => 200]);
        }
        switch ($menu->type) {
            case 'o':
                $menu_price = $menu->original_price;
                break;
            default:
                $menu_price = $menu->special_price;
                break;
        }

        $shopcart->fill($request->all());
        $shopcart->number = 1;
        $colletion = $menu->tags()->where('category', 'perfer')->get()->map(function ($item){
            $item->tags = Tag::where('pid', $item->id)->get()->first();
            return $item->only('tags');
        });
        $colletion->map(function ($item) use ($shopcart){
            if (!empty($item['tags'])) {
                $shopcart->tags_id .= $item['tags']['id'].',';
            }
        });
        $shopcart->tags_id = '[['.substr($shopcart->tags_id, 0, -1).']]';
        $shopcart->original_price = $menu_price;
        $shopcart->price = $menu_price;
        $shopcart->category = $menu->category;
        $shopcart->remark = '[""]';
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => __('messages.add')]);
    }

    /** 【 创建购物车（减少商品 -- 直接点击‘-’减少） 】 */ 
    public function reduced(Request $request, Shopcart $shopcart)
    {
        $id = Shopcart::where('place_id', $request->place_id)->where('menu_id', $request->menu_id)->value('id');
        Shopcart::where('id', $id)->delete();
        $shopcart = Shopcart::where('place_id', $request->place_id)->get();

        return (ShopcartResource::collection($shopcart))->additional(['status' => 200, 'message' => __('messages.reduce')]);
    }

    /** 【 客户端--创建购物车（减少商品 -- 直接点击‘-’减少） 】 */ 
    public function customerReduced(Request $request, Shopcart $shopcart)
    {
        $id = Shopcart::where('place_id', $request->place_id)->where('menu_id', $request->menu_id)->value('id');
        Shopcart::where('id', $id)->delete();
        $shopcart = Shopcart::where('place_id', $request->place_id)->get();

        return (ShopcartResource::collection($shopcart))->additional(['status' => 200, 'message' => __('messages.reduce')]);
    }
}
