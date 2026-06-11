<?php


namespace Cxb\DingTalk;

use Cxb\DingTalk\Cache\FileCache;
use Cxb\DingTalk\Contract\CacheInterface;

/**
 *配置类
 * Class Config
 * @package Cxb\HyperfDingTalk
 */
final class Config
{

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(private array $config)
    {
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed
     */
    public function get(string $name, $default = null): mixed
    {
        return $this->config[$name] ?? $default;
    }


    /**
     * @return string|null
     */
    public function getAppId(): ?string
    {
        return (string)$this->get('app_id');
    }

    /**
     * @return string|null
     */
    public function getAppSecret(): ?string
    {
        return (string)$this->get('app_secret');
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return (string)$this->get('uri', 'https://oapi.dingtalk.com');
    }

    /**
     * 获取缓存
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        $cache = $this->get('cache');
        return !$cache instanceof CacheInterface ? new FileCache() : $cache;
    }
}