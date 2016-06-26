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

    $wechat = app('wechat');

    $qrcode = $wechat->qrcode;

    $result = $qrcode->temporary(56, 3600);

    return view('welcome', compact('result', 'qrcode'));
});

Route::post('/doBindAccount', 'UsersController@doBindAccount');

Route::post('/sendTempMsg', 'UsersController@sendTempMsg');

Route::group(['middleware' => ['web']], function () {
    Route::get('/users', 'UsersController@users');
    Route::get('/user/{openId}', 'UsersController@user');
    Route::get('/menu', 'MenuController@menu');
    Route::get('/menus', 'MenuController@menus');
    Route::get('/questions', 'QuestionController@questions2');
    Route::get('/question/{questionId}', 'QuestionController@question');
});


Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/auth', 'UsersController@auth');
    Route::get('/bindAccount', 'UsersController@bindAccount');
    Route::get('/userCenter', 'UsersController@userCenter');
});

Route::any('/wechat', 'WechatController@serve');
