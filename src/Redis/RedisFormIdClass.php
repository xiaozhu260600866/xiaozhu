<?php namespace Xiaozhu\Redis;
use Illuminate\Support\Facades\Redis;
use Auth;
class RedisFormIdClass{
	/*取redis 的表为user_id 全部数据*/
	 public function __construct($config)
    {
        
        $this->config = $config;
       
    }
   
	public  function getAll($user_id){
		$lists = Redis::hgetall($user_id);
		
		return $lists;
	}

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	public  function getAdd($user_id,$form_id){
		//$redis->hmset('hash1', array('key3' => 'v3', 'key4' => 'v4'));
	    if($form_id == "the formId is a mock one") return true;
		$key = $form_id;
		
		Redis::hset($user_id,$key,$form_id);
	}

	/*参数一:表名*/
	public  function getDel($user_id){
		$lists = Redis::hgetall($user_id);
		foreach ($lists as $key => $value) {
			Redis::hdel($user_id,$key);
		}
		
	}
	/*参数一:表名*/
	/*参数二：键名*/
	public  function isExists($user_id,$form_id){
		return  Redis::hexists($user_id,$form_id);
	}
	//删除一个商品;
	//参数一表名,
	//键名
	public  function getDelOne($user_id,$form_id){
		Redis::hdel($user_id,$form_id);
	}

}