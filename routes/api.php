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


    /**【 门店 】*/
    // 门店列表
    Route::get('store/index', 'Api\StoresController@index');
    // 创建门店
    Route::post('store/store', 'Api\StoresController@store');
    // 修改门店
    Route::patch('store/update/{store}', 'Api\StoresController@update');
    // 删除门店
    Route::delete('store/destroy/{store}', 'Api\StoresController@destroy');
    // 门店菜品列表
    Route::get('store/show/{store}', 'Api\StoresController@show');

    /** 【 标签 】*/
    // 标签列表
    Route::post('tags/index', 'Api\TagsController@index');
    // 创建标签 
    Route::post('tags/store', 'Api\TagsController@store');
    // 修改标签 
    Route::patch('tags/update/{tag}', 'Api\TagsController@update');
    // 删除标签
    Route::delete('tags/destroy/{tag}', 'Api\TagsController@destroy');

    /**【 菜品 】*/
    // 菜品列表
    Route::get('menu/index/{menu}', 'Api\MenusController@index');
    // 创建菜品
    Route::post('menu/store', 'Api\MenusController@store');
    // 修改菜品
    Route::patch('menu/update/{menu}', 'Api\MenusController@update');
    // 删除菜品
    Route::delete('menu/destroy/{menu}', 'Api\MenusController@destroy');

    /**【 套餐 】*/
    // 套餐列表
    Route::get('package/index/{package}', 'Api\PackagesController@index');
    // 创建套餐
    Route::post('package/store', 'Api\PackagesController@store');
    // 修改套餐
    Route::patch('package/update/{package}', 'Api\PackagesController@update');
    // 删除套餐
    Route::delete('package/destroy/{package}', 'Api\PackagesController@destroy');

    /** 
     * 【 功能类接口 】
     */ 
    /**【 图片管理 】*/ 
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');
});