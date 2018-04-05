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

Route::group([
    "middleware" => "admin.custom.exception",
    "prefix" => 'admin'], function () {
    Route::get('/', 'Admin\HomeController@home');
    Route::get('/user/login', 'Admin\UserController@login');
    Route::post('/user/login', 'Admin\UserController@login');

    Route::post('/api/signup', 'API\ClientController@signup');
    Route::post('/api/login', 'API\ClientController@login');

    Route::group(["middleware" => "auth.session"], function () {

        Route::get('/wine/list', 'Admin\WineController@list');
        Route::get('/wine/add', 'Admin\WineController@add');
        Route::post('/wine/add', 'Admin\WineController@add');
        Route::get('/wine/autocomplete', array('as'=>'autocomplete','uses'=> 'Admin\WineController@autocomplete'));
        Route::get('/wine/{id}/edit', 'Admin\WineController@edit');
        Route::post('/wine/{id}/edit', 'Admin\WineController@edit');
        Route::get('/wine/{id}/order/up', 'Admin\WineController@order_up');
        Route::get('/wine/{id}/order/down', 'Admin\WineController@order_down');
        Route::get('/wine/{id}/remove', 'Admin\WineController@remove');

        Route::get('/questions/list', 'Admin\ConfiguratorController@list');
        Route::get('/questions/add', 'Admin\ConfiguratorController@add');
        Route::post('/questions/add', 'Admin\ConfiguratorController@add');
        Route::get('/questions/{id}/edit', 'Admin\ConfiguratorController@edit');
        Route::post('/questions/{id}/edit', 'Admin\ConfiguratorController@edit');
        Route::get('/questions/{id}/order/up', 'Admin\ConfiguratorController@order_up');
        Route::get('/questions/{id}/order/down', 'Admin\ConfiguratorController@order_down');
        Route::get('/questions/{id}/remove', 'Admin\ConfiguratorController@remove');

        Route::get('/settings', 'Admin\SettingsController@index');
        Route::get('/settings/user/list', 'Admin\SettingsController@user_list');
        Route::get('/settings/user/add', 'Admin\SettingsController@user_add');
        Route::post('/settings/user/add', 'Admin\SettingsController@user_add');
        Route::get('/settings/user/{id}/edit', 'Admin\SettingsController@user_edit');
        Route::post('/settings/user/{id}/edit', 'Admin\SettingsController@user_edit');
        Route::get('/settings/user/{id}/remove', 'Admin\SettingsController@user_remove');

        Route::get('/user/password', 'Admin\UserController@password');
        Route::post('/user/password', 'Admin\UserController@password');
        Route::get('/user/logout', 'Admin\UserController@logout');
    });
});