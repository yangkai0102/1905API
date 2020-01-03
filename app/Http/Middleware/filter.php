<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class filter
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
        if(isset($_SERVER['HTTP_TOKEN'])){

            $redis_key='str:count:u'. ':url:' . $_SERVER['REQUEST_URI'];
            $count=Redis::get($redis_key);
            if($count>=5){
                Redis::expire($redis_key,60);
                $response=[
                    'errno'=>40001,
                    'msg'=>'调用次数已达上限，请稍后再试'
                ];
                echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
            }
                Redis::incr($redis_key);
        }else{
            $response=[
                'errno'=>40004,
                'msg'=>'未授权'
            ];
            echo json_encode($response,JSON_UNESCAPED_UNICODE);die;
        }


        return $next($request);
    }
}
