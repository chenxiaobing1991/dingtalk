<?php


declare(strict_types=1);

namespace Cxb\DingTalk\Cache;

use Cxb\DingTalk\Contract\CacheInterface;


/**
 * Class FileCache
 * @package Cxb\DingTalk\Cache
 * @property $path
 */
class FileCache implements CacheInterface
{
    /**
     * FileCache constructor.
     * @param string|null $path
     */
    public function __construct(private ?string $path = null)
    {
        $this->path = $path === null ? dirname(__DIR__, 2) . '/cache' : $path;

    }

    /**
     *
     */
    private function init()
    {
        if (!is_dir($this->path))
            mkdir($this->path, '0755');
    }

    /**
     * @param string $name
     * @return string
     */
    private function tranferPath(string $name): string
    {
        return $this->path . '/' . md5($name);
    }

    /**
     * 获取配置
     * @param string $name
     */
    public function get(string $name): mixed
    {
        $data = @file_get_contents($this->tranferPath($name));
        if ($data) {
            $data = json_decode($data, true);
            return $data['expire'] !== null && $data['expire'] < time() ? null : $data['access_token'];
        }
        return $data;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return (bool)@file_get_contents($this->tranferPath($name));
    }

    /**
     * @param string $name
     * @param string $str
     * @param int|null $ttl
     * @return bool
     */
    public function set(string $name, string $str, ?int $ttl = null): bool
    {
        $info = [
            'expire' => $ttl === null ? null : $ttl + time(),
            'access_token' => $str
        ];
        return (bool)@file_put_contents($this->tranferPath($name), json_encode($info));
    }

    /**
     * @param string $name
     * @param mixed ...$names
     * @return bool
     */
    public function delete(string $name, ...$names): bool
    {
        @unlink($this->tranferPath($name));
        foreach ($names as $value) {
            @unlink($this->tranferPath($value));
        }
        return true;
    }
}