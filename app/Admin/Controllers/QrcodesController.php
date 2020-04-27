<?php

namespace App\Admin\Controllers;

use App\Models\Qrcode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Admin\Actions\Qrcode\Create;
use App\Admin\Actions\Qrcode\BatchReplicate;
use App\Admin\Actions\Qrcode\BatchDeletion;

class QrcodesController extends Controller
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
            ->header('二维码列表')
            ->description('description')
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
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Qrcode::findOrFail($id));

        $show->id('Id');
        $show->image('Image');
        $show->link('Link');
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
        $form = new Form(new Qrcode);
        // 在表单提交前调用
        $form->saving(function (Form $form) {
            $form->model()->image = env('APP_URL').'/images/systems/'. date('Ymd',time()). '/'. date('YmdHis'). uniqid(). '.png';
        });
            
        // 在表单提交后调用
        $form->saved(function (Form $form) {
            $dir = public_path('/images/systems/'). date('Ymd',time()). '/';
            if (!is_dir($dir)) {
                File::makeDirectory($dir, 0777, true);
            }
            $filename = substr($this->str, strripos($this->str, "/")+1);
            // 判断图片是否存在
            if (file_exists($dir. '/' .$filename[$i])) {
                unlink($dir. '/' .$filename[$i]);
            }
            // 保存二维码
            Qrcodes::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate('http://47.56.146.107/menu/#/BindCode/'. $form->model()->id, $dir. '/'. $filename[$i]);
        });

        return $form;

    }
}
