<?php 
namespace Xiaozhu\Wechat;
use Illuminate\Support\Facades\Redis;
use App\Workauthorizer;
class workOpen 
{
   public function __construct($config)
    {
        
        $this->config = $config;
    }

  public  function getSuiteId($type){
     $wechat     = app('wechat.open_platform.work');
     if($type == 0){
           $suite_id = $wechat['config']["suite_id"];
      }elseif($type == 1){
          $suite_id = $wechat['config']["boss_suite_id"];
      }elseif($type == 2){
          $suite_id = $wechat['config']["address_suite_id"];
      }
      return $suite_id;
  }
   public  function getSuiteSecret($type){
     $wechat     = app('wechat.open_platform.work');
     if($type == 0){
           $suite_id = $wechat['config']["suite_secret"];
      }elseif($type == 1){
          $suite_id = $wechat['config']["boss_suite_secret"];
      }elseif($type == 2){
          $suite_id = $wechat['config']["address_suite_secret"];
      }
      return $suite_id;
  }
  
  //get_provider_token获取服务商凭证
  public  function getProviderToken(){
  	  $wechat     = app('wechat.open_platform.work');
      $corpid = $wechat["config"]["corp_id"];
      $provider_secret=$wechat["config"]["provider_secret"];
      $url="https://qyapi.weixin.qq.com/cgi-bin/service/get_provider_token";
      $data=array("corpid"=>$corpid,"provider_secret"=>$provider_secret);
     return  json_decode(request_post($url,json_encode($data)));
  }

  //suite_access_token 获取第三方应用凭证
  public  function getSuiteAccessToken($type=0){
  	  $wechat     = app('wechat.open_platform.work');
      $suite_id = $this->getSuiteId($type);
      $suite_secret=$this->getSuiteSecret($type);
      $url="https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token";
      $data=array("suite_id"=>$suite_id,"suite_secret"=>$suite_secret,"suite_ticket"=>Redis::hget("card_website","work_suiteTicket_".$type));
    
     return  json_decode(request_post($url,json_encode($data)))->suite_access_token;
  }
  //pre_auth_code
  public  function getPreAuthCode($type=0){
  	$url="https://qyapi.weixin.qq.com/cgi-bin/service/get_pre_auth_code?suite_access_token=".$this->getSuiteAccessToken($type);
  	 return json_decode(request_get($url));
  }
  // 取获取企业凭证

  public  function getAccessToken($site_name,$type=0){
    
      $auth = Workauthorizer::where("site_name",$site_name)->where("type",$type)->first();
      $url = "https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=".$this->getSuiteAccessToken($type);
      $data=array(
            "auth_corpid"=>$auth->auth_corpid,
            "permanent_code"=>$auth->permanent_code
        );
       return  json_decode(request_post($url,json_encode($data)))->access_token;
  }


  public  function getAuthCode($authCodeTest,$type=0){
  	$url = "https://qyapi.weixin.qq.com/cgi-bin/service/get_permanent_code?suite_access_token=".$this->getSuiteAccessToken($type);
  	$data=array(
            "auth_code"=>$authCodeTest,
        );
  	return  json_decode(request_post($url,json_encode($data)));
  }



}

?>