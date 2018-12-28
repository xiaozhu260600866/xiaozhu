<?php 
namespace Xiaozhu\Wechat;
use App\Http\Requests;
use App\User;

class app 
{
   public function __construct($config)
    {
        
        $this->config = $config;
    }

  public  function getAccessToken(){
    if($this->config["wechat_open"]){
       $wechatOpen = new wechatOpen($this->config);
       return $wechatOpen->getAccessTokenRedis();
    }else{
        $wechat     = app('wechat.mini_program.default');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$wechat['config']["app_id"] ."&secret=". $wechat['config']["secret"];
        $data = request_get($url);
        $arr = json_decode($data);
        return $arr->access_token;
    }
  }
   public  function getOpenIdAll($request){
     if($this->config["wechat_open"] == 0){
        $wechat     = app('wechat.mini_program.default');
         $url     = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $wechat['config']["app_id"] . '&secret=' . $wechat['config']["secret"] . '&js_code=' . $request->code . '&grant_type=authorization_code';
        $content = request_get($url);
        $content = json_decode($content);
        if(isset($content->openid)){
          return $content;
        }else{
          return false;
        }
     }else{
        $wechatOpen = new wechatOpen($this->config);
        $open_platform     = app('wechat.open_platform.default');
        $wechat     = app('wechat.official_account.default');
        $url = "https://api.weixin.qq.com/sns/component/jscode2session?appid=". $wechat['config']["app_id"]."&js_code=".$request->code."&grant_type=authorization_code&component_appid=".$open_platform['config']['app_id']."&component_access_token=".$wechatOpen->getComponentAccessToken();
        
        $data= json_decode(request_get($url));
        return $data;
     }   
  }

  //取微信小程序的二维码

  public function getCode($scene,$page){
     $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$this->getAccessToken();
     $data=array(
            "scene"=>$scene,
            "page"=>$page
        );
      $data = json_encode($data);
      $data=request_post($url,$data);
      return $data;
  }

  //发送小程序模板消息

  public  function appSendTemplate($touser, $user_id,$template_id,$url, $data, $topcolor = '#7B68EE'){
     
       $userForm =\RedisFromId::getAll($user_id);
       $user = User::where("id",$user_id)->first();
       foreach ($userForm as $key => $userFormid) {
          if(strpos($userFormid,'{') !== false){
          }else{
            //判断时间
            $toDay = strtotime(date("Y-m-d H:i:s"));
            $formId7day = strtotime("+7 day",strtotime($key));
            if($toDay < $formId7day){
               $template = array(
                    'touser' => $user->openid,
                    'template_id' => $template_id,
                     'page' => $url,
                    'form_id'=>$userFormid,
                    'data' => $data,
                    'color' => $topcolor
                  );
                  $json_template = json_encode($template);
                  $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$this->getAccessToken();
                  $dataRes = request_post($url, urldecode($json_template));
                  $dataRes = json_decode($dataRes,true);
                   \RedisFromId::getDelOne($user_id,$key);
                  if ($dataRes['errcode'] == 0) {
                      return true;
                  }else{
                    return false;
                  }
              
            }else{
                 \RedisFromId::getDelOne($user_id,$key);
            }
          }

       }
        return false;
  }

 

}

?>