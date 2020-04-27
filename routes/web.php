<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/password', function (\Illuminate\Http\Request $request){
    $http = new \GuzzleHttp\Client();

    $response = $http->post('http://menu.test/oauth/token', [
        'form_params' => [
            'grant_type' => 'password',
            'client_id' => '2',
            'client_secret' => 'myvid08Ru6Wmn4aLbIFVhrewzABRDUvUty2Pbpec',
            'username' => 'admin@163.com',
            'password' => 'password',
            'scope' => '*', //令牌授权支持的所有域进行
        ],
    ]);

    return json_decode((string)$response->getBody(), true);
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
