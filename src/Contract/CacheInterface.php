<?php


namespace Cxb\DingTalk\Contract;

/**
 * 定义缓存契约
 * Interface CacheInterface
 * @package Cxb\DingTalk\Contract
 */
interface CacheInterface
{
    /**
     * 获取缓存
     * @param string $name
     * @return mixed
     */
    public function get(string $name): mixed;//获取缓存

    /**
     * 设置缓存
     * @param string $name
     * @param string $str
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $name, string $str, ?int $ttl = null): bool;//设置缓存

    /**
     * 删除缓存
     * @param string $name
     * @param mixed ...$names
     * @return bool
     */
    public function delete(string $name, ...$names): bool;


    public function exists(string $name): bool;
}