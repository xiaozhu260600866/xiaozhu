<?php
namespace Xiaozhu\Facades;
use Illuminate\Support\Facades\Facade;
class RedisTokenClass extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redisToken';
    }
}
