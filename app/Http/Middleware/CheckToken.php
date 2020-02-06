<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        print_r($_SERVER);die;
        if(isset($_SERVER['HTTP_TOKEN'])){
            $redis_key='str:'.'url'.$_SERVER['REQUEST_URI'];
            $c=Redis::get($redis_key);
            if($c>3){
                Redis::expire($redis_key,30);
                $response=[
                    'errno'=>'400001',
                    'msg'=>'接口调用超过三次',
                ];
                echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
                Redis::incr($redis_key);
            }else{
                $response=[
                    'errno'=>'400005',
                    'msg'=>'未授权',

                ];
                echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }
        }
        return $next($request);
    }
}
