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
Route::post('send', 'Api\MessageCodesController@store');
/**【 注册 】*/ 
Route::post('user/register', 'Api\UsersController@register');
/**【 登录 】*/ 
Route::post('user/login', 'Api\UsersController@login');
/**【 忘记密码 】*/ 
Route::post('user/forgot', 'Api\UsersController@forgotPassWord');
/**【 获取区号 】*/ 
Route::get('areacode', 'Api\AreacodesController@index');

/** 【 socket通讯 】 */
// 加入端口
Route::post('socket/join', 'Api\SocketsController@join');
// 离开端口
Route::post('socket/leave', 'Api\SocketsController@leave');


/**【 验证类接口 】*/ 
Route::group(['middleware' => 'auth:api'], function(){
    /**【 个人信息 】*/ 
    Route::get('user/member', 'Api\UsersController@member');
    /**【 修改密码 /手机号码】*/ 
    Route::patch('user/update', 'Api\UsersController@update');
    /**【 退出登录 】*/
    Route::get('user/logout', 'Api\UsersController@logout');

    /**【 员工 】*/ 
    // 员工信息详情
    Route::get('user/{user}/detail', 'Api\UsersController@detail');
    // 添加员工信息
    Route::post('user/staff', 'Api\UsersController@staff');
    // 修改员工信息
    Route::patch('user/{user}/edit', 'Api\UsersController@edit');
    // 删除员工信息
    Route::delete('user/{user}/delete', 'Api\UsersController@delete');
    // 员工表现
    Route::get('user/{user}/behavior', 'Api\UsersController@behavior');


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
    // 门店菜品列表
    Route::get('store/{store}/menus', 'Api\StoresController@menus');
    // 门店套餐列表
    Route::get('store/{store}/packages', 'Api\StoresController@packages');
    // 门店座位列表、加菜
    Route::get('store/{store}/places', 'Api\StoresController@places');
    // 门店座位列表--按人数筛选
    Route::get('store/{store}/scrPlaces/{number}', 'Api\StoresController@scrPlaces');
    // 门店座位列表--退菜
    Route::get('store/{store}/retreatPlaces', 'Api\StoresController@retreatPlaces');
    // 门店员工列表
    Route::get('store/{store}/users', 'Api\StoresController@users');
    // 删除座位--整层
    Route::delete('store/{store}/delete/{floor}', 'Api\StoresController@delete');
    // 售罄菜品列表
    Route::get('store/{store}/saleOut', 'Api\StoresController@saleOut');
    // 门店菜品列表--全部
    Route::get('store/{store}/totalMenus', 'Api\StoresController@totalMenus');

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
    /** 【 套餐嵌入标签 】 */ 
    // 添加标签
    Route::post('menu/{menu}/addTags', 'Api\MenusController@addTags');
    // 排序标签
    Route::post('menu/{menu}/orderTags', 'Api\MenusController@orderTags');
    // 删除标签
    Route::delete('menu/{menu}/subTags', 'Api\MenusController@subTags');
    /** 【 套餐嵌入菜品 】 */
    // 添加、修改菜品
    Route::post('menu/{id}/addMenus', 'Api\MenusController@addMenus');
    // 删除菜品
    Route::delete('menu/{menu}/subMenus', 'Api\MenusController@subMenus');
    // 获取菜品列表
    Route::get('menu/{id}/getMenus', 'Api\MenusController@getMenus');
    // 菜品售罄
    Route::patch('menu/saleStatus', 'Api\MenusController@saleStatus');

    

    /**【 座位 】*/
    // 创建楼层
    Route::post('place/addFloor', 'Api\PlacesController@addFloor');
    // 修改楼层
    Route::patch('place/{place}/editFloor', 'Api\PlacesController@editFloor');
    // 创建座位
    Route::post('place/store', 'Api\PlacesController@store');
    // 修改座位
    Route::patch('place/{place}/update', 'Api\PlacesController@update');
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
    // 预约选位 
    Route::post('store/{store}/index', 'Api\StoresController@index');
    // 预约列表 
    Route::get('store/{store}/book', 'Api\StoresController@book');
    // 预约详情
    Route::get('book/{book}/index', 'Api\BooksController@index');

    /** 【 购物车 】 */ 
    // 创建购物车（加入商品）
    Route::post('shopcart/store', 'Api\ShopcartsController@store');
    // 购物车增加、减少商品
    Route::patch('shopcart/{shopcart}/update', 'Api\ShopcartsController@update'); 

    /** 【 订单】 */
    // 订单详情
    Route::put('order/{order}/index', 'Api\OrdersController@index'); 
    // 确认支付（修改订单状态）
    Route::patch('order/{order}/update', 'Api\OrdersController@update');
    /** 【 后厨 】 */
    // 出菜 / 未出菜 所有列表
    Route::get('order/{status}/orders', 'Api\OrdersController@orders');
    Route::get('order/serving', 'Api\OrdersController@serving');

    /** 【 员工表现 】 */
    /**
    * 点击打扫
    @ 预约->book，下单->order，上菜->serving，打扫->clean，结账->settle，退菜->retreat
    */
    Route::post('behavior/store', 'Api\BehaviorsController@store');
    // 打扫完成
    Route::patch('behavior/{behavior}/update', 'Api\BehaviorsController@update');

    /** 
     * 【 功能类接口 】
     */ 
    /**【 图片管理 】*/ 
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');
    Route::post('qrcode', 'Api\ImagesController@createQrcode');
});


/** 【 门店详情 】 */
Route::get('store/{store}/customerShow', 'Api\StoresController@customerShow'); 
/** 【 座位状态 】 */
Route::get('place/{place}/customerStatus', 'Api\PlacesController@customerStatus'); 

/** 【 自定义验证类接口 】 */ 
Route::group(['middleware' => 'Code'], function(){
    // Route::get('store/index/{store}/{place}/{code}', 'Api\StoresController@index');
    // 
    /** 【 菜品列表--全部】 */ 
    Route::get('store/{store}/customerMenus', 'Api\StoresController@customerMenus');

    /** 【 购物车 】 */ 
    // 创建购物车（加入商品）
    Route::post('shopcart/customerStore', 'Api\ShopcartsController@customerStore');
    // 购物车增加、减少商品
    Route::patch('shopcart/{shopcart}/customerUpdate', 'Api\ShopcartsController@customerUpdate'); 

    /**【 座位 】*/
    // 获取座位下购物车详情
    Route::get('place/{place}/customerShopcart', 'Api\PlacesController@customerShopcart');

    /** 【 订单 】 */
    // 生成订单
    Route::patch('place/{place}/customerOrder', 'Api\PlacesController@customerOrder'); 
    // 订单详情
    Route::put('order/{order}/customerIndex', 'Api\OrdersController@customerIndex'); 

});