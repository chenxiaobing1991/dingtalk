<?php
declare(strict_types=1);

namespace Cxb\DingTalk;

use Cxb\DingTalk\Contract\DriverInterface;
use Cxb\DingTalk\Driver\AuthDriver;
use Cxb\DingTalk\Event\AfterHandler;
use Cxb\DingTalk\Event\BeforeHandler;
use Cxb\DingTalk\Event\FailHandler;
use Cxb\GuzzleHttp\ClientFactory;
use Cxb\GuzzleHttp\RequestClient;
use Cxb\GuzzleHttp\ResponseClient;
use Cxb\DingTalk\Exception\BadQueryDingTalkException;
use Cxb\DingTalk\Exception\BusinessException;
use Psr\Container\ContainerInterface;
use Hyperf\Context\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;
use \Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * Class AbstractDriver
 * @package Cxb\DingTalk
 */
abstract class AbstractDriver implements DriverInterface
{
    /**
     * AbstractDriver constructor.
     * @param Config $config
     * @param Driver $driver
     */
    public function __construct(protected Config $config, protected Driver $driver)
    {

    }

    /**
     * @return Config
     */
    final public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * 容器实例
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

    /**
     * @param array $params
     * @param array $rules
     * @param array $message
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function validate(array $params, array $rules, array $message = []): void
    {
        $validator = $this->getContainer()->get(ValidatorFactoryInterface::class);
        $validator = $validator->make($params, $rules, $message);
        if ($validator->fails()) {
            throw new BusinessException($validator->errors()->first());
        }
    }


    /**
     * 时间驱动调度器
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->getContainer()->get(EventDispatcherInterface::class);
    }

    /**
     * 获取令牌
     * @return string
     */
    public function getAccessToken(): ?string
    {
        return $this->driver->get(AuthDriver::class)->getAccessToken();
    }

    /**
     * @param string $path
     * @param string $method
     * @param null $body
     * @param array $headers
     * @return mixed
     */
    protected function request(string $path, string $method = 'GET', $body = null, array $headers = []): mixed
    {
        return $this->baseRequest($this->config->getUri() . $path, $method, $body, $headers);
    }

    /**
     * @param string $url
     * @param string $method
     * @param null $body
     * @param array $headers
     * @return mixed
     */
    final protected function baseRequest(string $url, string $method = 'GET', $body = null, array $headers = []): mixed
    {
        try {
            $request = new RequestClient($method, $url, $body, $headers);
            $this->getEventDispatcher()?->dispatch(new BeforeHandler($this, $request));
            $result = $this->handleResponse(ClientFactory::send($request));
            $this->getEventDispatcher()?->dispatch(new AfterHandler($this, $result));
            return $result;
        } catch (\Throwable $throwable) {
            $this->getEventDispatcher()?->dispatch(new FailHandler($this, $throwable));
            throw $throwable;
        }
    }

    /**
     * @param ResponseClient $responseClient
     */
    protected function handleResponse(ResponseClient $response)
    {
        if ($response->statusCode != 200)
            throw new BadQueryDingTalkException($response->error);
        $body = is_array($response->body) ? $response->body : json_decode($response->body, true);
        if (isset($body['errcode'])&&$body['errcode'] != 0)
            throw new BusinessException($body['errmsg'], $body['errcode']);
        return $body;
    }

}