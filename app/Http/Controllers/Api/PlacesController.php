<?php

namespace App\Http\Controllers\Api;

use App\Models\{Place, Image, Menu, Tag, Order, User, OrderDetail, Shopcart, Behavior};
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
    public function store(Request $request, Place $place, Image $image)
    {        
        $place->fill($request->all());
        $place->status = 0;
        $place->save();
        $place->updateQrcode($request->all(),$place->id);

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
        $place->updateQrcode($request->all(),$place->id);
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
    public function shopcart(Request $request, Place $place)
    {
        $shopcarts = $place->shopcarts;
        $new = $shopcarts->map(function ($item, $key){
            $item->menu_name = (Menu::find($item->menu_id, ['name']))->name;
            if ($item->menus_id) {
                $item->menus_name = Menu::find(json_decode($item->menus_id))->pluck('name');
            }

            if ($item->tags_id) {
                foreach (json_decode($item->tags_id) as $k => $value) {
                    $name[] = Tag::find($value)->pluck('name');
                }
            }
            $item->tags_name = $name;
            $item->fill_price = json_decode($item->fill_price);

            return $item;
        });

        $total = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->price;
        });

        return response()->json(['data' => $new->all(), 'status' => 200, 'count' => count($shopcarts), 'total' => $total]);
    } 

    /** 【 创建订单 】 */
    public function order(Request $request, Place $place, User $user)
    {
        $shopcarts = $place->shopcarts;
        if ($shopcarts->isEmpty()) {
            return response()->json(['error' => ['message' => ['购物车为空！']], 'status' => 404]);
        }

        $total = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->price;
        });

        if ($request->id) {
            $order = Order::find($request->id);
            // 修改订单金额
            $order->update([
                'price' => $order->price+$total,
                'final_price' => $order->price+$total,
                'number' => $order->number+count($shopcarts),
                'final_number' => $order->number+count($shopcarts)
            ]);
            $only_order = $order->order;
        } else {
            $only_order = date('YmdHis').$user->random();
            // 创建订单信息
            $order = Order::create([
                'order' => $only_order,
                'store_id' => $place->store_id,
                'place_id' => $place->id,
                'price' => $total,
                'final_price' => $total,
                'number' => count($shopcarts),
                'final_number' => count($shopcarts),
                'status' => 0,
            ]);
        }
        // 循环创建订单详情
        $new = $shopcarts->map(function ($item, $key) use ($only_order){
            OrderDetail::create([
                'order_order' => $only_order,
                'menu_id' => $item->menu_id,
                'menus_id' => $item->menus_id,
                'tags_id' => $item->tags_id,
                'fill_price' => $item->fill_price,
                'number' => $item->number,
                'price' => $item->price,
                'status' => 0,
            ]);
            
            // 删除购物车记录
            Shopcart::where('id',$item->id)->delete();    
        });

        // 修改座位状态
        $place->update(['status'=>1]);
        // 记录员工行为
        Behavior::create([
            'user_id' => auth()->id(),
            'target_id' => $order->id,
            'category' => 'order',
            'status' => 1,
        ]);

        return response()->json(['id' => $order->id, 'status' => 200, 'message' => '下单成功！']);
    } 
}
