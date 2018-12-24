<?php


namespace Xiaozhu\Sms;

class M3Result {

  public $status;
  public $message;
  public $code;

  public function toJson()
  {
    return json_encode($this, JSON_UNESCAPED_UNICODE);
  }

}
