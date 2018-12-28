<?php
namespace Xiaozhu\Facades;
use Illuminate\Support\Facades\Facade;
class WechatOpen extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wechatOpen';
    }
}
