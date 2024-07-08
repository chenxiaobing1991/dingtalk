<?php


namespace Cxb\DingTalk\Cache;

/**
 * mix \Redis
 * Class RedisCache
 * @package Cxb\DingTalk\Cache
 */
class RedisCache implements CacheInterface
{
    private $redis;//缓存配置
    public function __construct($redis){
         $this->redis=$redis;
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
    public function set(string $name, $data,$expire)
    {
        $this->redis->set($name,$data);
    }
}