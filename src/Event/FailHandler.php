<?php


namespace Cxb\DingTalk\Event;

use Cxb\DingTalk\Contract\DriverInterface;

/**
 * Class FailHandler
 * @package Cxb\DingTalk\Event
 */
class FailHandler extends BaseEvent
{
    /**
     * AfterHandler constructor.
     * @param DriverInterface $driver
     * @param mixed $data
     */
    public function __construct(DriverInterface $driver, private \Throwable $throwable)
    {
        parent::__construct($driver);
    }
}