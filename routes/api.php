<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
/**【 获取验证码 】*/ 
Route::post('send', 'Api\MessageCodesController@store')->middleware('setLocale');
/**【 注册 】*/ 
Route::post('user/register', 'Api\UsersController@register')->middleware('setLocale');
/**【 登录 】*/ 
Route::post('user/login', 'Api\UsersController@login')->middleware('setLocale');
/**【 忘记密码 】*/ 
Route::post('user/forgot', 'Api\UsersController@forgotPassWord')->middleware('setLocale');
/**【 获取区号 】*/ 
Route::get('areacode', 'Api\AreacodesController@index')->middleware('setLocale');
/**【 切换语言 】*/
Route::get('changeLocale/{locale}','Api\AreacodesController@changeLocale')->middleware('setLocale');
/**【 获取门店设置语言 】*/
Route::get('store/{store}/language', 'Api\StoresController@language');
// 获取门店设置货币 
Route::get('store/{store}/currency', 'Api\StoresController@currency');

// paypal支付
Route::get('paypal/pay', 'Api\PaypalsController@pay');
Route::get('paypal/callback', 'Api\PaypalsController@callback');
Route::get('paypal/notify', 'Api\PaypalsController@notify');

/** 【 定时调用 】 */
// 定时计算上线天数
Route::get('store/computeDays', 'Api\StoresController@computeDays');

Route::get('place/redis', 'Api\PlacesController@redis');



/** 【 socket通讯 】 */
// 加入端口
Route::post('socket/join', 'Api\SocketsController@join');
// 离开端口
Route::post('socket/leave', 'Api\SocketsController@leave');
// 测试socket通讯
Route::post('socket/test', 'Api\SocketsController@test');


