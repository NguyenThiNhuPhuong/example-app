<?php

namespace App\Http\Controllers;

use App\Models\User;
use http\Client\Request;
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

    function time()
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

    function data($dataKey){
        switch ($dataKey){

            case 'Strings':
                $key="LP0325";
                $data="Nguyen Thi Nhu Phuong";
                Redis::set($key,$data);

                if(Redis::exists($key)){

                    $value = Redis::get($key);

                    echo "Key: " .$key. "<br>";
                    echo "Value: ".$value;

                }
                break;

            case 'Lists':

                $key="myList";
                $data="Nguyen Thi Ngoc Han";

                Redis::rpush($key, $data);

                //Redis::lrem($key, 0, $value);

                if(Redis::exists($key)) {

                    $myList = Redis::lrange($key, 0, -1);

                    echo "Key: " . $key . "<br>";
                    foreach ($myList as $index => $item) {
                        echo "Item " . $index . ':  ' . $item . "<br>";
                    }

                }
                break;

            case "Sets":
                $key="SetsKey";
                $data =["phuong","hue","nguyen"] ;

                Redis::sadd($key, $data);

                if(Redis::exists($key)) {

                    $mySets=Redis::smembers($key);

                    echo "Key: " . $key . "<br>";
                    foreach ($mySets as $index => $item) {
                        echo "Item " . $index . ':  ' . $item . "<br>";
                    }

                }
                break;
            case "SortedSets":
                $key="SortedSets";
                $data =[
                    'phuong' => 1000,
                    'binh' => 800,
                    'han' => 1200,
                ];

                Redis::zadd($key, $data);

                if(Redis::exists($key)) {
                    // Lấy điểm của một người chơi
                    $score = Redis::zscore($key, 'phuong');
                    echo 'phuong score: ' . $score. '<br>';

                    // Lấy xếp hạng (rank) của một người chơi trong bảng xếp hạng
                    $rank = Redis::zrank($key, 'phuong');
                    echo 'phuong rank: ' . $rank. '<br>';

                    // Lấy danh sách các người chơi có điểm từ thấp đến cao
                    $players = Redis::zrange($key, 0, -1);

                    foreach ($players as $index => $item) {
                        echo "Item " . $index . ':  ' . $item . "<br>";
                    }
                }
                break;

            case 'Hashes':
                $key="hash_key";
                $data=['email' => 'nguyenphuong@gmail.com', 'password' => '$2y$10$w7PKGIKRkDaBDRS2IaJsmOIV0GkrigUxW9O5ReHGWjT.P504qPwWe'];
                Redis::hmset($key, $data);

                if(Redis::exists($key)) {

                    $value= Redis::hgetall($key);

                    foreach ($value as $index => $item) {
                        echo  $index . ':  ' . $item . "<br>";
                    }

                }
                break;

            case 'Streams':
                $key="my_stream";
                $data1= ['name' => 'phuong', 'message' => 'Hello phuong'];
                $data2= ['name' => 'binh', 'message' => 'Hi Binh'];
                Redis::xadd($key, '*',['data' => json_encode($data1)]);
                Redis::xadd($key, '*', ['data' => json_encode($data2)]);

                break;

            case 'Json':
                $jsonData = [
                    'name' => 'John',
                    'age' => 30,
                ];

                // Lưu dữ liệu JSON vào Redis sử dụng RedisJSON
                Redis::command('JSON.SET', ['myjsondata', '.', json_encode($jsonData)]);
                //Redis::command('SET', ['mykey', 'myvalue']);

                return 'Dữ liệu JSON đã được lưu vào Redis bằng RedisJSON.';
                break;

            case "StringJson":
                $key="StringJson";
                $jsonData = [
                    'name' => 'Phuong',
                    'age' => 22,
                ];

                // Chuyển đổi dữ liệu JSON thành chuỗi
                $jsonString = json_encode($jsonData);

                // Lưu chuỗi JSON vào Redis với một key cụ thể
                Redis::set($key, $jsonString);
                break;
            case "StringJson":
                $key="StringJson";
                $jsonData = [
                    'name' => 'Phuong',
                    'age' => 22,
                ];

                // Chuyển đổi dữ liệu JSON thành chuỗi
                $jsonString = json_encode($jsonData);

                // Lưu chuỗi JSON vào Redis với một key cụ thể
                Redis::set($key, $jsonString);
                break;


            default:
                echo "http://app.com:8080/data/".$dataKey."<br>"." không hợp lệ.";
                break;
        }


    }
}
