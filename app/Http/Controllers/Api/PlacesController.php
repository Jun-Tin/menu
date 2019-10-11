<?php

namespace App\Http\Controllers\Api;

use App\Models\{Place, Image, Menu, Tag};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, File};
use App\Http\Controllers\Controller;
use App\Http\Resources\{PlaceResource, ShopcartResource};
use Chumper\Zipper\Zipper;

class PlacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
    public function store(Request $request, Place $place)
    {        
        $place->fill($request->all());
        $place->save();

        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '创建成功！']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Place $place)
    {

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
    public function update(Request $request, Place $place)
    {
        $place->update($request->all());

        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Place $place)
    {
        $images = Image::find($place->image_id);
        $pos = strpos($images->path, 'images');
        $path = substr($images->path,$pos,strlen($images->path));
        if (file_exists($path)) {
            unlink($path);
        }
        $place->delete();

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    // /**【删除座位--整层】*/
    // public function delete(Request $request, Place $place)
    // {
    //     // dd($request->store_id);
    //     // dd(Storage::disk('qrcodes'));
    //     // dd(public_path('/images/uploads/201909'));
    //     // $file = Storage::delete('/uploads/201909/');
    //     Storage::disk('qrcodes')->deleteDirectory('1/1');
    //     // \File::delete(public_path('/images/uploads/201909/'));
    //     // $file = File::delete();
    //     // dd($disk);
    //     dd(123);

    //     $place->where('floor', $request->floor)->delete();

    //     return response()->json(['message' => '删除成功！', 'status' => 200]);
    // }

    /** 【 获取压缩包 】 */
    public function makeZip(Request $request)
    {
        $zipname = date('YmdHis') . uniqid() . '.zip';
        $dir = public_path('zips');
        if (!is_dir($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
        $zipper = new Zipper();
        $arr = glob(public_path('images/qrcodes/'. $request->store_id . '/' . $request->floor));
        $zipper->make($dir . '/' . $zipname)->add($arr)->close();

        if (file_exists($dir. '/' .$zipname)) {
            // return response()->json(['message' => '压缩成功！', 'status' => 200, 'url' => env('APP_URL').'/zips/' .$zipname]);
            return response()->download($dir . '/' . $zipname)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => ['message' => '压缩失败'], 'status' => 201]);
    }

    /** 【 创建楼层 】 */
    public function addFloor(Request $request, Place $place)
    {
        $place->fill($request->all());
        $place->floor = 0;
        $place->save();

        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '创建成功！']);
    }

    /** 【 修改楼层 】 */
    public function editFloor(Request $request, Place $place)
    {
        $place->update($request->all());

        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 购物车详情 】 */
    public function shopcart(Request $request, Place $place, $total)
    {
        $shopcarts = $place->shopcarts;
        $new = $shopcarts->map(function ($item, $key){
            $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
            $item->menus_id = json_decode($item->menus_id);
            if ($item->menus_id) {
                $item->menus_name = Menu::find($item->menus_id)->pluck('name');
            }

            $item->tags_id = json_decode($item->tags_id);
            if ($item->tags_id) {
                foreach ($item->tags_id as $k => $value) {
                    $name[] = Tag::find($value)->pluck('name');
                }
            }
            $item->tags_name = $name;

            $item->fill_price = json_decode($item->fill_price);

            $total += $item->price; 
            dd($total);
            return $item;
        });

        return response()->json(['data' => $new->all(), 'status' => 200, 'count' => count($shopcarts)]);
    } 
}
