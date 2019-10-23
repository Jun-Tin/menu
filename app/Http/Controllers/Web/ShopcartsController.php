<?php

namespace App\Http\Controllers\Web;

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
        dd($request);
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
    public function update(Request $request, $id)
    {
        //
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
