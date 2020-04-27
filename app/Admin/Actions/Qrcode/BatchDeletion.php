<?php

namespace App\Admin\Actions\Qrcode;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchDeletion extends BatchAction
{
    public $name = '批量删除';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
        	$dir = public_path('/images/systems/'). date('Ymd',time()). '/';
        	$filename = substr($model->image, strripos($model->image, "/")+1);
        	// 判断图片是否存在
            if (file_exists($dir. '/' .$filename)) {
                unlink($dir. '/' .$filename);
            }
            $model->delete();
        }

        return $this->response()->success('删除成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定删除？');
    }

}