<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class TestController extends Controller
{
    //
    public function reg1(Request $request){

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
    public function login1(Request $request){
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

    public function reg(Request $request){
        //
        $url='http://passport.1905.com/test/reg';
        $response=UserModel::curlPost($url,$_POST);

        return $response;
    }

    public function login(Request $request){
        //
        $url='http://passport.1905.com/test/login';
        $response=UserModel::curlPost($url,$_POST);

        return $response;
    }

    public function showData(){
        $uid=$_SERVER['HTTP_UID'];
        $token=$_SERVER['HTTP_TOKEN'];

        $url='http://passport.1905.com/test/reg';
        $response=UserModel::curlPost($url,['uid'=>$uid,'token'=>$token]);
        $status=json_decode($response,true);

        //处理鉴权结果
        if($status['errno']==0){
            $data='be8bbfe8b056805174aace18e1fb0cda';
            $response=[
                'errno'=>'ok',
                'msg'=>'鉴权成功',
                'data'=>$data
            ];
        }else{
            $response=[
                'errno'=>40003,
                'msg'=>'鉴权失败'
            ];
        }
        return $response;
    }

    public function md5test(){
        //发送的数据
        $data='yangkai';

        $key='1905';
        //计算签名
        $signature=md5($data.$key);
        echo "发送端的签名：".$signature;echo '</br>';

        //发送数据
        $url='http://passport.1905.com/test/checksign?data='.$data.'&signature='.$signature;
        $response=file_get_contents($url);
        echo $response;
    }

    public function md5test2(){
        //签名key
        $key='1905';
        $data=[
            'order_id'=>'yk'.mt_rand(11111,99999),
            'order_amount'=>mt_rand(1111,9999),
            'uid'=>1,
            'add_time'=>time()
        ];
        $data_json=json_encode($data);
        //计算签名
        $sign=md5($data_json.$key);

        //post发送数据
        $client=new Client();
        $url='http://passport.1905.com/test/checksign2';
        $response=$client->request('POST',$url,[
            'form_params'=>[
                'data'=>$data_json,
                'sign'=>$sign
            ]
        ]);
        //接收服务器响应的数据
        $response_data=$response->getBody();
        echo $response_data;
    }

    //
    function rsa1(){
        $data='aszx';

        $path=storage_path('keys/priv.key');
        $priv_key=openssl_pkey_get_private("file://".$path);
        var_dump($priv_key);echo "</br>";
        //计算签名
        openssl_sign($data,$signature,$priv_key);
        echo $signature;echo "</br>";

        echo "<hr>";
        openssl_free_key($priv_key);
        //base64编码
        $sign_str=base64_encode($signature);
        echo "base64后的签名:".$sign_str;

        $url='http://passport.1905.com/test/rsa2?data='.$sign_str;
        $response=file_get_contents($url);
        echo $response;
    }


    //对称加密
    public function encrypt(){
        $data='hello world';
        echo "原数据：".$data;echo "</br>";
        $method='AES-256-CBC';
        $key='yk';
        $iv='vgdfvfvfrdserrbd';

        $enc_data=openssl_encrypt($data,$method,$key,OPENSSL_RAW_DATA,$iv);
        echo "加密:".$enc_data;echo "</br>";
        $enc_data=urlencode(base64_encode($enc_data));
        echo "base64之后的加密：".$enc_data;echo "</br>";
        //发送加密数据
        $url='http://passport.1905.com/test/decrypt1?data='.$enc_data;
        $response=file_get_contents($url);
        echo $response;
    }

    //非对称加密
    public function rsa2(){
        $data='yangkai';
        //获取私钥路径
        $path=storage_path('keys/priv.key');
        //私钥资源
        $priv_key=openssl_pkey_get_private("file://".$path);
        //给数据私钥加密
        openssl_private_encrypt($data,$enc_data,$priv_key);
        echo $enc_data;echo "</br>";
        //加密的数据base64转译
        $enc_data=urlencode(base64_encode($enc_data));
        echo "base64加密：".$enc_data;echo "</br>";
        //发送数据
        $url='http://passport.1905.com/test/rsa2?data='.$enc_data;
        $response=file_get_contents($url);
        echo $response;

    }
}
