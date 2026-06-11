<?php


namespace Cxb\DingTalk\Contract;

use Cxb\DingTalk\Config;

/**
 *
 * Interface DriverInterface
 * @package Cxb\DingTalk\Contract
 */
interface DriverInterface
{
    /**
     * @return Config
     */
    public function getConfig():Config;
}