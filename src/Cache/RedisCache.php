<?php

declare(strict_types=1);

namespace Cxb\DingTalk\Cache;

use Hyperf\Redis\Redis;
use Cxb\DingTalk\Contract\CacheInterface;

/**
 * mix \Redis
 * Class RedisCache
 * @package Cxb\DingTalk\Cache
 */
class RedisCache implements CacheInterface
{
    /**
     * RedisCache constructor.
     * @param Redis $redis
     */
    public function __construct(protected Redis $redis)
    {

    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed
    {
        return $this->redis->get($name);
    }

    /**
     * @param string $name
     * @param mixed ...$names
     * @return bool
     */
    public function delete(string $name, ...$names): bool
    {
        return (bool)$this->redis->del($name, ...$names);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return (bool)$this->redis->exists($name);
    }

    /**
     * @param string $name
     * @param string $str
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $name, string $str, ?int $ttl = null): bool
    {
       return (bool) $this->redis->set($name, $str, $ttl);
    }
}