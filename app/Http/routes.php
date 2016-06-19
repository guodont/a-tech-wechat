<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware'=>['web']], function (){
    Route::get('/users','UsersController@users');
    Route::get('/user/{openId}','UsersController@user');
    Route::get('/menu','MenuController@menu');
    Route::get('/menus','MenuController@menus');
});


Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/auth', function (){
        $user = session('wechat.oauth_user'); // 拿到授权用户资料
        return view('usercenter', compact('user'));
    });
    Route::get('/userCenter', 'UsersController@userCenter');
});

Route::any('/wechat', 'WechatController@serve');
