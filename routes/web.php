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

Route::get('/test/pay','TestController@alipay');
Route::get('/test/alipay/return','Alipay\PayController@aliReturn');
Route::post('/test/alipay/notify','Alipay\PayController@notify');


//注册
Route::post('/test/reg','Api\TestController@reg');
Route::post('/test/login','Api\TestController@login');                          //登录
Route::get('/test/list','Api\TestController@userList')->middleware('filter');  //用户列表



