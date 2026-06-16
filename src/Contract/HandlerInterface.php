<?php


namespace Cxb\DingTalk\Contract;

/**
 *处理器契约定义
 * Interface HandlerInterface
 * @package Cxb\DingTalk\Contract
 */
interface HandlerInterface
{
    /**
     * 全局参数
     * @return mixed
     */
    public function getParsedBody(): ?array;

    /**
     * 数据注入
     * @param mixed $params
     * @return HandlerInterface
     */
    public function load(array $params): HandlerInterface;

    /**
     * 签名验签
     * @return bool
     */
    public function verifySignature(): bool;


    /**
     * @return mixed
     */
    public function handle(): mixed;

    /**
     * 成功
     * @param string $msg
     * @return mixed
     */
    public function success(mixed $data = null, string $msg = 'success'): mixed;

    /**
     * @param \Throwable $throwable
     * @return mixed
     */
    public function error(\Throwable $throwable): mixed;


}