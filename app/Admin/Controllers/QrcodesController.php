<?php

namespace App\Admin\Controllers;

use App\Models\{Menu, Qrcode, Image, place};
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Admin\Actions\Qrcode\{Create, BatchReplicate, BatchDeletion};
use App\Admin\Extensions\QrcodesExporter;

class QrcodesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '绑定座位二维码';

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
        // $grid->disableActions();
        // 禁用原有批量删除
        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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

        // $grid->actions(function ($actions) {
        //     // 去掉删除
        //     $actions->disableDelete();
        //     // 去掉编辑
        //     $actions->disableEdit();
        //     // 去掉查看
        //     $actions->disableView();
        //     $actions->add(new Bind);
        // });
        $excel = new QrcodesExporter();
        $excel->setAttr(['id', '图片', '链接地址'], ['id', 'image', 'link']);
        $grid->exporter($excel);

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

        $show->id('id');
        $show->image('二维码')->image();
        $show->link('链接地址');
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

        $form->image('image', '二维码');
        $form->select('store_id', '门店')->options(Menu::getSelectOptions())->load('floor', '/api/floor');
        $form->select('floor', '楼层')->load('place', '/api/place');
        $form->select('place', '座位');

        //保存前回调
        $form->saving(function (Form $form) {
            if (\request('place')) {
                $form->model()->link = Image::find(place::find(\request('place'))->image_id)->link;
            }
        });
        $form->ignore(['store_id', 'floor', 'place']);

        return $form;
    }
}
