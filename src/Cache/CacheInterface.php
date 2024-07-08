<?php


namespace Cxb\DingTalk\Cache;

/**
 * 缓存接口
 * Interface CacheInterface
 * @package Cxb\DingTalk\Cache
 */
interface CacheInterface
{
   public function get(string $name);//获取缓存
   public function set(string $name,$data,$ttl);//设置缓存
}