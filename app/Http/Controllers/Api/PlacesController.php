<?php

namespace App\Http\Controllers\Api;

use App\Models\{Place, Order, User, OrderDetail, Behavior};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, File, Redis};
use App\Http\Controllers\Controller;
use App\Http\Resources\{PlaceResource, ShopcartResource, ShopcartCollection};
use Chumper\Zipper\Zipper;
use GatewayWorker\Lib\Gateway;

class PlacesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Place $place)
    {
        dd(Redis::get('keys*'));
        $place->fill($request->all());
        $place->status = 0;
        $place->save();

        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '创建成功！']);
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
        $path = $place->image->path;
        $str = substr($path, strripos($path, "images"));
        if (file_exists($str)) {
            unlink($str);
        }
        $data = $request->all();
        // 默认类型值
        $data['type'] = 'place';
        $place->updateQrcode($data,$place->id);
        $place->update($request->all());
        $place->image->update(['path' => env('APP_URL').'/images/qrcodes/'. $place->store_id. '/' . $place->floor. '/' .$place->name. '.png']);

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
        $path = $place->image->path;
        $str = substr($path, strripos($path, "images"));
        if (file_exists($str)) {
            unlink($str);
        }
        $place->delete();

        return response()->json(['message' => '删除成功！', 'status' => 200]);
    }

    /** 【 刷新座位二维码 】 */ 
    public function refresh(Request $request, Place $place)
    {
        $path = $place->image->path;
        $str = substr($path, strripos($path, "images"));
        if (file_exists($str)) {
            unlink($str);
        }
        $place->fill($request->all());
        $place->update();
        $data = array(
            'name' => $place->name,
            'store_id' => $place->store_id,
            'floor' => $place->floor,
            'type' => 'place',
        );
        $result = $place->updateQrcode($data,$place->id);
        $place->image->update(['path' => env('APP_URL').'/images/qrcodes/'. $place->store_id. '/' . $place->floor. '/' .$place->name. '.png']);

        if ($result) {
            return (new PlaceResource($place))->additional(['status' => 200, 'message' => '修改成功！']);
        }
    }

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
        // 总价格
        $total = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->price;
        });
        // 总条数
        $number = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->number;
        });

        return (new ShopcartCollection($shopcarts))->additional(['status' => 200, 'count' => $number?:0, 'total' => $total?:0]);
    } 

    /** 【 客户端--购物车详情 】 */
    public function customerShopcart(Request $request, Place $place)
    {
        $shopcarts = $place->shopcarts;
        // 总价格
        $total = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->price;
        });
        // 总条数
        $number = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->number;
        });

        return (new ShopcartCollection($shopcarts))->additional(['status' => 200, 'count' => $number?:0, 'total' => $total?:0]);
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

        $number = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->number;
        });

        if ($request->id) {
            $order = Order::find($request->id);
            // 修改订单金额
            $order->update([
                'price' => $order->price + $total,
                'final_price' => $order->price + $total,
                'number' => $order->number + $number,
                'final_number' => $order->number + $number,
                'sitter' => $request->sitter
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
                'number' => $number,
                'final_number' => $number,
                'status' => 0,
                'sitter' => $request->sitter
            ]);
        }
        // 循环创建订单详情
        $new = $shopcarts->map(function ($item, $key) use ($only_order, $place){
            for ($i=0; $i < $item->number; $i++) { 
                if ($item->category == 'm') {
                    $create = OrderDetail::create([
                        'order_order' => $only_order,
                        'store_id' => $place->store_id,
                        'menu_id' => $item->menu_id,
                        'category' => $item->category,
                        'menus_id' => $item->menus_id,
                        'tags_id' => json_encode(json_decode($item->tags_id,true)[0])?:0,
                        'fill_price' => $item->fill_price?:0,
                        'number' => 1,
                        'original_price' => $item->original_price?:0,
                        'price' => $item->price/$item->number,
                        'status' => 0,
                        'remark' => json_decode($item->remark,true)[0]?:'',
                        'place_id' => $item->place_id,
                        'pid' => 0,
                    ]);
                } else {
                    $create = OrderDetail::create([
                        'order_order' => $only_order,
                        'store_id' => $place->store_id,
                        'menu_id' => $item->menu_id,
                        'category' => $item->category,
                        'number' => 1,
                        'original_price' => $item->original_price,
                        'price' => $item->price/$item->number,
                        'status' => 0,
                        'place_id' => $item->place_id,
                        'pid' => 0,
                    ]);
                }
                // 判断是套餐还是单品
                if ($item->category == 'p') {
                    if ($item->menus_id) {
                        for ($j=0; $j < count(json_decode($item->menus_id,true)); $j++) { 
                            OrderDetail::create([
                                'order_order' => $only_order,
                                'store_id' => $place->store_id,
                                'menu_id' => 0,
                                'category' => 'm',
                                'menus_id' => json_decode($item->menus_id,true)[$j]?:0,
                                'tags_id' => json_encode(json_decode($item->tags_id,true)[$j])?:0,
                                'fill_price' => json_decode($item->fill_price,true)[$j]?:0,
                                'number' => 1,
                                'status' => 0,
                                'remark' => json_decode($item->remark,true)[$j]?:'',
                                'place_id' => $item->place_id,
                                'pid' => $create->id,
                            ]);
                        }
                    }
                }
            }
            // 删除购物车记录
            $item->delete();
        });

        // 修改座位状态
        $place->update(['status' => 1]);
        // 记录员工行为
        Behavior::create([
            'user_id' => auth()->id(),
            'target_id' => $order->id,
            'category' => 'order',
            'status' => 1,
        ]);

        Gateway::sendToGroup('chef_'.$place->store_id, json_encode(array('type' => 'cooking', 'message' => '做饭了！'), JSON_UNESCAPED_UNICODE));

        return response()->json(['id' => $order->id, 'status' => 200, 'message' => '下单成功！']);
    } 

    /** 【 客户端--创建订单 】 */
    public function customerOrder(Request $request, Place $place, User $user)
    {
        $shopcarts = $place->shopcarts;
        if ($shopcarts->isEmpty()) {
            return response()->json(['error' => ['message' => ['购物车为空！']], 'status' => 404]);
        }

        $total = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->price;
        });

        $number = $shopcarts->reduce(function ($sum, $value){
            return $sum + $value->number;
        });

        if ($request->id) {
            $order = Order::find($request->id);
            // 修改订单金额
            $order->update([
                'price' => $order->price + $total,
                'final_price' => $order->price + $total,
                'number' => $order->number + $number,
                'final_number' => $order->number + $number,
                'sitter' => $request->sitter
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
                'number' => $number,
                'final_number' => $number,
                'status' => 0,
                'sitter' => $request->sitter
            ]);
        }
        // 循环创建订单详情
        $new = $shopcarts->map(function ($item, $key) use ($only_order, $place){
            for ($i=0; $i < $item->number; $i++) { 
                if ($item->category == 'm') {
                    $create = OrderDetail::create([
                        'order_order' => $only_order,
                        'store_id' => $place->store_id,
                        'menu_id' => $item->menu_id,
                        'category' => $item->category,
                        'menus_id' => $item->menus_id,
                        'tags_id' => json_encode(json_decode($item->tags_id,true)[0])?:0,
                        'fill_price' => $item->fill_price?:0,
                        'number' => 1,
                        'original_price' => $item->original_price?:0,
                        'price' => $item->price/$item->number,
                        'status' => 0,
                        'remark' => json_decode($item->remark,true)[0]?:'',
                        'place_id' => $item->place_id,
                        'pid' => 0,
                    ]);
                } else {
                    $create = OrderDetail::create([
                        'order_order' => $only_order,
                        'store_id' => $place->store_id,
                        'menu_id' => $item->menu_id,
                        'category' => $item->category,
                        'number' => 1,
                        'original_price' => $item->original_price,
                        'price' => $item->price/$item->number,
                        'status' => 0,
                        'place_id' => $item->place_id,
                        'pid' => 0,
                    ]);
                }
                // 判断是套餐还是单品
                if ($item->category == 'p') {
                    if ($item->menus_id) {
                        for ($j=0; $j < count(json_decode($item->menus_id,true)); $j++) { 
                            OrderDetail::create([
                                'order_order' => $only_order,
                                'store_id' => $place->store_id,
                                'menu_id' => 0,
                                'category' => 'm',
                                'menus_id' => json_decode($item->menus_id,true)[$j]?:0,
                                'tags_id' => json_encode(json_decode($item->tags_id,true)[$j])?:0,
                                'fill_price' => json_decode($item->fill_price,true)[$j]?:0,
                                'number' => 1,
                                'status' => 0,
                                'remark' => json_decode($item->remark,true)[$j]?:'',
                                'place_id' => $item->place_id,
                                'pid' => $create->id,
                            ]);
                        }
                    }
                }
            }
            // 删除购物车记录
            $item->delete();
        });

        // 修改座位状态
        $place->update(['status'=>1]);

        Gateway::sendToGroup('chef_'.$place->store_id, json_encode(array('type' => 'cooking', 'message' => '做饭了！'), JSON_UNESCAPED_UNICODE));

        return response()->json(['id' => $order->id, 'status' => 200, 'message' => '下单成功！']);
    } 

    /** 【 客户端--座位状态 】*/
    public function customerStatus(Request $request, Place $place)
    {
        // $place->order = Order::where('place_id', $place->id)->where('status', 0)->whereDate('created_at', date('Y-m-d'))->orderBy('created_at', 'desc')->first();
        $place->order = Order::where('place_id', $place->id)->where('status', 0)->orderBy('created_at', 'desc')->first();

        return response()->json(['data'=>$place, 'status'=>200]);
    }

    /** 【 绑定桌位二维码 】 */
    public function binding(Request $request, Place $place)
    {
        $place->fill($request->all());
        $place->update();
        $data = array(
            'name' => $place->name,
            'store_id' => $place->store_id,
            'floor' => $place->floor,
            'type' => 'place',
        );
        $place->updateQrcode($data,$place->id);
        $place->image->update(['path' => env('APP_URL').'/images/qrcodes/'. $place->store_id. '/' . $place->floor. '/' .$place->name. '.png']);
        return (new PlaceResource($place))->additional(['status' => 200, 'message' => '绑定成功！']);
    }
}
