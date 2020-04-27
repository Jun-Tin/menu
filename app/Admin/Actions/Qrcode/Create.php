<?php

namespace App\Admin\Actions\Qrcode;

use App\Models\Qrcode;
use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode as Qrcodes;

class Create extends Action
{
    protected $selector = '.create';

    public function handle(Request $request)
    {
        if (!$request->number) {
            return $this->response()->timeout(1000)->info('请输入创建个数');
        }
        $dir = public_path('/images/systems/'). date('Ymd',time()). '/';
        if (!is_dir($dir)) {
            File::makeDirectory($dir, 0777, true);
        }
        for ($i=0; $i < $request->number; $i++) {
            $filename = date('YmdHis'). uniqid(). '.png';
            // 判断图片是否存在
            if (file_exists($dir. '/' .$filename)) {
                unlink($dir. '/' .$filename);
            }
            $image = env('APP_URL').'/images/systems/'. date('Ymd',time()). '/'. $filename;
            // 保存二维码
            $create = Qrcode::create([
                'image' => $image
            ]);
            Qrcodes::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate('http://47.56.146.107/menu/#/BindCode/'. $create->id, $dir. '/'. $filename);
        }

        return $this->response()->success('创建成功')->refresh();
    }

    public function html()
    {
        return <<<HTML
        <div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
            <a class="btn btn-sm btn-success create">
                <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;批量创建</span>
            </a>
        </div>
HTML;
    }

    public function form()
    {
        $this->text('number', '创建个数');
    }
}