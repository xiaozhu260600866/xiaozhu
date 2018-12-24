<?php namespace Xiaozhu\Redis;
use Illuminate\Support\Facades\Redis;
use Auth;
class RedisAuthClass{
	protected   $database='card_'; //键名前缀 
	protected $redisHistory ;
	  public function __construct($config)
    {
        
        $this->config = $config;
        $this->database = $config["redis_auth_database"];
        $this->redisHistory = new RedisHistoryClass( $this->config);
    }
   

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	
	public  function getAdd($tablename,$model){
		//$redis->hmset('hash1', array('key3' => 'v3', 'key4' => 'v4'));
		$key = $this->database.$tablename;
		$id = $model->id;
		\Log::info($tablename);
		$this->redisHistory->getDelAll($tablename);
		Redis::hset($key,$id,json_encode($model));
	}


	public  function getLists($lists,$tablename){
		$newLists=array();
		foreach ($lists->data as $key => $value) {
			 $res = json_decode(Redis::hget($this->database.$tablename,$value->id));
			 if($res){
			 	$newLists[$key] = json_decode(Redis::hget($this->database.$tablename,$value->id));
			 }
		}
		$lists->data= $newLists;
		return $lists;
	}
	

	public  function getSiteConfig($lists,$tablename){
		$newLists=array();
		 $arr=[];
		foreach ($lists as $key => $value) {
			$newLists[$key] = json_decode(Redis::hget($this->database.$tablename,$value->id));
			if($newLists[$key]->name == "company_logo" || $newLists[$key]->name == "wenhua_logo"){
                $arr[$newLists[$key]->name."arr"] = explode(",", $newLists[$key]->value);    
             }
             $arr[$newLists[$key]->name] = $newLists[$key]->value;
		}
		return $arr;
	}
	public  function getDel($tablename,$model){
		$this->redisHistory->getDelAll($tablename);
		return  Redis::hdel($this->database.$tablename,$model->id);
	}

	public  function getFind($tablename,$id){
		return  json_decode(Redis::hget($this->database.$tablename,$id));
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