<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImagesController extends Controller
{
    // 上传图片单张
    public function uploadImg(Request $request){
        $file = $request->file('file');
        header('Content-type: application/json');
        
        if (!$request->type) {
            return response()->json(['error' => ['message' => '图片类型不能为空'], 'status' => 201]);
        }
        
        // 文件是否上传成功
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); //文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名

            $realPath = $file->getRealPath();   //临时文件的绝对路径

            $type = $file->getClientMimeType();     // image/jpeg
            $size =$file->getSize();

            $extArr = array('jpg','jpeg','png','gif');
            if(!in_array($ext,$extArr)){
                return response()->json(['error' => ['message' => '文件格式不正确'], 'status' => 201]);
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

                return response()->json(['success' => ['message' => '上传成功', 'data' => array('image_id' => $image->id, 'url' => $url)], 'status' => 200]);
            }else{
                return response()->json(['error' => ['message' => '上传失败'], 'status' => 201]);
            }

        }else{
            return response()->json(['error' => ['message' => '上传失败'], 'status' => 201]);
        }
    }


    // 上传图片多张
    public function uploadImgs(Request $request){
        $file = $request->file('file');
        header('Content-type: application/json');
        
        if (!$request->type) {
            return response()->json(['error' => ['message' => '图片类型不能为空'], 'status' => 201]);
        }

        $filePath =[];  // 定义空数组用来存放图片路径
        foreach ($file as $key => $value) {
            // 文件是否上传成功
            if ($value->isValid()) {
                // 获取文件相关信息
                $originalName = $value->getClientOriginalName(); //文件原名
                $ext = $value->getClientOriginalExtension();     // 扩展名

                $realPath = $value->getRealPath();   //临时文件的绝对路径

                $type = $value->getClientMimeType();     // image/jpeg
                $size = $value->getSize();

                $extArr = array('jpg','jpeg','png','gif');
                if(!in_array($ext,$extArr)){
                    return response()->json(['error' => ['message' => '文件格式不正确'], 'status' => 201]);
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
                    return response()->json(['error' => ['message' => '上传失败'], 'status' => 201]);
                }
            }else{
                return response()->json(['error' => ['message' => '上传失败'], 'status' => 201]);
            }
        }

        return response()->json(['success' => ['message' => '上传成功', 'data' => array('image_id' => $data, 'url' => $url)], 'status' => 200]);
    }
}
