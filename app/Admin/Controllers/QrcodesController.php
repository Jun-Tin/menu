<?php

namespace App\Admin\Controllers;

use App\Models\Qrcode;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Qrcode\BatchDeletion;
use App\Admin\Actions\Qrcode\BatchReplicate;
use App\Admin\Actions\Qrcode\Create;

class QrcodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '二维码';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Qrcode);

        $grid->id('Id');
        $grid->column('image', '二维码')->image('image', 100, 100);
        $grid->column('link', '链接地址');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        // 禁用创建按钮
        $grid->disableCreateButton();
        // 禁用行操作列
        $grid->disableActions();
        // 禁用原有批量删除
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
            // $tools->append(new Creation());
        });
        // 批量复制
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchReplicate());
            $batch->add(new BatchDeletion());
            
        });

        // 批量创建
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new Create());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Qrcode::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('image', __('Image'));
        $show->field('link', __('Link'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Qrcode());

        $form->image('image', __('Image'));
        $form->url('link', __('Link'));

        return $form;
    }
}
