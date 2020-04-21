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
            $ext = $file->getClientOriginalExtension();     // 扩展名

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
                $ext = $value->getClientOriginalExtension();     // 扩展名

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
            $ext = $file->getClientOriginalExtension();     // 扩展名
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
                        $fixed = 100;
                        break;
                    case 1:
                        $fixed = 200;
                        break;
                    case 2:
                        $fixed = 300;
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

    /** 【 补充数据 】 */
    public function loopImage()
    {

        $bigImg= $this->GrabImage('http://47.56.146.107/menub/images/uploads/201912/shop/201912231831475e0097932517a.jpg', public_path('/images/uploads/'). date('Ym',time()). '/shop/'); 
        dd($bigImg);
        var_dump($this->compressImg($bigImg,100,100,1));
        dd(123);
        $collection = Image::get()->map(function ($item){
            header('Content-type: image/jpg');
            // dd(imagecreatefromstring(file_get_contents($item->path)));
        });
        
    } 

    /** 
     *根据url获取服务器上的图片 
     *$url服务器上图片路径 $filename文件名 
    */  
    public function GrabImage($url,$filename="") { 
        if($url=="") return false;  
        if($filename=="") {  
            $ext=strrchr($url,".");  
            if($ext!=".gif" && $ext!=".jpg" && $ext!=".png")  
                return false;  
            $filename=date("YmdHis").$ext;  
        }  
        ob_start();   
        readfile($url);   
        $img = ob_get_contents();   
        ob_end_clean();  
        $size = strlen($img);   
      
        $fp2=@fopen($filename, "a");  
        fwrite($fp2,$img);  
        fclose($fp2);  
        return $filename;  
    } 

    /** 
    *
    *函数：调整图片尺寸或生成缩略图 
    *返回：True/False 
    *参数：
    *   $Image   需要调整的图片(含路径) 
    *   $Dw=450  调整时最大宽度;缩略图时的绝对宽度 
    *   $Dh=450  调整时最大高度;缩略图时的绝对高度 
    *   $Type=1  1,调整尺寸; 2,生成缩略图 
    */ 

    public function compressImg($Image,$Dw,$Dh,$Type){  
        IF(!file_exists($Image)){  
            return false;  
        }  
        // 如果需要生成缩略图,则将原图拷贝一下重新给$Image赋值(生成缩略图操作)  
        // 当Type==1的时候，将不拷贝原图像文件，而是在原来的图像文件上重新生成缩小后的图像(调整尺寸操作)  
        IF($Type!=1){  
            copy($Image,str_replace(".","_x.",$Image));  
            $Image=str_replace(".","_x.",$Image);  
        }  
        // 取得文件的类型,根据不同的类型建立不同的对象  
        $ImgInfo=getimagesize($Image);  
        Switch($ImgInfo[2]){  
            case 1:  
                $Img =@imagecreatefromgif($Image);  
                break;  
            case 2:  
                $Img =@imagecreatefromjpeg($Image);  
                Break;  
            case 3:  
                $Img =@imagecreatefrompng($Image);  
                break;  
        }  
        // 如果对象没有创建成功,则说明非图片文件  
        IF(Empty($Img)){  
            // 如果是生成缩略图的时候出错,则需要删掉已经复制的文件  
            IF($Type!=1){  
                unlink($Image);  
            }  
            return false;  
        }  
        // 如果是执行调整尺寸操作则  
        IF($Type==1){  
            $w=ImagesX($Img);  
            $h=ImagesY($Img);  
            $width = $w;  
            $height = $h;  
            IF($width>$Dw){  
                $Par=$Dw/$width;  
                $width=$Dw;  
                $height=$height*$Par;  
                IF($height>$Dh){  
                    $Par=$Dh/$height;  
                    $height=$Dh;  
                    $width=$width*$Par;  
                }  
            } ElseIF($height>$Dh) {  
                $Par=$Dh/$height;  
                $height=$Dh;  
                $width=$width*$Par;  
                IF($width>$Dw){  
                    $Par=$Dw/$width;  
                    $width=$Dw;  
                    $height=$height*$Par;  
                }  
            } Else {  
                $width=$width;  
                $height=$height;  
            }  
            $nImg =ImageCreateTrueColor($width,$height);// 新建一个真彩色画布  
            ImageCopyReSampled($nImg,$Img,0,0,0,0,$width,$height,$w,$h);// 重采样拷贝部分图像并调整大小  
            ImageJpeg($nImg,$Image);// 以JPEG格式将图像输出到浏览器或文件  
            return true;  
        } Else {// 如果是执行生成缩略图操作则  
            $w=ImagesX($Img);  
            $h=ImagesY($Img);  
            $width = $w;  
            $height = $h;  
            $nImg =ImageCreateTrueColor($Dw,$Dh);  
            IF($h/$w>$Dh/$Dw){// 高比较大  
                $width=$Dw;  
                $height=$h*$Dw/$w;  
                $IntNH=$height-$Dh;  
                ImageCopyReSampled($nImg, $Img, 0, -$IntNH/1.8, 0, 0, $Dw, $height, $w, $h);  
            } Else {// 宽比较大  
                $height=$Dh;  
                $width=$w*$Dh/$h;  
                $IntNW=$width-$Dw;  
                ImageCopyReSampled($nImg, $Img,-$IntNW/1.8,0,0,0, $width, $Dh, $w, $h);  
            }  
            ImageJpeg($nImg,$Image);  
            return true;  
        }  
    }
}
