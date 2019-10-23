<?php

namespace App\Http\Controllers\Web;

use App\Models\{Store, Tag, Image, Menu};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\{StoreResource, ShopcartResource};

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Store $store)
    {
        dd($store);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customerShow(Store $store)
    {
        return (new StoreResource($store))->additional(['status'=>200]);
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

    /** 【 菜品列表--全部 】 */ 
    public function menus(Request $request, Store $store)
    {
        $all = $store->tags()->where('pid',0)->where('category', 'class')->get();
        $new = $all->map(function ($item, $key){
            $item->menus = Tag::find($item->id)->menus()->where('category','m')->where('status',1)->get();
            $item->menus->map(function ($item){
                $item->image = Image::find($item->image_id);
            });
            return $item;
        });

        return response()->json(['data' => $new->all(), 'status' => 200]);
    }
}
