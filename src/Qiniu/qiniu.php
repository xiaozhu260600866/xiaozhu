<?php 
namespace Xiaozhu\Qiniu;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

	class qiniu{
		 public function __construct($config)
	    {
	        
	        $this->config = $config;
	       
	    }
		protected  function init(){
			require_once __DIR__ . '/autoload.php';
		}
		public   function upload($filename){
			$this->init();
			$accessKey = $this->config["qiniu_access_key"];
			$secretKey = $this->config["qiniu_secret_key"];
			$bucket=$this->config["qiniu_bucket"];
			$auth = new Auth($accessKey, $secretKey);
			
			$uptoken = $auth->uploadToken($bucket, null, 3600);
			//上传文件的本地路径
			$filePath =$filename;

			//指定 config
			// $uploadMgr = new UploadManager($config);
			$uploadMgr = new UploadManager();

			list($ret, $err) = $uploadMgr->putFile($uptoken, null, $filePath);
			
			if ($err !== null) {
			   
			    return $err;
			} else {
			   
			    return $ret;
			}

		}	
	}
?>