<?php
namespace Xiaozhu\Facades;
use Illuminate\Support\Facades\Facade;
class RedisHistoryClass extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redisHistory';
    }
}
