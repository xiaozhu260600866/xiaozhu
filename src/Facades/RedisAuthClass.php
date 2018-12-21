<?php
namespace Xiaozhu\Facades;
use Illuminate\Support\Facades\Facade;
class RedisAuthClass extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redisAuth';
    }
}
