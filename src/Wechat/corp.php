<?php 
namespace Xiaozhu\Wechat;
use App\Http\Requests;
use Illuminate\Config\Repository;
class corp 
{
   public function __construct(Repository $config)
    {
        
        $this->config = $config;
    }

  public  function getAccessToken(){
    $wechat     = app('wechat.work.default');
    $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$wechat["corp_id"]."&corpsecret=".$wechat["secret"];
    $data = request_get($url);
    $arr = json_decode($data);
    return $arr->access_token;
  }

 

}

?>