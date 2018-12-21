<?php
use App\Http\Redis\RedisClassSite;



function getConfig($key)
{
    return RedisClassSite::getOne(getSiteName(), $key);
}

function base64EncodeImage($image_file)
{
    $base64_image = '';
    $image_info   = getimagesize($image_file);
    $image_data   = fread(fopen($image_file, 'r'), filesize($image_file));
    $base64_image = 'pic=' . chunk_split(base64_encode($image_data));
    return $base64_image;
}

function importCover($cover)
{
    $newCover = "";
    foreach ($cover as $key => $value) {
        if(isset($value["name"])){
            if (isset($value["response"])) {
                $newCover .= $value["response"]["filename"] . ",";
            } else {
                $newCover .= $value["name"] . ",";
            }
         }

    }
    return trim($newCover, ",");
}
function splitCover($cover, $path)
{
    $newCover = [];
    foreach ($cover as $key => $value) {
        $newCover[$key] = url('/upload/images/' . $path . '/' . $value);
    }
    return $newCover;
}
function request_post($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }
    $postUrl  = $url;
    $curlPost = $param;
    $ch       = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl); //抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch); //运行curl
    curl_close($ch);
    return $data;
}

function request_get($url = '')
{
    if (empty($url)) {
        return false;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}


function getOrderNo()
{
    $no = "";
    if (!\Cache::has(date("Ymd"))) {
        \Cache::forever(date("Ym"), "000");
    }

    $num = \Cache::increment(date("Ymd"));
    $num = str_pad($num, 3, "0", STR_PAD_LEFT);
    $no .= date("Ymd") . $num;
    \Cache::forever(date("Ymd"), $num);
    return $no;
}

function getUser($request)
{
    return json_decode($request->userInfo);
}

function getDiqu()
{
    return array(
        [
            'label' => '江门市',
            'value' => '江门市',
        ],
        [
          'label' => '开平市',
           'value' => '开平市',
        ],
        [
            'label' => '佛山市',
            'value' => '佛山市',
        ],
        [
            'label' => '东莞市',
            'value' => '东莞市',
        ],
        [
            'label' => '恩平市',
            'value' => '恩平市',
        ],
         [
            'label' => '惠州市',
            'value' => '惠州市',
        ],
         [
            'label' => '中山市',
            'value' => '中山市',
        ],
          [
            'label' => '广州市',
            'value' => '广州市',
        ],

         [
            'label' => '云浮市',
            'value' => '云浮市',
        ],
         [
            'label' => '茂名市',
            'value' => '茂名市',
        ],
         [
            'label' => '湛江市',
            'value' => '湛江市',
        ],
         [
            'label' => '绍关市',
            'value' => '绍关市',
        ],
         [
            'label' => '南宁市',
            'value' => '南宁市',
        ],
       
        [
            'label' => '平顶山',
            'value' => '平顶山',
        ],
    [
            'label' => '郑州市',
            'value' => '郑州市',
        ],
        [
            'label' => '长沙市',
            'value' => '长沙市',
        ],
        [
            'label' => '杭州市',
            'value' => '杭州市',
        ],
        [
            'label' => '深圳市',
            'value' => '深圳市',
        ],
      


    );
}

function getDiquOne($value)
{
    $diquArr = getDiqu();
    foreach ($diquArr as $key => $v) {
        if ($v["value"] == $value) {
            return $v["label"];
        }

    }

}


function time_tranx($the_time){  
   $now_time = date("Y-m-d H:i:s",time());

   $now_time = strtotime($now_time);  
   $show_time = strtotime($the_time);  
   $dur = $now_time - $show_time;  
   if($dur < 0){  
        return date("Y-m-d H:i:s",strtotime($the_time));  
   }else{  
        if($dur < 60){  
         return '刚刚';  
        }else{  
             if($dur < 3600){  
              return floor($dur/60).'分钟前';  
             }else{  
                  if($dur < 86400){  
                     return floor($dur/3600).'小时前';  
                  }else{ 
                       //259200 
                       if($dur < 259200){ //90天内  
                            return floor($dur/86400).'天前';  
                       }else{  
                              return date("Y-m-d",strtotime($the_time));   
                       }  
                  }  
            }  
        }  
   }  
}

function callBackTest($fun){
     $a="xiaozhu";
     return $fun($a);

}
/**
  callBackTest(function($a){
        echo $a; //xiaozhu
  });
**/ 

function getClientIp()
{
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }
    return $onlineip;
}

/**
 * 加解密的密钥
 * @return string
 */
function signKey() {
    return pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a0511");
}

/**
 * 对问答信息进行签名
 * @param $info
 */
function signQuestion($info) {
    $key = signKey();
//    $key_size =  strlen($key);
//    echo "Key size: " . $key_size . "\n";

    $plaintext = json_encode($info);

    # 为 CBC 模式创建随机的初始向量
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);


    # 创建和 AES 兼容的密文（Rijndael 分组大小 = 128）
    # 仅适用于编码后的输入不是以 00h 结尾的
    # （因为默认是使用 0 来补齐数据）
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
        $plaintext, MCRYPT_MODE_CBC, $iv);

    # 将初始向量附加在密文之后，以供解密时使用
    $ciphertext = $iv . $ciphertext;
    # 对密文进行 base64 编码
    $ciphertext_base64 = base64_encode($ciphertext);
    return $ciphertext_base64;
}

/**
 * 将字符串解开，得到问答信息
 * @param $ciphertext
 */
function unsignQuestion($ciphertext_base64) {
    $key = signKey();


    # === 警告 ===
    # 密文并未进行完整性和可信度保护，
    # 所以可能遭受 Padding Oracle 攻击。
    # --- 解密 ---

    $ciphertext_dec = base64_decode($ciphertext_base64);

    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    # 初始向量大小，可以通过 mcrypt_get_iv_size() 来获得
    $iv_dec = substr($ciphertext_dec, 0, $iv_size);

    # 获取除初始向量外的密文
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

    # 可能需要从明文末尾移除 0
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,
        $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

    return $plaintext_dec;
}


function getLocation($lng1,$lat1,$lng2,$lat2){
        //将角度转为狐度
        @$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
        @$radLat2=deg2rad($lat2);
        @$radLng1=deg2rad($lng1);
        @$radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;
        $b=$radLng1-$radLng2;
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
        $res=floor($s);
       return $res;
    }


function getDoubleData($data){
      $indexLinkNew = [];
        $countKey =5;
        $indexKey = 0;
        for ($i=0; $i < count($data) ; $i++) { 
            if($countKey == $i){
                $indexKey++;
                $countKey+=$countKey;
            }
            $indexLinkNew[$indexKey][$i]=$data[$i];
        }
        return $indexLinkNew;
}

function getCode($code){
    $charset = '1234567890';
    $_len = strlen($charset) - 1;
    for ($i = 0;$i < 3;++$i) {
        $code .= $charset[mt_rand(0, $_len)];
    }
    return $code;
}



