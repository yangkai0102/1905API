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

Route::get('/test/showdata','Api\TestController@showData')->middleware('CheckToken');  //用户列表


Route::get('/test/asscii','TestController@asscii');
Route::get('/test/dec','TestController@dec');

// 用户管理
Route::get('/user/addkey','User\IndexController@addSSHKey1');
Route::post('/user/addkey','User\IndexController@addSSHKey2');
//解密数据
Route::get('/user/decrypt/data','User\IndexController@decrypt1');
Route::post('/user/decrypt/data','User\IndexController@decrypt2');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//验证签名
Route::get('/sign1','TestController@sign1');
//
Route::get('/sign2','TestController@sign2');

//get签名
Route::get('/test/md5test','Api\TestController@md5test');
//post签名
Route::get('/test/md5test2','Api\TestController@md5test2');

//
Route::get('/test/rsa1','Api\TestController@rsa1');

//测试接口防刷
Route::get('/test/token','TestController@token1');
