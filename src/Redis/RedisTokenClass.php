<?php namespace Xiaozhu\Redis;
use Illuminate\Support\Facades\Redis;
use Auth;

class RedisTokenClass{
	protected   $database='card_token'; //键名前缀 
	public function __construct($config)
    {
        
        $this->config = $config;
        $this->database = $config["redis_token_database"];
       
    }

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	public  function getAdd($user_id){
		//$redis->hmset('hash1', array('key3' => 'v3', 'key4' => 'v4'));
		$key = $this->database;
		Redis::hset($key,$user_id,date("Y-m-d"));
	}
	public  function getDel($tablename,$model){
		return  Redis::hdel($this->database.$tablename,$model->id);
	}

	public  function getFind($user_id){
		return  Redis::hget($this->database,$user_id);
	}
	public  function getSort($newLists){
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