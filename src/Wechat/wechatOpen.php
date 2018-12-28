<?php 
namespace Xiaozhu\Wechat;
use Illuminate\Support\Facades\Redis;
use App\Authorizer;
use App\AuthorizerCode;
class wechatOpen 
{
  public function __construct($config)
    {
        
        $this->config = $config;
    }

  public   function getAccessTokenRedis(){
     //\Log::info("site_name".getSiteName());
     $auth = Authorizer::where("site_name",getSiteName())->first();
     $access_token =  $auth->access_token;
     $url="https://api.weixin.qq.com/wxa/get_page?access_token=".$access_token;
     $res= json_decode(request_get($url));

     //如果access_token过期,就重新取access_token
    if(isset($res->errcode) && $res->errcode !=0){
      $data = $this->getRefreshToken();
      $auth->access_token = $data->authorizer_access_token;
      $auth->refresh_token = $data->authorizer_refresh_token;
      $auth->save();
    }
    return $auth->access_token;

      //return Redis::hget("card_website","authorizer_access_token");
  }
 
  public  function getComponentAccessToken(){

      $url  = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";
       $wechat     = app('wechat.open_platform.default');
      $data=array(
          "component_appid"=>$wechat['config']["app_id"],
          "component_appsecret"=>$wechat['config']["secret"],
          "component_verify_ticket"=>Redis::hget($this->config['componentVerifyTicket'],"componentVerifyTicket")
        );
    
      return json_decode(request_post($url,json_encode($data)))->component_access_token;
  }

  //取预授权码
  public  function getPreAuthCode(){
  	 $wechat     = app('wechat.open_platform.default');
  	$component_access_token = $this->getComponentAccessToken();
  	$url = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=".$component_access_token;
  	$data=array(
  	 		"component_appid"=>$wechat['config']["app_id"],
  	 	);
  	return json_decode(request_post($url,json_encode($data)))->pre_auth_code;
  }

  public  function getAccessToken($auth_code){
     $wechat     = app('wechat.open_platform.default');
  	$url = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=".$this->getComponentAccessToken();
    $data=array(
           "component_appid"=>$wechat['config']["app_id"],
           "authorization_code"=>$auth_code
      );
      return json_decode(request_post($url,json_encode($data)));
  }
  //用于access_token过期，自动获取
  public  function getRefreshToken(){
      $auth = Authorizer::where("site_name",getSiteName())->first();
      $wechat     = app('wechat.open_platform.default');
      $url = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=".$this->getComponentAccessToken();
      $data=array(
          "component_appid"=>$wechat['config']["app_id"],
          "authorizer_appid"=>$auth->app_id,
          "authorizer_refresh_token"=>$auth->refresh_token
        );
       return json_decode(request_post($url,json_encode($data)));

  }

  //修改小程序服务器域名
  public  function editDomain($data){
    $url="https://api.weixin.qq.com/wxa/modify_domain?access_token=".$this->getAccessTokenRedis();

    return json_decode(request_post($url,json_encode($data)));
  }

  //绑定微信用户为小程序体验者

  public  function bindTester($wechatid){
    $url="https://api.weixin.qq.com/wxa/bind_tester?access_token=".$this->getAccessTokenRedis();

    $data=array(
        "wechatid"=>$wechatid,
      );
      return json_decode(request_post($url,json_encode($data)));
  }


  //获取小程序信息设置
  public  function getAppInfo(){
      $url="https://api.weixin.qq.com/cgi-bin/account/getaccountbasicinfo?access_token=".$this->getAccessTokenRedis();
      return json_decode(request_get($url));
  }
  //获取小程序上传的代码
  public  function getCodeLists(){
    $url="https://api.weixin.qq.com/wxa/gettemplatelist?access_token=".$this->getComponentAccessToken();
     return json_decode(request_get($url));
  }
  
  //小程序上传代码
  public  function getTemplateUpload($data){
      $url = "https://api.weixin.qq.com/wxa/commit?access_token=".$this->getAccessTokenRedis();
      $data = json_encode($data);
       return json_decode(request_post($url,$data));
  }
  //获取授权小程序帐号的可选类目
  public  function getCategory(){
    $url="https://api.weixin.qq.com/wxa/get_category?access_token=".$this->getAccessTokenRedis();
    //dd(json_decode(request_get($url)));
    return json_decode(request_get($url));
  }

  //获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用
  public  function getPages(){
     $url="https://api.weixin.qq.com/wxa/get_page?access_token=".$this->getAccessTokenRedis();
     return json_decode(request_get($url));
  }
  //小程序代码提交审核
  public  function getCodeSubmit($address="pages/index/main"){
    $category =$this->getCategory()->category_list[0];
    $url="https://api.weixin.qq.com/wxa/submit_audit?access_token=".$this->getAccessTokenRedis();
    $data=array(
          "item_list"=>[array(
              "address"=>$address,
              "tag"=>"名片",
              "first_class"=>$category->first_class,
              "second_class"=>$category->second_class,
              "first_id"=>$category->first_id,
              "second_id"=>$category->second_id,
              "title"=>"首页"
            )]

      );
    //dd($data);
    
      $res=json_decode(request_post($url,json_encode($data,JSON_UNESCAPED_UNICODE)));
      \Log::info(json_encode($res));
      if(isset($res->errcode) && $res->errcode ==0){
        $auth = Authorizer::where("site_name",getSiteName())->first();
        $auth->auditid =$res->auditid;
        $auth->version = $this->getTemplates()->user_version;
        AuthorizerCode::createOrEdit($auth);
      }
      return $res;
  }
  //查询审核状态
  public  function checkCodeSubmitStatus($auditid){
     $url="https://api.weixin.qq.com/wxa/get_auditstatus?access_token=".$this->getAccessTokenRedis();
     $data=array("auditid"=>$auditid);
    return json_decode(request_post($url,json_encode($data)));

  }
  //查询最新一次提交的审核状态（仅供第三方代小程序调用）

