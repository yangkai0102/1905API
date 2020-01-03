<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
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
            'mobile'=>$request->input('mobile'),
            'email'   =>$request->input('email'),
            'last_login'=>time(),
            'last_ip'=>$_SERVER['REMOTE_ADDR']
        ];

        $uid=UserModel::insertGetId($data);
        if($uid){
            $res=[
                'errno'=>'ok',
                'msg'=>'注册成功'
            ];
        }else{
            $res=[
                'errno'=>'40001',
                'msg'=>'注册失败'
            ];
        }
        return $res;
    }

//登录
    public function login(Request $request){
        $name=$request->input('name');
        $pass=$request->input('pass');
//        echo $pass;
        $user=UserModel::where(['name'=>$name])->first();
        echo $user;
        if($user){
            //验证密码
            if(password_verify($pass,$user->password)){
                //生成token
                $token=Str::random(32);
                $res=[
                    'error'=>0,
                    'msg'=>'ok',
                    'data'=>[
                        'token'=>$token
                    ]
                ];
            }else{
                $res=[
                    'errno'=>'40003',
                    'msg'=>'密码错误'
                ];
            }
        }else{
            $res=[
                'errno'=>'40001',
                'msg'=>'用户不存在'
            ];
        }
        return $res;

    }

    public function userList(){

        $res=UserModel::all();
        print_r($res->toArray());
    }
}
