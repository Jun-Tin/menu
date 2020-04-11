<?php

namespace App\Admin\Controllers;

use App\Models\{Menu, Image};
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Intervention\Image\Facades\Image AS Iimage;

class MenusController extends Controller
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
            ->header('菜品列表')
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
        $grid = new Grid(new Menu);

        $grid->id('Id');
        $grid->column('store.name', '门店名称');
        $grid->column('name', '主菜名');
        $grid->column('name_en', '复菜名');
        $grid->column('introduction', '菜品介绍');
        $grid->column('image.path', '菜品图片')->image('image.path',100,100);
        $grid->column('original_price', '原价');
        $grid->column('special_price', '特价');
        $grid->column('level', '推荐指数')->using([
            1 => "<i class=\"fa fa-star\"></i>",
            2 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            3 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            4 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            5 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>"
        ]);
        $grid->column('type', '显示价格')->using([
            'o' => '原价',
            's' => '特价'
        ]);
        $grid->column('category', '菜品类型')->using([
            'm' => '单品',
            'p' => '套餐'
        ]);
        $grid->column('status', '售卖状态')->using([
            0 => '下架',
            1 => '上架'
        ]);
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
        $show = new Show(Menu::findOrFail($id));

        $show->id('Id');
        $show->field('store_name', '门店名称')->as(function (){
            return $this->store->name;
        });
        $show->name('主菜名');
        $show->name_en('复菜名');
        $show->introduction('菜品介绍');
        $show->field('image_id', '菜品图片')->as(function (){
            return $this->image->path;
        })->image();
        $show->original_price('原价');
        $show->special_price('特价');
        $show->level('推荐指数')->using([
            1 => "<i class=\"fa fa-star\"></i>",
            2 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            3 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            4 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>",
            5 => "<i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i><i class=\"fa fa-star\"></i>"
        ]);
        $show->type('显示价格')->using([
            'o' => '原价',
            's' => '特价'
        ]);
        $show->category('菜品类型')->using([
            'm' => '单品',
            'p' => '套餐'
        ]);
        $show->status('售卖状态')->using([
            0 => '下架',
            1 => '上架'
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
        $form = new Form(new Menu);

        $form->select('store_id', '门店名称')->options(Menu::getSelectOptions());
        $form->text('name', '主菜名');
        $form->text('name_en', '复菜名');
        $form->textarea('introduction', '菜品介绍');
        $form->image('image.path', '菜品图片');
        
        $form->decimal('original_price', '原价');
        $form->decimal('special_price', '特价');
        $form->number('level', '推荐指数')->min(0)->max(5);
        
        $form->select('type', '显示价格')->options([
            'o' => '原价',
            's' => '特价'
        ]);
        $form->select('category', '菜品类型')->options([
            'm' => '单品',
            'p' => '套餐'
        ]);
        $form->select('status', '售卖状态')->options([
            0 => '下架',
            1 => '上架'
        ]);
        // 在表单提交前调用
        $form->saving(function (Form $form) {
            $dir = public_path('/images/uploads/'). date('Ym',time()). '/shop/';
            if (!is_dir($dir)) {
                File::makeDirectory($dir, 0777, true);
            }
            $image = Iimage::make(\request()->file('image.path'));
            // 拼接文件名称
            $filename = date('YmdHis'). uniqid(). '.'. \request()->file('image.path')->getClientOriginalExtension();
            $path = $dir. $filename;
            $height = $image->height() / 200;
            $width = $image->width() / $height;
            $bool = $image->resize($width, 200)->save($path);

            if($bool){
                $url = env('APP_URL'). '/images/uploads/'. date('Ym',time()). '/shop/'. $filename;
                // 保存在数据库
                $create = Image::create([
                    'user_id' => Admin::user()->id,
                    'type' => 'shop',
                    'path' => $url,
                ]);
            }
            $form->model()->image_id = $create->id;
        });

        return $form;
    }
}
