<?php

namespace App\Admin\Controllers;

use App\Models\Qrcode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode as Qrcodes;
use App\Admin\Actions\Qrcode\BatchReplicate;
use Encore\Admin\Grid\Displayers\DropdownActions;

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
        $grid->image('Image');
        $grid->link('Link');
        $grid->created_at('Created at');
        // $grid->updated_at('Updated at');
         // 开启新的行内编辑
        // $grid->setActionClass(DropdownActions::class);
        $grid->batchActions(function ($batch) {
            $batch->add(new BatchReplicate());
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
        $form->number('number', '个数');
        // dd($number);
        // $form->image('image', 'Image');
        // $form->url('link', 'Link');
        // $form->filename = date('YmdHis'). uniqid(). '.png';
        dd($form);
        for ($i=0; $i < \request('number'); $i++) { 
            // 在表单提交前调用
            $form->saving(function (Form $form) use($i) {
                if (!\request('number')) {
                    // 抛出异常
                    throw new \Exception('请输入个数');
                }
                    $form->model()->image = env('APP_URL').'/images/systems/'. date('Ym',time()). '/'. date('YmdHis'). uniqid(). '.png';
                    $this->str[$i] = env('APP_URL').'/images/systems/'. date('Ym',time()). '/'. date('YmdHis'). uniqid(). '.png';
            });
                
            // 在表单提交后调用
            $form->saved(function (Form $form) use($i) {
                $dir = public_path('/images/systems/'). date('Ym',time()). '/';
                if (!is_dir($dir)) {
                    File::makeDirectory($dir, 0777, true);
                }
                    $filename[$i] = substr($this->str[$i], strripos($this->str[$i], "/")+1);
                    // 判断图片是否存在
                    if (file_exists($dir. '/' .$filename[$i])) {
                        unlink($dir. '/' .$filename[$i]);
                    }
                    // 保存二维码
                    Qrcodes::format('png')->errorCorrection('L')->size(200)->margin(2)->encoding('UTF-8')->generate('http://47.56.146.107/menu/#/BindCode/'. $form->model()->id, $dir. '/'. $filename[$i]);
            });
        }
        $form->ignore(['number']);

        return $form;

    }
}
