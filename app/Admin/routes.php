<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('users', UsersController::class);
    $router->resource('menus', MenusController::class);
    $router->resource('qrcodes', QrcodesController::class);
    $router->resource('places', PlacesController::class);
    $router->resource('tags', TagsController::class);

});
