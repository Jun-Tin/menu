<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('用户列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);
        $grid->model()->orderBy('id', 'desc');
        $grid->id('ID')->sortable();
        $grid->column('name', '用户名');
        $grid->column('store.name', '门店名称');
        $grid->column('account', '账号');
        $grid->column('area_code', '区号');
        $grid->column('phone', '手机号码');
        $grid->column('gender')->using(['0' => '女', '1' => '男']);
        $grid->column('birthday', '生日');
        $grid->column('post', '职位')->using(['boss' => '老板', 'waiter' => '服务员', 'chef' => '后厨']);
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));
        // dd($show);
        $show->id('ID');
        $show->name('用户名');
        // $show->store->name('门店名称');
        $show->account('账号');
        $show->area_code('区号');
        $show->phone('手机号码');
        $show->gender('性别')->display(function ($gender) {
            return $gender ? '男' : '女';
        });
        $show->birthday('生日');
        $show->column('post', '职位')->display(function($post) {
            switch ($post) {
                case 'boss':
                    return '老板';
                    break;
                case 'waiter':
                    return '服务员';
                    break;
                case 'chef':
                    return '后厨';
                    break;
            }
        });
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->display('id', 'ID');
        $form->text('name', '用户名')->rules('required');
        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        return $form;
    }
}
