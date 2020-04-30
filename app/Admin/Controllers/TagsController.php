<?php

namespace App\Admin\Controllers;

use App\Models\Tag;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TagsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Tag';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tag());

        $grid->column('id', __('Id'));
        $grid->column('pid', __('Pid'));
        $grid->column('store_id', __('Store id'));
        $grid->column('name', __('Name'));
        $grid->column('category', __('Category'));
        $grid->column('order_number', __('Order number'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Tag::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('pid', __('Pid'));
        $show->field('store_id', __('Store id'));
        $show->field('name', __('Name'));
        $show->field('category', __('Category'));
        $show->field('order_number', __('Order number'));
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
        $form = new Form(new Tag());

        $form->number('pid', __('Pid'));
        $form->number('store_id', __('Store id'));
        $form->text('name', __('Name'));
        $form->text('category', __('Category'));
        $form->number('order_number', __('Order number'));

        return $form;
    }
}
