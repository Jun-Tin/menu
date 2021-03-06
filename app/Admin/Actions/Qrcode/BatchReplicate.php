<?php

namespace App\Admin\Actions\Qrcode;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchReplicate extends BatchAction
{
    public $name = '批量复制';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->replicate()->save();
        }

        return $this->response()->success('Success message...')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定复制？');
    }
}