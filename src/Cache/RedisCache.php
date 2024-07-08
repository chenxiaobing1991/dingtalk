<?php


namespace Cxb\DingTalk\Cache;

use Hyperf\Redis\Redis;

/**
 * mix \Redis
 * Class RedisCache
 * @package Cxb\DingTalk\Cache
 */
class RedisCache implements CacheInterface
{
    public function __construct(protected Redis $redis){

    }

    /**
     *
     * @param string $name
     */
    public function  get(string $name)
    {
       return $this->redis->get($name);
    }

    /**
     * 设置缓存
     * @param string $name
     * @param $data
     */
    public function set(string $name, $data,$ttl)
    {
        $this->redis->set($name,$data,$ttl);
    }
}