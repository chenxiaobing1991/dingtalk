<?php


namespace Cxb\DingTalk\Handler;

/**
 * OA审批回调处理器
 * Class ProcessHandler
 * @package Cxb\DingTalk\Handler
 */
class ProcessHandler extends WebHookHandler
{

    /**
     * 过程处理器
     * @return mixed
     */
    protected function process(): mixed
    {
        return $this->decode();
    }
}