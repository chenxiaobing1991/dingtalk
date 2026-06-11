<?php


namespace Cxb\DingTalk\Event;

use Cxb\DingTalk\Contract\DriverInterface;
use Cxb\GuzzleHttp\RequestClient;

/**
 * Class BeforeHandler
 * @package Cxb\DingTalk\Event
 */
class BeforeHandler extends BaseEvent
{
    /**
     * BeforeHandler constructor.
     * @param DriverInterface $driver
     * @param RequestClient $equest
     */
    public function __construct(DriverInterface $driver, private RequestClient $equest)
    {
        parent::__construct($driver);
    }
}