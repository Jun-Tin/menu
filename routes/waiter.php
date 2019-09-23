<?php

use Illuminate\Http\Request;


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
    /** 【 个人信息 】 */ 
    Route::get('book/index', 'Waiter\BooksController@index');
    /** 【 创建预约 】 */
    Route::post('book/store', 'Waiter\BooksController@store');
    /** 【 修改预约 】 */
    Route::patch('book/{book}/update', 'Waiter\BooksController@update');
    /** 【 删除预约 】 */ 
    Route::delete('book/{book}/destroy', 'Waiter\BooksController@destroy');
    /** 【 预约选位 】 */ 
    Route::post('store/{store}/index', 'Waiter\StoresController@index');
    /** 【 预约列表 】*/ 
    Route::get('store/{store}/book', 'Waiter\StoresController@book');

});