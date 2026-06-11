<?php


namespace Cxb\DingTalk\Contract;

/**
 * Interface ManagerInterface
 * @package Cxb\DingTalk\Contract
 */
interface ManagerInterface
{
    public function get(string $name): DriverInterface;
}