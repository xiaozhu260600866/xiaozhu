<?php namespace App\Http\Controllers\Redis;
use Illuminate\Support\Facades\Redis;
use Auth;
class RedisTokenClass{
	protected static  $database='card_token'; //键名前缀 

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	public static function getAdd($user_id){
		//$redis->hmset('hash1', array('key3' => 'v3', 'key4' => 'v4'));
		$key = self::$database;
		Redis::hset($key,$user_id,date("Y-m-d"));
	}
	public static function getDel($tablename,$model){
		return  Redis::hdel(self::$database.$tablename,$model->id);
	}

	public static function getFind($user_id){
		return  Redis::hget(self::$database,$user_id);
	}
	public static function getSort($newLists){
		$arr = $newLists["data"];
        for ($i=0; $i < count($arr); $i++) { 
            for ($j=$i+1; $j < count($arr); $j++) { 
                if($arr[$j]["id"] > $arr[$i]["id"]){
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$i];
                    $arr[$i] = $temp;
                }         
            }               
        }       	
        return $arr;
	}


}