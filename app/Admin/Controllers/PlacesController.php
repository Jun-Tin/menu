<?php

namespace App\Admin\Controllers;

use App\Models\Place;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PlacesController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Place';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Place());

        $grid->column('id', __('Id'));
        $grid->column('store_id', __('Store id'));
        $grid->column('name', __('Name'));
        $grid->column('number', __('Number'));
        $grid->column('floor', __('Floor'));
        $grid->column('image_id', __('Image id'));
        $grid->column('status', __('Status'));
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
        $show = new Show(Place::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('store_id', __('Store id'));
        $show->field('name', __('Name'));
        $show->field('number', __('Number'));
        $show->field('floor', __('Floor'));
        $show->field('image_id', __('Image id'));
        $show->field('status', __('Status'));
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
        $form = new Form(new Place());

        $form->number('store_id', __('Store id'));
        $form->text('name', __('Name'));
        $form->number('number', __('Number'));
        $form->number('floor', __('Floor'));
        $form->number('image_id', __('Image id'));
        $form->number('status', __('Status'));

        return $form;
    }
}
