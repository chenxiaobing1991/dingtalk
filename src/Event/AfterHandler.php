<?php


namespace Cxb\DingTalk\Event;


use Cxb\DingTalk\Contract\DriverInterface;

/**
 * Class AfterHandler
 * @package Cxb\DingTalk\Event
 */
class AfterHandler extends BaseEvent
{
    /**
     * AfterHandler constructor.
     * @param DriverInterface $driver
     * @param mixed $data
     */
    public function __construct(DriverInterface $driver, private mixed $data)
    {
        parent::__construct($driver);
    }
}