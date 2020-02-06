<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TestController extends Controller
{
    public function alipay(){

        //支持网关
        $ali_geteway='https://openapi.alipaydev.com/gateway.do';

        //公共参数
        $appid='2016101400681523';
        $method='alipay.trade.page.pay';
        $charset='utf-8';
        $signtype='RSA2';
        $sign='';
        $timestamp=date('Y-m-d H:i:s');
        $version='1.0';
        $return_url = 'http://yk.1548580932.top/test/alipay/return';       // 支付宝同步通知
        $notify_url = 'http://yk.1548580932.top/test/alipay/notify'; //支付宝异步通知地址
        $biz_content='';

        //请求参数
        $out_trade_no=time() . rand(1111,9999);      //商户订单号
        $product_code='FAST_INSTANT_TRADE_PAY';
        $total_amount=10000;
        $subject='海景豪华别墅' . $out_trade_no;

        $request_param=[
            'out_trade_no'=>$out_trade_no,
            'product_code'=>$product_code,
            'total_amount'=>$total_amount,
            'subject'=>$subject
        ];

        $param=[
            'app_id'=>$appid,
            'method'=>$method,
            'charset'=>$charset,
            'sign_type'=>$signtype,
            'timestamp'=>$timestamp,
            'version'=>$version,
            'notify_url'=>$notify_url,
            'return_url'=>$return_url,
            'biz_content'=>json_encode($request_param)
        ];
//
//        print_r($param);

        //字典序排序
        ksort($param);

        //拼接key=value&key=value
        $str='';
        foreach($param as $k=>$v){
            $str .= $k . '=' . $v . '&';
        }

        $str=rtrim($str,'&');

        //3、计算签名
        $key=storage_path('keys/app_priv');
        $prikey=file_get_contents($key);
        $res = openssl_get_privatekey($prikey);
        openssl_sign($str,$sign,$res,OPENSSL_ALGO_SHA256);
        $sign=base64_encode($sign);
        $param['sign']=$sign;

        //4 urlencode
        $param_str='?';
        foreach($param as $k=>$v){
            $param_str .= $k .'='.urlencode($v) . '&';
        }
        $param_str=rtrim($param_str,'&');
        $url = $ali_geteway . $param_str;
//        echo $url;die;
        header("Location:".$url);

    }


    public function asscii(){
        $a='yangkai';
        $length=strlen($a);
        echo $length;echo '</br>';

        $pass='';
        for($i=0;$i<$length;$i++){
            echo $a[$i] . '>>>' .ord($a[$i]);echo '</br>';
            $ord=ord($a[$i])+3;
            $chr=chr($ord);
            echo $a[$i] . '>>>' . $ord . '>>>' .$chr;echo '<hr>';

            $pass .=$chr;
        }
        echo "加密：".$pass;

    }

    public function dec(){
        $enc='|dqjndl';
        echo "密文：".$enc;echo '<hr>';

        $length=strlen($enc);
        $str='';
        for ($i=0;$i<$length;$i++){
//            echo $i;echo "</br>";

            $ord=ord($enc[$i])-3;
            echo $ord;echo "</br>";
            $chr=chr($ord);
            echo $chr;echo '</br>';
            $str .=$chr;
        }

        echo "解密：".$str;

    }


    public function sign1(){
        //验证签名
        echo '<pre>';print_r($_GET);echo "</pre>";

        $sign=$_GET['sign'];
        unset($_GET['sign']);

        //将参数字典序排序
        ksort($_GET);

        echo '<pre>';print_r($_GET);echo '</pre>';
        echo '<hr>';
        //拼接字符串
        $str="";
        foreach($_GET as $k=>$v){
            $str.=$k .'=' . $v .'&';
        }
        $str=rtrim($str,'&');
        echo $str;
        echo '</br>';

        //使用公钥验签
        $pub_key=file_get_contents(storage_path('keys/pub.key'));
        $status=openssl_verify($str,base64_decode($sign),$pub_key,OPENSSL_ALGO_SHA256);
        var_dump($status);

        if($status){
            echo '验签成功';
        }else{
            echo "验证失败";
        }
    }

    //
    public function sign2(){

        $token_key='fdsfdsafd';
        //接收参数
        echo '<pre>';print_r($_GET);echo "</pre>";

        $sign=$_GET['sign'];
        echo $sign;echo "</br>";
        unset($_GET['sign']);

        ksort($_GET);
        echo '<pre>';print_r($_GET);echo "</pre>";


        //拼接待签名字符串
        $str='';
        foreach ($_GET as $k=>$v){
            $str .= $k .'='.$v.'&';
        }
        $str=rtrim($str,'&');
        echo $str;echo '</br>';

        $tmp_str=$str.$token_key;
        $sign1=md5($tmp_str);
        echo '接收端计算的签名：'.$sign1;echo '</br>';

        if($sign1===$sign){
            echo '验签成功';
        }else{
            echo '验签失败';
        }
    }


    //接口防刷
    public function token1(){

//        echo  11;die;
//       用户标识
        $token=$_SERVER['HTTP_TOKEN'];
        //当前url
        $request_url=$_SERVER['REQUEST_URI'];

        $url_md=md5($token . $request_url);
        $key='count:url:'.$url_md;

        $count=Redis::get($key);
        echo '当前接口访问次数为：'.$count;

        if($count>=3) {
            $time=10;
            echo "不要勿频繁访问此接口,请在$time 秒后重试";
            Redis::expire($key,$time);
            die;
        }
        //访问数加1
        $count=Redis::incr($key);
        echo 'count: '.$count;
    }
}