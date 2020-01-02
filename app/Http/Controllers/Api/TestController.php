<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function reg(Request $request){

        $pass1=$request->input('pass1');
        $pass2=$request->input('pass2');
        if($pass1!=$pass2){
            die('两次密码输入不一致');
        }

        $password=password_hash($pass1,PASSWORD_BCRYPT);
        $data=[
            'name'  =>$request->input('name'),
            'password'  =>$password,
            'email'   =>$request->input('email'),
            'last_login'=>time(),
            'last_ip'=>$_SERVER['REMOTE_ADDR']
        ];


    }
}
