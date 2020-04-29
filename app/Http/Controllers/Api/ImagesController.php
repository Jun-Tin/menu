<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Storage, File};
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image AS Iimage;

class ImagesController extends Controller
{
    // 上传图片单张
    public function uploadImg(Request $request){
        $file = $request->file('file');
        header('Content-type: application/json');
        
        if (!$request->type) {
            return response()->json(['error' => ['message' => __('messages.picture_type')], 'status' => 201]);
        }
        
        // 文件是否上传成功
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); //文件原名
            $ext = strtolower($file->getClientOriginalExtension());     // 扩展名

            $realPath = $file->getRealPath();   //临时文件的绝对路径

            $type = $file->getClientMimeType();     // image/jpeg
            $size =$file->getSize();

            $extArr = array('jpg','jpeg','png','gif');
            if(!in_array($ext,$extArr)){
                return response()->json(['error' => ['message' => __('messages.picture_format')], 'status' => 201]);
            }

            // 拼接文件名称
            $filename = $request->type.'/'.date('YmdHis') . uniqid() . '.' . $ext;
            // 使用我们新建的upload_img本地存储空间（目录）
            //这里的upload_img是配置文件的名称
            $bool = Storage::disk('upload_img')->put($filename, file_get_contents($realPath));
            if($bool){
                $url = env('APP_URL').'/images/uploads/'.date('Ym',time()).'/'.$filename;
                // 保存在数据库
                $image = Image::create([
                	'user_id' => auth()->user()->id,
                	'type' => $request->type,
                	'path' => $url,
                ]);

                return response()->json(['message' => __('messages.upload_success'), 'success' => array('image_id' => $image->id, 'url' => $url), 'status' => 200]);
            }else{
                return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
            }

        }else{
            return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
        }
    }


    /** 【 上传图片多张 】 */ 
    public function uploadImgs(Request $request){
        $file = $request->file('file');
        header('Content-type: application/json');
        
        if (!$request->type) {
            return response()->json(['error' => ['message' => __('messages.picture_type')], 'status' => 201]);
        }

        $filePath =[];  // 定义空数组用来存放图片路径
        foreach ($file as $key => $value) {
            // 文件是否上传成功
            if ($value->isValid()) {
                // 获取文件相关信息
                $originalName = $value->getClientOriginalName(); //文件原名
                $ext = strtolower($value->getClientOriginalExtension());     // 扩展名

                $realPath = $value->getRealPath();   //临时文件的绝对路径

                $type = $value->getClientMimeType();     // image/jpeg
                $size = $value->getSize();

                $extArr = array('jpg','jpeg','png','gif');
                if(!in_array($ext,$extArr)){
                    return response()->json(['error' => ['message' => __('messages.picture_format')], 'status' => 201]);
                }

                // 拼接文件名称
                $filename = $request->type.'/'.date('YmdHis') . uniqid() . '.' . $ext;
                // 使用我们新建的upload_img本地存储空间（目录）
                //这里的upload_img是配置文件的名称
                $bool = Storage::disk('upload_img')->put($filename, file_get_contents($realPath));

                if($bool){
                    $url[] = env('APP_URL').'/images/uploads/'.date('Ym',time()).'/'.$filename;

                    // 保存在数据库
                    $image = Image::create([
                        'user_id' => auth()->user()->id,
                        'type' => $request->type,
                        'path' => env('APP_URL').'/images/uploads/'.date('Ym',time()).'/'.$filename,
                    ]);
                    $data[] = $image->id;
                }else{
                    return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
                }
            }else{
                return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
            }
        }

        return response()->json(['message' => __('messages.upload_success'), 'success' => array('image_id' => $data, 'url' => $url), 'status' => 200]);
    }

    /** 【 生成二维码 】 */
    public function createQrcode(Request $request)
    {
        $dir = public_path('images/qrcodes/'.$request->store_id. '/' .$request->floor);
        if (!is_dir($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
        // $filename = date('YmdHis').uniqid().'.png';
        $filename = $request->name . '.png';
        // 判断图片是否存在
        if (file_exists($dir. '/' .$filename)) {
            unlink($dir. '/' .$filename);
        }
        // 保存二维码
        // QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->color(255,255,255)->backgroundColor(132,212,141)->encoding('UTF-8')->generate('www.baidu.com', $dir. '/'. $filename);
        QrCode::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate('http://www.bunchparty.com', $dir. '/'. $filename);
        // 返回url链接
        $url = env('APP_URL').'/images/qrcodes/'. $request->store_id. '/' .$request->floor. '/' .$filename;
        // 保存在数据库
        $image = Image::create([
            'user_id' => auth()->user()->id,
            'type' => 'qrcodes/'.$request->store_id,
            'path' => $url,
        ]);

        return response()->json(['message' => __('messages.upload_success'), 'success' => array('image_id' => $image->id, 'url' => $url), 'status' => 200]);
    }

    /** 【 裁剪图片 】 */ 
    public function thumbImage(Request $request)
    {
        $file = $request->file('file');
        header('Content-type: application/json');
        
        if (!$request->type) {
            return response()->json(['error' => ['message' => __('messages.picture_type')], 'status' => 201]);
        }

        // 文件是否上传成功
        if ($file->isValid()) {
            $ext = strtolower($file->getClientOriginalExtension());     // 扩展名
            $extArr = array('jpg','jpeg','png','gif');

            if(!in_array($ext,$extArr)){
                return response()->json(['error' => ['message' => __('messages.picture_format')], 'status' => 201]);
            }
            $dir = public_path('/images/uploads/'). date('Ym',time()). '/'. $request->type. '/';
            if (!is_dir($dir)) {
                File::makeDirectory($dir, 0777, true);
            }
            $image = Iimage::make($file);
            for ($i=0; $i < 3; $i++) { 
                switch ($i) {
                    case 0:
                        $fixed = 800;
                        break;
                    case 1:
                        $fixed = 200;
                        break;
                    case 2:
                        $fixed = 100;
                        break;
                }
                // 拼接文件名称
                $filename[$i] = date('YmdHis'). uniqid(). '.'. $ext;
                $path[$i] = $dir. $filename[$i];
                $bool[$i] = $image->resize($fixed, null, function($constraint){
                    $constraint->aspectRatio();
                })->save($path[$i]);
                $url[$i] = env('APP_URL'). '/images/uploads/'. date('Ym',time()). '/'. $request->type. '/'. $filename[$i];
            }

            if($bool){
                // 保存在数据库
                $create = Image::create([
                    'user_id' => auth()->user()->id,
                    'type' => $request->type,
                    'path' => $url[2],
                    'mediumpath' => $url[1],
                    'tinypath' => $url[0],
                ]);
                return response()->json(['message' => __('messages.upload_success'), 'success' => array('image_id' => $create->id, 'url' => $url[2]), 'status' => 200]);
            }else{
                return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
            }
        } else {
            return response()->json(['error' => ['message' => __('messages.upload_fail')], 'status' => 201]);
        }
    }


    /** 
     *根据url获取服务器上的图片 
    **/  
    public function GrabImage() 
    { 
        Image::where('type', 'shop')->whereNull('mediumpath')->get()->map(function ($item){
            $dir = public_path('/images/uploads/'). date('Ym',time()). '/shop/'; //保存路径       
            if(!file_exists($dir))
            {       
               //检查是否有该文件夹，如果没有就创建，并给予最高权限       
               mkdir($dir, 0700);
            }

            ob_start();
            readfile($item->path);
            $img = ob_get_contents();
            ob_end_clean(); 
            $size = strlen($img);
            // 拼接文件名称
            $ext = substr($item->path,strripos($item->path,".")+1);
            for ($i=0; $i < 3; $i++) { 
                $image[$i] = Iimage::make($img);
                switch ($i) {
                    case 0:
                        $fixed = 800;
                        break;
                    case 1:
                        $fixed = 200;
                        break;
                    case 2:
                        $fixed = 100;
                        break;
                }
                // 拼接文件名称
                $filename[$i] = date('YmdHis'). uniqid(). '.'. $ext;
                $path[$i] = $dir. $filename[$i];
                $bool[$i] = $image[$i]->resize($fixed, null, function($constraint){
                    $constraint->aspectRatio();
                })->save($path[$i]);
                $link[$i] = env('APP_URL'). '/images/uploads/'. date('Ym',time()). '/shop/'. $filename[$i];
            }
            
            $item->update([
                'mediumpath' => $link[1],
                'tinypath' => $link[2]
            ]);
        });

    }
}
