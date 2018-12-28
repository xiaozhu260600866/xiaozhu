<?php 
namespace Xiaozhu\Wechat;
use App\Http\Requests;
use App\SiteConfig;;

class corp 
{
   public function __construct($config)
    {
        
        $this->config = $config;
    }
  /*
    取应用的access_token 类认为0
  */
   public  function getAccessToken($type=0){
    if($this->config["crop_open"]){
       $workOpen = new workOpen($this->config);
       return $workOpen->getAccessToken(getSiteName(),$type);
    }else{
        $siteConfig = SiteConfig::getLists();
        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$siteConfig["corp_id"]."&corpsecret=".$siteConfig["app_secret"];
        $data = request_get($url);
        $arr = json_decode($data);
        return $arr->access_token;
    }
  }
  //向应用发送信息
   public  function sendMessage($content,$user_id){
     $siteConfig = SiteConfig::getLists();
      $token = $this->getAccessToken();
      $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$token;
      $arr = [
        "touser"=>isset($user_id) ? $user_id :'@all',
        "msgtype"=>'text',
        "text"=>[
          "content"=>$content
        ],
        'agentid'=>$siteConfig['agent_id']
      ];
      $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true);
  }

  //向应用发出加入企业审请
   public  function joinMe($userid){
       $token = $this->getAccessToken();
       $url = "https://qyapi.weixin.qq.com/cgi-bin/batch/invite?access_token=".$token;
       $arr = [
          "user"=>array($userid)
       ];
      $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true);
  }

  //读取企业部门列表
   public  function getDepartmentLists(){
    $url="https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=".$this->getAccessToken(2);
    return json_decode(request_get($url));

  }

  //读取成员列表
  public  function getStaffLists(){
    $lists = $this->getDepartmentLists();
    $newArray = array();
    $listsKey = 0;
    foreach ($lists->department as $key => $value) {
        $url="https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=".$this->getAccessToken(2)."&department_id=".$value->id."&fetch_child=1";
        $res = json_decode(request_get($url));
        foreach ($res->userlist as $key2 => $value2) {
              $newArray[$listsKey] = $value2;
              $listsKey++;
        }
    }
    return $newArray;
  }

   //创建部门
  public  function addDepartment($arr){
     $url= "https://qyapi.weixin.qq.com/cgi-bin/department/create?access_token=".$this->getAccessToken(2);
      $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true);
  }
  public  function editDepartment($arr){
      $url= "https://qyapi.weixin.qq.com/cgi-bin/department/update?access_token=".$this->getAccessToken(2);
      $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true);  
  }

  public  function delDepartment($id){
      $url="https://qyapi.weixin.qq.com/cgi-bin/department/delete?access_token=".$this->getAccessToken(2)."&id=".$id;
       return json_decode(request_get($url),true);
  }

  public  function createStaff($arr){
      $url="https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=".$this->getAccessToken(2);
      $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true);  

  }
  public  function editStaff($arr){
      $url="https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=".$this->getAccessToken(2);
       $arr = json_encode($arr);
      $res = request_post($url,$arr);
      return json_decode($res,true); 

  }
  public  function delStaff($userId){
      $url="https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=".$this->getAccessToken(2)."&userid=".$userId;
      return json_decode(request_get($url),true);

  }





}

?>