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

/** [ 获取验证码 ] */ 
Route::post('send', 'Api\MessageCodesController@store');
/** [ 注册 ] */ 
Route::post('user/register', 'Api\UsersController@register');
/** [ 登录 ] */ 
Route::post('user/login', 'Api\UsersController@login');
/** [ 忘记密码 ] */ 
Route::post('user/forgot', 'Api\UsersController@forgotPassWord');
 
/** [ 验证类接口 ] */ 
Route::group(['middleware' => 'auth:api'], function(){
    /** [ 个人信息 ] */ 
    Route::get('user/member', 'Api\UsersController@member');
    
    /** [ 图片管理 ] */ 
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');

    /** [ 门店 ] */
    // 创建门店
    Route::post('store/store', 'Api\StoresController@store');
    // 修改门店
    Route::post('store/update/{store}', 'Api\StoresController@update');
    // 删除门店
    Route::get('store/destroy/{store}', 'Api\StoresController@destroy');

    /** [ 标签 ] */
    // 创建标签 
    Route::post('tags/store', 'Api\TagsController@store');
    // 修改标签 
    Route::post('tags/update/{tag}', 'Api\TagsController@update');
    // 删除标签
    Route::get('tags/destroy/{tag}', 'Api\TagsController@destroy');
});