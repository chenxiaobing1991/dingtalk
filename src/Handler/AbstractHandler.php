<?php

namespace Cxb\DingTalk\Handler;

use Cxb\DingTalk\Contract\HandlerInterface;
use Cxb\DingTalk\Contract\ManagerInterface;
use Cxb\DingTalk\Exception\BusinessException;

/**
 * Class AbstractHandler
 * @package Cxb\DingTalk\Handler
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * AbstractHandler constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(protected ManagerInterface $manager, protected ?array $params = null)
    {

    }

    /**
     * 执行
     * @return mixed
     */
    abstract protected function process(): mixed;

    /**
     * @param mixed $params
     * @return HandlerInterface
     */
    public function load(mixed $params): HandlerInterface
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array|null
     */
    final public function getParsedBody(): ?array
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    public function verifySignature(): bool
    {
        return true;
    }

    /**
     * 验证器
     * @return bool
     * @throws BusinessException
     */
    public function validate(): bool
    {
        if (!$this->verifySignature())
            throw new BusinessException('验签失败');
        return true;
    }

    /**
     * @return mixed
     */
    public function handle(): mixed
    {
        if ($this->validate()) {
            return $this->process();
        }
        return null;
    }
}