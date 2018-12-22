<?php
namespace Xiaozhu\Facades;
use Illuminate\Support\Facades\Facade;
class RedisFormIdClass extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'redisFormId';
    }
}