  public  function checkCodeSubmitStatusNew(){
    $url="https://api.weixin.qq.com/wxa/get_latest_auditstatus?access_token=".$this->getAccessTokenRedis();
    return json_decode(request_get($url));

  }

  //15. 小程序审核撤回

  public  function codeUnload(){
    $url="https://api.weixin.qq.com/wxa/undocodeaudit?access_token=".$this->getAccessTokenRedis();
    return  json_decode(request_get($url));

  }

  //发布已通过审核的小程序（仅供第三方代小程序调用）
  public  function publishCode(){
     $url="https://api.weixin.qq.com/wxa/release?access_token=".$this->getAccessTokenRedis();
     $data="{}";
  
     return json_decode(request_post($url,$data));
  }

  //模板全部消息

  public  function getTemplateLists(){
    $url="https://api.weixin.qq.com/cgi-bin/wxopen/template/library/list?access_token=".$this->getAccessTokenRedis();
    $data=array("offset"=>0,"count"=>20);
    return json_decode(request_post($url,json_encode($data)));

  }
  //模板ID
  public  function getTemplateInfo($id="AT0309"){
    $url ="https://api.weixin.qq.com/cgi-bin/wxopen/template/library/get?access_token=".$this->getAccessTokenRedis();
    $data=array("id"=>$id);
    return json_decode(request_post($url,json_encode($data)));
  }

  //$res = \App\Tool\wechatApi\wechatOpen::addTemplate("AT0309",[5,1],"seeCard");
  //$res = \App\Tool\wechatApi\wechatOpen::addTemplate("AT0891",[2,6,3],"seeMessage");
  public  function addTemplate($id,$keyword_id_list,$name){
    $siteConfig = \App\SiteConfig::where("name",$name)->where("site_name",getSiteName())->first();
    if(!$siteConfig){
      $siteConfig = new \App\SiteConfig();
      $siteConfig->name = $name;
      $siteConfig->site_name = getSiteName();
      $siteConfig->save();
    }
    if(!$siteConfig->value){
      $url ="https://api.weixin.qq.com/cgi-bin/wxopen/template/add?access_token=".$this->getAccessTokenRedis();
      $data=array("id"=>$id,"keyword_id_list"=>$keyword_id_list);
      $res =json_decode(request_post($url,json_encode($data)));
      if($res->errmsg == "ok"){
        \App\SiteConfig::where("name",$name)->where("site_name",getSiteName())->update(["value"=>$res->template_id]);
      }
      return $res;
    }
  }
  //将小程序绑定到开放平台帐号下
  public  function appToOpen(){
    $auth = Authorizer::where("site_name",getSiteName())->first();
    $open_platform     = app('wechat.open_platform.default');
    $url = "https://api.weixin.qq.com/cgi-bin/open/bind?access_token=".$this->getAccessTokenRedis();
    $data=array(
        "appid"=>$auth->app_id,
        "open_appid"=>$open_platform["config"]["app_id"]
      );
     return json_decode(request_post($url,json_encode($data)));
  }


  /* $res = \App\Tool\wechatApi\wechatOpen::getQrcode();
         return response($res, 200, [
            'Content-Type' => 'image/png',
        ]); 生成体验版小程序*/
  public  function getQrcode($path){
     $path=urlencode($path);
     $url="https://api.weixin.qq.com/wxa/get_qrcode?access_token=".$this->getAccessTokenRedis()."&path=".$path;
    return request_get($url);
   
  }
  //获取草稿箱内的所有临时代码草稿
  public  function getAddToTmplate(){
    $url = "https://api.weixin.qq.com/wxa/gettemplatedraftlist?access_token=".$this->getComponentAccessToken();
    $lists=  json_decode(request_get($url));
    return $this->getAddToTmplate_($lists->draft_list[count($lists->draft_list)-1]->draft_id);

  }
  public  function  getAddToTmplate_($draft_id){
    $url="https://api.weixin.qq.com/wxa/addtotemplate?access_token=".$this->getComponentAccessToken();
    $data=array("draft_id"=>$draft_id);
    $res = json_decode(request_post($url,json_encode($data)));
    return $this->getTemplates();

  }
  public  function getTemplates(){
    $url ="https://api.weixin.qq.com/wxa/gettemplatelist?access_token=".$this->getComponentAccessToken();
    $lists=  json_decode(request_get($url));
    return $lists->template_list[count($lists->template_list)-1];
  }



}

?>