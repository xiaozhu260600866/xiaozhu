<?php namespace Xiaozhu\Redis;
use Illuminate\Support\Facades\Redis;
use Illuminate\Config\Repository;
use Auth;
class RedisHistoryClass{
	protected   $database='history_card_'; //键名前缀 

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	    public function __construct(Repository $config)
    {
        
        $this->config = $config;
        
    }
	 
	public  function getAdd($tablename,$request,$lists){
		$request= $this->getUnset($request);
		$key = $this->database.$tablename;
		Redis::hset($key,json_encode($request),json_encode($lists));
	}
	public  function getFind($tablename,$request){

		$request= $this->getUnset($request);
		$key = $this->database.$tablename;

		return  json_decode(Redis::hget($key,json_encode($request)));
	}
	public  function getDelAll($tablename){
		$lists = Redis::hgetall($this->database.$tablename);
		foreach ($lists as $key => $value) {
			  Redis::hdel($this->database.$tablename,$key);
		}
	}

	public  function getUnset($request){
		if(isset($request["userinfo"])){
			unset($request["userinfo"]);
		}
		if(isset($request["location_x"])){
			unset($request["location_x"]);
		}
		if(isset($request["location_y"])){
			unset($request["location_y"]);
		}
		if(isset($request["openid"])){
			unset($request["openid"]);
		}
		unset($request["userInfo"]);
		unset($request["token"]);
		unset($request["api_token"]);
		return $request;
	}


}