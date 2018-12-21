<?php namespace App\Http\Controllers\Redis;
use Illuminate\Support\Facades\Redis;
use Auth;
class RedisClass{
	protected static  $prefixKey='goods_id_'; //键名前缀 

	/*取redis 的表为user_id 全部数据*/
	public static function getAll($user_id){
		$lists = Redis::hgetall($user_id);
		foreach ($lists as $key => $value) {
			$data = json_decode($value,true);
			$lists[$key]=array();
			if($data && count($data) && is_array($data)){
				foreach ($data as $key2 => $value2) {
					$key2 = (string) $key2;	
					$lists[$key][$key2] = $value2;
				}
			}
		}
		return $lists;
	}

	/*参数一：表名
	  参数二：参增加的数据,键为数据的键，值为数据的值
	  参数三：key为product_id;
	*/
	public static function getAdd($user_id,$data,$product_id){
		//$redis->hmset('hash1', array('key3' => 'v3', 'key4' => 'v4'));
		$key = self::$prefixKey.$product_id;
		Redis::hset($user_id,$key,json_encode($data));
	}

	/*参数一:表名*/
	public static function getDel($user_id){
		$lists = Redis::hgetall($user_id);
		foreach ($lists as $key => $value) {
			Redis::hdel($user_id,$key);
		}
		
	}
	/*参数一:表名*/
	/*参数二：键名*/
	public static function isExists($user_id,$goods_id){
		return  Redis::hexists($user_id,self::$prefixKey.$goods_id);

	}
	//更新购物车数量;
	//参数一表名
	//参数二：键名:
	//参数三：数量
	public static function updateNum($user_id,$goods_id,$num){
		$lists = Self::getAll($user_id);
		$data= array();
		foreach ($lists as $key => $value) {
			if($value){
				 if($value['product_id']==$goods_id){
				 	$data=[
				 		'openid'=> $value['openid'],
	                    'product_id'=>$value['product_id'],
	                    'num'=>$value['num']+=$num,
	                    'is_info'=>$value['is_info'],
	                    'siteName'=>$value['siteName'],
	                    'user_id'=>$value['user_id'],
	                    'info_id'=>$value['info_id'],
	                    'is_check'=>$value['is_check'],
				 	];
				 	
				 }
		  }

		}
		if(count($data))Self::getAdd($user_id,$data,$goods_id);
	}

	//统计购物车数量;
	//参数一表名
	
	public static function getCount($user_id){
		$num = 0;
		$lists = Self::getAll($user_id);
		foreach ($lists as $key => $value) {
				if(isset($value["num"])){
					$num+=$value['num'];
				}
			
		}
		return $num;
	}
	//删除一个商品;
	//参数一表名,
	//键名
	public static function getDelOne($user_id,$goods_id){
		Redis::hdel($user_id,self::$prefixKey.$goods_id);
	}
	//更新购物车，如果数量少于一，就删除，如果大于一，就减一
	//参数一表名
	//参数二：键名:
	//参数三：数量
	public static function updateCart($user_id,$goods_id,$num){
		$lists = Self::getAll($user_id);
		foreach ($lists as $key => $value) {
			if($value){
				if($key==self::$prefixKey.$goods_id){
					
					if($value['num']<=$num) Self::getDelOne($user_id,$goods_id);
				}else{
					$num = $value['num']-$num;
					Self::updateNum($user_id,$goods_id,$num);
				}
			}
		}

	}
	// public static function getId(){
	// 	$tmp = range(1,30);
	// 	return array_rand($tmp,10);
	// }
}