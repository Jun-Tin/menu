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
    Route::get('user/detail/{user}', 'Api\UsersController@detail');
    // 添加员工信息
    Route::post('user/staff', 'Api\UsersController@staff');
    // 修改员工信息
    Route::patch('user/edit/{user}', 'Api\UsersController@edit');
    // 删除员工信息
    Route::delete('user/delete/{user}', 'Api\UsersController@delete');


    /**【 门店 】*/
    // 门店列表
    Route::get('store/index', 'Api\StoresController@index');
    // 门店详情
    Route::get('store/show/{store}', 'Api\StoresController@show');
    // 创建门店
    Route::post('store/store', 'Api\StoresController@store');
    // 修改门店
    Route::patch('store/update/{store}', 'Api\StoresController@update');
    // 删除门店
    Route::delete('store/destroy/{store}', 'Api\StoresController@destroy');
    // 门店菜品列表
    Route::get('store/menus/{store}', 'Api\StoresController@menus');
    // 门店套餐列表
    Route::get('store/packages/{store}', 'Api\StoresController@packages');
    // 门店座位列表
    Route::patch('store/places/{store}', 'Api\StoresController@places');
    // 门店员工列表
    Route::get('store/users/{store}', 'Api\StoresController@users');

    /** 【 标签 】*/
    // 标签列表
    Route::get('tags/index/{pid}/{category}', 'Api\TagsController@index');
    // 创建标签 
    Route::post('tags/store', 'Api\TagsController@store');
    // 修改标签 
    Route::patch('tags/update/{tag}', 'Api\TagsController@update');
    // 删除标签
    Route::delete('tags/destroy', 'Api\TagsController@destroy');

    /**【 菜品 】*/
    // 菜品列表
    Route::get('menu/index/{menu}', 'Api\MenusController@index');
    // 创建菜品
    Route::post('menu/store', 'Api\MenusController@store');
    // 修改菜品
    Route::patch('menu/update/{menu}', 'Api\MenusController@update');
    // 删除菜品
    Route::delete('menu/destroy', 'Api\MenusController@destroy');

    /**【 套餐 】*/
    // 套餐列表
    Route::get('package/index/{package}', 'Api\PackagesController@index');
    // 创建套餐
    Route::post('package/store', 'Api\PackagesController@store');
    // 修改套餐
    Route::patch('package/update/{package}', 'Api\PackagesController@update');
    // 删除套餐
    Route::delete('package/destroy', 'Api\PackagesController@destroy');
    /** 【 嵌入标签 】 */ 
    // 套餐嵌入标签
    Route::patch('package/addTags/{package}', 'Api\PackagesController@addTags');
    // 删除嵌入标签
    Route::delete('package/subTags/{package}/{target_id}', 'Api\PackagesController@subTags');
    // 嵌入标签排序
    Route::post('package/orderTags/{package}', 'Api\PackagesController@orderTags');


    /**【 座位 】*/
    // 创建座位
    Route::post('place/store', 'Api\PlacesController@store');
    // 修改座位
    Route::patch('place/update/{place}', 'Api\PlacesController@update');
    // 删除座位--单个
    Route::delete('place/destroy/{place}', 'Api\PlacesController@destroy');
    // 删除座位--整层
    Route::delete('place/delete', 'Api\PlacesController@delete');
    // 获取座位二维码压缩包
    Route::get('place/makeZip/{store_id}/{floor}', 'Api\PlacesController@makeZip');


    /** 
     * 【 功能类接口 】
     */ 
    /**【 图片管理 】*/ 
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');
    Route::post('qrcode', 'Api\ImagesController@createQrcode');
});