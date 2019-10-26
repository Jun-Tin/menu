<?php

namespace App\Http\Controllers\Api;

use App\Models\{Shopcart, Menu};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopcartResource;

class ShopcartsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => '菜品已售罄！', 'status' => 200]);
        }
        switch ($menu->category) {
            case 'o':
                $menu_price = $menu->original_price;
                break;
            default:
                $menu_price = $menu->special_price;
                break;
        }
        dd($menu->category);

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
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => '加入成功！']);
    }

    /** 【 客户端--创建购物车 】 */ 
    public function customerStore(Request $request, Shopcart $shopcart)
    {
        $menu = Menu::find($request->menu_id);

        if (!$menu->status) {
            return response()->json(['message' => '菜品已售罄！', 'status' => 200]);
        }
        switch ($menu->category) {
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
        $shopcart->save();

        return (new ShopcartResource($shopcart))->additional(['status' => 200, 'message' => '加入成功！']);
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
    public function update(Request $request, Shopcart $shopcart)
    {
        switch ($request->type) {
            case 'add':
                $shopcart->update([
                    'number' => $shopcart->number+1,
                    'price' => $shopcart->price*2
                ]);
                break;
            default:
                if ($shopcart->number == 1) {
                    $shopcart->delete();
                }

                $shopcart->update([
                    'number' => $shopcart->number-1,
                    'price' => $shopcart->price/2
                ]);
                break;
        }

        return response()->json(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 客户端--购物车增加、减少商品】 */ 
    public function customerUpdate(Request $request, Shopcart $shopcart)
    {
        switch ($request->type) {
            case 'add':
                $shopcart->update([
                    'number' => $shopcart->number+1,
                    'price' => $shopcart->price*2
                ]);
                break;
            default:
                if ($shopcart->number == 1) {
                    $shopcart->delete();
                }

                $shopcart->update([
                    'number' => $shopcart->number-1,
                    'price' => $shopcart->price/2
                ]);
                break;
        }

        return response()->json(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