/**【 验证类接口 】*/ 
Route::group(['middleware' => ['auth:api', 'setLocale']], function(){
    /**【 个人信息 】*/ 
    Route::get('user/member', 'Api\UsersController@member');
    /**【 退出登录 】*/
    Route::get('user/logout', 'Api\UsersController@logout');
    /**【 我的上线 】 */
    Route::get('user/online', 'Api\UsersController@online'); 
    

    /**【 员工 】*/ 
    // 员工信息详情
    Route::get('user/{user}/detail', 'Api\UsersController@detail');
    // 添加员工信息
    Route::post('user/staff', 'Api\UsersController@staff');
    // 修改员工信息
    Route::patch('user/{user}/edit', 'Api\UsersController@edit');
    // 删除员工信息
    Route::delete('user/{user}/delete', 'Api\UsersController@delete');
    // 刷新二维码
    Route::get('user/{user}/refresh', 'Api\UsersController@refresh');
    // 员工表现
    Route::get('user/behavior', 'Api\UsersController@behavior');
    // 更换关联手机 —— 验证手机号码
    Route::post('user/verify', 'Api\UsersController@verify');
    // 更换关联手机 —— 新手机号码
    Route::post('user/relate', 'Api\UsersController@relate');
    /** 【 销售人员 】 */ 
    // 创建账号
    Route::post('user/create', 'Api\UsersController@create');
    // 我的客户
    Route::get('user/client', 'Api\UsersController@client');
    // 修改客户金币数
    Route::patch('user/update', 'Api\UsersController@update');
    // 我的账单
    Route::get('user/bill', 'Api\UsersController@bill');


    /**【 门店 】*/
    // 门店列表
    Route::get('store/index', 'Api\StoresController@index');
    // 门店详情
    Route::get('store/{store}/show', 'Api\StoresController@show');
    // 创建门店
    Route::post('store/store', 'Api\StoresController@store');
    // 修改门店
    Route::patch('store/{store}/update', 'Api\StoresController@update');
    // 删除门店
    Route::delete('store/{store}/destroy', 'Api\StoresController@destroy');
    // 激活门店
    Route::patch('store/{store}/active', 'Api\StoresController@active');
    // 门店菜品列表
    Route::get('store/{store}/menus', 'Api\StoresController@menus');
    // 门店套餐列表
    Route::get('store/{store}/packages/{type}', 'Api\StoresController@packages');
    // 门店座位列表、加菜
    Route::get('store/{store}/places', 'Api\StoresController@places');
    // 门店座位列表--按人数筛选
    Route::get('store/{store}/scrPlaces/{number}', 'Api\StoresController@scrPlaces');
    // 门店座位列表--退菜
    Route::get('store/{store}/retreatPlaces', 'Api\StoresController@retreatPlaces');
    // 门店员工列表 -- 服务员
    Route::get('store/{store}/users', 'Api\StoresController@users');
    // 门店员工列表 -- 后厨
    Route::get('store/{store}/chef', 'Api\StoresController@chef');
    // 删除座位--整层
    Route::delete('store/{store}/delete/{floor}', 'Api\StoresController@delete');
    // 售罄菜品列表
    Route::get('store/{store}/saleOut', 'Api\StoresController@saleOut');
    // 门店菜品列表--全部
    Route::get('store/{store}/totalMenus/{type}', 'Api\StoresController@totalMenus');
    // 门店在售、售罄菜品数量
    Route::get('store/{store}/searchMenus', 'Api\StoresController@searchMenus');
    // 门店售罄菜品一键恢复
    Route::get('store/{store}/returnMenus', 'Api\StoresController@returnMenus');
    // 预约选位 
    Route::post('store/{store}/index', 'Api\StoresController@index');
    // 预约列表 
    Route::get('store/{store}/book', 'Api\StoresController@book');
    // 电话列表 
    Route::get('store/{store}/phone', 'Api\StoresController@phone');
    /** 【 区域 】 */
    // 区域列表
    Route::get('store/{store}/area', 'Api\StoresController@area'); 
    // 刷新门店二维码 -- 排队/大屏幕
    Route::patch('store/{store}/refresh', 'Api\StoresController@refresh');
    // 门店预约二维码
    Route::get('store/{store}/bookQrcode', 'Api\StoresController@bookQrcode');
    


    /** 【 报表 】 */
    // 收入报表 -- 总月
    Route::patch('store/{store}/totalMonthIncome', 'Api\StoresController@totalMonthIncome');
    // 收入报表 -- 每月
    Route::patch('store/{store}/eachMonthIncome', 'Api\StoresController@eachMonthIncome');
    // 收入报表 -- 每天
    Route::patch('store/{store}/eachDayIncome', 'Api\StoresController@eachDayIncome');
    // 收入报表 -- 总周
    Route::patch('store/{store}/totalWeekIncome', 'Api\StoresController@totalWeekIncome');
    // 收入报表 -- 每周
    Route::patch('store/{store}/eachWeekIncome', 'Api\StoresController@eachWeekIncome');
    // 客人报表 -- 时段
    Route::patch('store/{store}/guestMoment', 'Api\StoresController@guestMoment');
    // 菜品报表 -- 排行
    Route::patch('store/{store}/menuRank', 'Api\StoresController@menuRank');
    // 桌位报表 -- 排行
    Route::patch('store/{store}/placeRank', 'Api\StoresController@placeRank');
    // 员工报表 -- 服务
    Route::patch('store/{store}/staffService', 'Api\StoresController@staffService');
    // 后厨报表 -- 出菜时间
    Route::patch('store/{store}/menuServed', 'Api\StoresController@menuServed');
    // 所有套餐种类
    Route::get('store/{store}/menuTags', 'Api\StoresController@menuTags');



    /** 【 标签 】*/
    // 标签列表
    Route::get('tags/{store_id}/index/{pid}/{category}', 'Api\TagsController@index');
    // 创建标签 
    Route::post('tags/store', 'Api\TagsController@store');
    // 修改标签 
    Route::patch('tags/{tag}/update', 'Api\TagsController@update');
    // 删除标签
    Route::delete('tags/destroy', 'Api\TagsController@destroy');
    // 门店菜品列表--标签
    Route::get('tags/{tag}/menus', 'Api\TagsController@menus');


    /**【 菜品 、 套餐 】*/
    // 菜品、套餐详情
    Route::get('menu/{menu}/index', 'Api\MenusController@index');
    // 创建菜品、套餐
    Route::post('menu/store', 'Api\MenusController@store');
    // 修改菜品、套餐
    Route::patch('menu/{menu}/update', 'Api\MenusController@update');
    // 删除菜品、套餐
    Route::delete('menu/destroy', 'Api\MenusController@destroy');
    // 菜品售罄（多选菜品）
    Route::patch('menu/saleStatus', 'Api\MenusController@saleStatus');
    // 菜品在售、售罄修改（单个菜品）
    Route::patch('menu/{menu}/soldStatus', 'Api\MenusController@soldStatus');
    // 修改菜品 —— 不修改标签关系
    Route::patch('menu/{menu}/edit', 'Api\MenusController@edit');
    // 修改菜品排序 —— 菜品
    Route::patch('menu/upDown', 'Api\MenusController@upDown');
    // 修改菜品排序 —— 套餐内单品
    Route::patch('menu/MenusUpDown', 'Api\MenusController@MenusUpDown');
    // 修改菜品排序 —— 套餐
    Route::patch('menu/PackageUpDown', 'Api\MenusController@PackageUpDown');
    // 选中套餐种类
    Route::post('menu/{menu}/selectMenuTag', 'Api\MenusController@selectMenuTag');


    /** 【 新套餐 】 */
    // 创建标签
    Route::post('menu/{menu}/addTags', 'Api\MenusController@addTags');
    // 修改标签
    Route::patch('menu/{menu}/editTags', 'Api\MenusController@editTags');
    // 排序标签
    Route::post('menu/{menu}/orderTags', 'Api\MenusController@orderTags');
    // 删除标签
    Route::delete('menu/{menu}/subTags', 'Api\MenusController@subTags');
    // 添加、修改菜品
    Route::post('menu/{id}/addMenus', 'Api\MenusController@addMenus');
    // 删除菜品
    Route::delete('menu/{menu}/subMenus', 'Api\MenusController@subMenus');
    // 获取菜品列表
    Route::get('menu/{id}/getMenus', 'Api\MenusController@getMenus');


    /**【 座位 】*/
    // 创建楼层
    Route::post('place/addFloor', 'Api\PlacesController@addFloor');
    // 修改楼层
    Route::patch('place/{place}/editFloor', 'Api\PlacesController@editFloor');
    // 创建座位
    Route::post('place/store', 'Api\PlacesController@store');
    // 刷新座位二维码
    Route::patch('place/{place}/refresh', 'Api\PlacesController@refresh');
    // 修改座位
    Route::patch('place/{place}/update', 'Api\PlacesController@update');
    // 绑定二维码
    Route::patch('place/{place}/binding', 'Api\PlacesController@binding');
    // 删除座位--单个
    Route::delete('place/{place}/destroy', 'Api\PlacesController@destroy');
    // 删除座位--整层
    // Route::delete('place/{store_id}/delete/{floor}', 'Api\PlacesController@delete');
    // 获取座位二维码压缩包
    Route::get('place/makeZip/{store_id}/{floor}', 'Api\PlacesController@makeZip');
    // 获取座位下购物车详情
    Route::get('place/{place}/shopcart', 'Api\PlacesController@shopcart');
    /** 【 订单 】 */
    // 生成订单
    Route::patch('place/{place}/order', 'Api\PlacesController@order'); 


    /** 【 预约 】 */ 
    // 创建预约 
    Route::post('book/store', 'Api\BooksController@store');
    // 修改预约
    Route::patch('book/{book}/update', 'Api\BooksController@update');
    // 删除预约 
    Route::delete('book/{book}/destroy', 'Api\BooksController@destroy');
    // 预约详情
    Route::get('book/{book}/index', 'Api\BooksController@index');
    // 预约状态修改按钮
    Route::patch('book/{book}/edit', 'Api\BooksController@edit');


    /** 【 区域人员 】 */
    // 创建区域
    Route::post('area/store', 'Api\AreasController@store');
    // 修改区域
    Route::patch('area/{id}/update', 'Api\AreasController@update');
    // 删除区域
    Route::delete('area/{area}/destroy', 'Api\AreasController@destroy');
    // 区域详情
    Route::get('area/{area}/index', 'Api\AreasController@index');


    /** 【 购物车 】 */ 
    // 创建购物车（加入商品 -- 详情页添加）
    Route::post('shopcart/store', 'Api\ShopcartsController@store');
    // 购物车增加、减少商品
    Route::patch('shopcart/{shopcart}/update', 'Api\ShopcartsController@update'); 
    // 创建购物车（加入商品 -- 直接点击‘+’添加）
    Route::post('shopcart/created', 'Api\ShopcartsController@created');
    // 创建购物车（减少商品 -- 直接点击‘-’减少）
    Route::post('shopcart/reduced', 'Api\ShopcartsController@reduced');


    /** 【 订单】 */
    // 订单详情
    Route::put('order/{order}/index', 'Api\OrdersController@index'); 
    // 确认支付（修改订单状态）
    Route::patch('order/{order}/update', 'Api\OrdersController@update');
    /** 【 后厨 】 */
    // 出菜 / 未出菜 所有列表
    Route::get('order/orders', 'Api\OrdersController@orders');
    // 上菜列表
    Route::patch('order/serving', 'Api\OrdersController@serving');
    // 退菜列表
    Route::patch('order/{order}/retreat', 'Api\OrdersController@retreat');


    /** 【 员工表现 】 */
    /**
    * 点击打扫
    @ 预约->book，下单->order，上菜->serving，打扫->clean，结账->settle，退菜->retreat
    */
    Route::post('behavior/store', 'Api\BehaviorsController@store');
    // 打扫完成
    Route::patch('behavior/{behavior}/update', 'Api\BehaviorsController@update');


    /** 【 支付类型 】 */
    // 列表
    Route::get('paymentmethod/index', 'Api\PaymentMethodsController@index');
    // 创建
    Route::post('paymentmethod/store', 'Api\PaymentMethodsController@store');
    // 修改
    Route::patch('paymentmethod/{paymentmethod}/update', 'Api\PaymentMethodsController@update');
    // 删除
    Route::delete('paymentmethod/{paymentmethod}/destroy', 'Api\PaymentMethodsController@destroy');


    /** 【 上线周期 】 */
    // 列表
    Route::get('period/index', 'Api\PeriodController@index');
    // 创建
    Route::post('period/store', 'Api\PeriodController@store');
    // 修改
    Route::patch('period/{period}/update', 'Api\PeriodController@update');
    // 删除
    Route::delete('period/{period}/destroy', 'Api\PeriodController@destroy');


    /** 【 排队 】 */
    // 列表
    Route::get('line/index', 'Api\LinesController@index');
    // 修改状态值
    Route::patch('line/{line}/update', 'Api\LinesController@update');


    /** 【 语言选择 】 */
    // 列表
    Route::get('language/index', 'Api\LanguagesController@index');
    // 创建
    Route::post('language/store', 'Api\LanguagesController@store');
    // 修改
    Route::patch('language/{language}/update', 'Api\LanguagesController@update');
    // 删除 
    Route::delete('language/{language}/destroy', 'Api\LanguagesController@destroy');


    /** 【 门店类型 】 */
    // 列表
    Route::get('type/index', 'Api\TypesController@index');
    // 创建
    Route::post('type/store', 'Api\TypesController@store');
    // 修改
    Route::patch('type/{type}/update', 'Api\TypesController@update');
    // 删除 
    Route::delete('type/{type}/destroy', 'Api\TypesController@destroy');


    /** 【 货币选择 】 */
    // 列表
    Route::get('currency/index', 'Api\CurrenciesController@index');
    // 创建
    Route::post('currency/store', 'Api\CurrenciesController@store');
    // 修改
    Route::patch('currency/{currency}/update', 'Api\CurrenciesController@update');
    // 删除 
    Route::delete('currency/{currency}/destroy', 'Api\CurrenciesController@destroy');


    /** 
     * 【 功能类接口 】
     */ 
    /**【 图片管理 】*/ 
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');
    Route::post('qrcode', 'Api\ImagesController@createQrcode');

});
    Route::post('image', 'Api\ImagesController@thumbImage');


