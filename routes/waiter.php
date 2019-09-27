<?php

use Illuminate\Http\Request;

/**【 验证类接口 】*/ 
Route::group(['middleware' => 'auth:api'], function(){
    /** 【预约】 */ 
    // 创建预约 
    Route::post('book/store', 'Waiter\BooksController@store');
    // 修改预约
    Route::patch('book/{book}/update', 'Waiter\BooksController@update');
    // 删除预约 
    Route::delete('book/{book}/destroy', 'Waiter\BooksController@destroy');
    // 预约选位 
    Route::post('store/{store}/index', 'Waiter\StoresController@index');
    // 预约列表 
    Route::get('store/{store}/book', 'Waiter\StoresController@book');
    
});