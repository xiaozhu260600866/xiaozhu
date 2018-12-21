<?php 
namespace Xiaozhu\Wechat;
use App\Http\Requests;
use Illuminate\Config\Repository;
class app 
{
   public function __construct(Repository $config)
    {
        
        $this->config = $config;
    }

  public  function getAccessToken(){
    $wechat     = app('wechat.mini_program.default');
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wechat['config']["app_id"] ."&secret=". $wechat['config']["secret"];
    $data = request_get($url);
    $arr = json_decode($data);
    return $arr->access_token;
  }

  public  function getOpenId($request){
        $wechat     = app('wechat.mini_program.default');
         $url     = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $wechat['config']["app_id"] . '&secret=' . $wechat['config']["secret"] . '&js_code=' . $request->code . '&grant_type=authorization_code';
      
        $content = request_get($url);
       
        $content = json_decode($content);
        if(isset($content->openid)){
          return $content->openid;
        }else{
          return false;
        }
  }
   public  function getOpenIdAll($request){
        $wechat     = app('wechat.mini_program.default');
         $url     = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $wechat['config']["app_id"] . '&secret=' . $wechat['config']["secret"] . '&js_code=' . $request->code . '&grant_type=authorization_code';
        $content = request_get($url);
        $content = json_decode($content);
        if(isset($content->openid)){
          return $content;
        }else{
          return false;
        }
  }

 

}

?>