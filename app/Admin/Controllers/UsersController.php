<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户';

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
        $grid->column('gender', '性别')->using(['0' => '女', '1' => '男']);
        $grid->column('post', '职位')->using(['boss' => '老板', 'waiter' => '服务员', 'chef' => '后厨']);
        $grid->column('created_by', '推荐人')->display(function ($item) {
            if ($this->created_by) {
                return $this->user->name;
            }
        });
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->disableCreateButton();

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
        $show = new Show(User::findOrFail($id));
        
        $show->id('ID');
        $show->name('用户名');
        $show->field('store_name', '门店名称')->as(function (){
            return $this->store->name;
        });
        $show->account('账号');
        $show->area_code('区号');
        $show->phone('手机号码');
        $show->gender('性别')->using(['0' => '女', '1' => '男']);
        $show->post('职位')->using([
            'boss' => '老板',
            'waiter' => '服务员',
            'chef' => '后厨',
        ]);
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
        $form->text('name', '用户名')->rules('required', [
            'required' => '不能为空'
        ]);
        $form->text('phone', '电话号码');
        $form->select('gender', '性别')->options([
            '0' => '女',
            '1' => '男'
        ]);
        $form->number('coins', '金币数量');
        // 密码输入框
        $form->password('password', '密码');
        $form->select('post', '职位')->options([
            'waiter' => '服务员',
            'chef' => '后厨',
            'boss' => '老板'
        ]);
        $form->select('created_by', '推荐人')->options(User::getSelectOptions());
        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->model()->pro_password = $form->password;
                $form->password = bcrypt($form->password);
            }
        });

        return $form;
    }
}