/** 【 门店详情 】 */
Route::get('store/{store}/customerShow', 'Api\StoresController@customerShow')->middleware('setLocale'); 
/** 【 座位状态 】 */
Route::get('place/{place}/customerStatus', 'Api\PlacesController@customerStatus')->middleware('setLocale'); 
/** 【 客户预约 】 */
// 创建
Route::post('book/customerStore', 'Api\BooksController@customerStore')->middleware('setLocale');
// 详情
Route::get('book/{book}/customerIndex', 'Api\BooksController@customerIndex')->middleware('setLocale');

/** 【 自定义验证类接口 】 */ 
Route::group(['middleware' => ['Code', 'setLocale']], function(){
    // Route::get('store/index/{store}/{place}/{code}', 'Api\StoresController@index');
    // 
    /** 【 菜品列表--全部 】 */ 
    Route::get('store/{store}/customerMenus/{type}', 'Api\StoresController@customerMenus');
    /** 【 菜品列表--套餐 】 */ 
    Route::get('store/{store}/customerPackages/{type}', 'Api\StoresController@customerPackages');
    /** 【 菜品详情 】 */ 
    Route::get('menu/{menu}/customerIndex', 'Api\MenusController@customerIndex');

    /** 【 购物车 】 */ 
    // 创建购物车（加入商品）
    Route::post('shopcart/customerStore', 'Api\ShopcartsController@customerStore');
    // 购物车增加、减少商品
    Route::patch('shopcart/{shopcart}/customerUpdate', 'Api\ShopcartsController@customerUpdate');
    // 创建购物车（加入商品 -- 直接点击‘+’添加）
    Route::post('shopcart/customerCreated', 'Api\ShopcartsController@customerCreated');
    // 创建购物车（减少商品 -- 直接点击‘-’减少）
    Route::post('shopcart/customerReduced', 'Api\ShopcartsController@customerReduced');

    /** 【 座位 】 */
    // 获取座位下购物车详情
    Route::get('place/{place}/customerShopcart', 'Api\PlacesController@customerShopcart');

    /** 【 订单 】 */
    // 生成订单
    Route::patch('place/{place}/customerOrder', 'Api\PlacesController@customerOrder'); 
    // 订单详情
    Route::put('order/{order}/customerIndex', 'Api\OrdersController@customerIndex'); 

    /** 【 排队 】 */
    // 创建
    Route::patch('line/store', 'Api\LinesController@store');
    // 大屏幕列表
    Route::get('line/screen', 'Api\LinesController@screen');

    /** 【 免登录验证 】 */ 
    // 检测用户正确性
    Route::patch('user/confirm', 'Api\UsersController@confirm');

});