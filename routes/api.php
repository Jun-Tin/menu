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

// 获取验证码
Route::post('send', 'Api\MessageCodesController@store');
// 注册
Route::post('user/register', 'Api\UsersController@register');
// 登录
Route::post('user/login', 'Api\UsersController@login');
// Route::post('send', 'Api\UsersController@sendSmsCode');
// 忘记密码
Route::post('user/forgot', 'Api\UsersController@forgotPassWord');
 
Route::group(['middleware' => 'auth:api'], function(){
    // 个人信息
    Route::get('user/member', 'Api\UsersController@member');
    // 图片管理
    Route::post('img', 'Api\ImagesController@uploadImg');
    Route::post('imgs', 'Api\ImagesController@uploadImgs');

    // 门店
    Route::post('store', 'Api\StoresController@store');
});