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
     * 生成签名
     * @return string
     */
    public function generateSignature(): string;


    /**
     * @return mixed
     */
    public function handle(): mixed;


}