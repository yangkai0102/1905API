<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}