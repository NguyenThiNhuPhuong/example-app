<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $modelClass = User::class;

    function test()
    {
        $key="hihi";
      if(Redis::exists($key)){
          $start = microtime(true); // Thời gian bắt đầu

          $value = Redis::get($key);

          $end = microtime(true); // Thời gian kết thúc

          $executionTime = ($end - $start); // Thời gian thực hiện tính bằng giây

          echo "Lấy từ redis: " . $executionTime . " s";
      }else{
          $start = microtime(true); // Thời gian bắt đầu

          $value = $this->modelClass::limit(9000)->get();

          $end = microtime(true); // Thời gian kết thúc

          $executionTime = ($end - $start); // Thời gian thực hiện tính bằng giây

          Redis::set($key,$value);

          echo "Lấy từ database(mySql): " . $executionTime . " s";
      }
    }

    function testKey(){
        $key="hihii";
        if(Redis::exists($key)){

            $value = Redis::get($key);

            echo "Tồn tại key: " .$key. "<br>";
            echo "Giá trị: ".$value;

        }else{
            echo "Không tồn tại key: " .$key;
        }

    }

    function cache(){
        $key="han";
        if(Cache::has($key)){

            $value = Cache::get($key);

            echo "Tồn tại key: " .$key. "<br>";
            echo "Giá trị: ".$value;

        }else{
            $value="Nguyễn Văn Hân";
            Cache::set($key,$value);
            echo "Không tồn tại key: " .$key ."<br>";
            echo "Đã set key thành công!";
        }

    }
}
