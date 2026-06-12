<?php


namespace Cxb\DingTalk\Contract;

use Cxb\DingTalk\Config;

/**
 * Interface ManagerInterface
 * @package Cxb\DingTalk\Contract
 */
interface ManagerInterface
{
    public function get(string $name): DriverInterface;

    /**
     * 获取配置
     * @return Config
     */
    public function getConfig():Config;
}