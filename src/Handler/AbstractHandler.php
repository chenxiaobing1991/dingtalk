<?php

namespace Cxb\DingTalk\Handler;

use Cxb\DingTalk\Contract\HandlerInterface;
use Cxb\DingTalk\Contract\ManagerInterface;
use Cxb\DingTalk\Exception\BusinessException;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerInterface;

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
     * @param array $params
     * @return HandlerInterface
     */
    final public function load(array $params): HandlerInterface
    {
        $this->params = $params;
        return $this;
    }

    /**
     * 执行
     * @return mixed
     */
    abstract public function process(): mixed;

    /**
     * @return array|null
     */
    public function getParsedBody(): ?array
    {
        return $this->params;
    }

    /**
     * @param string|null $name
     * @param null $default
     */
    public function get(?string $name = null, $default = null)
    {
        $params = $this->getParsedBody();
        return $name === null ? $params : ($params[$name] ?? $default);
    }


    /**
     * @return bool
     */
    public function verifySignature(): bool
    {
        return true;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public function getRandomStr(int $length = 6)
    {
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }
        return $bytes;
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
        try {
            $result = $this->process();
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
        return $result;
    }
}