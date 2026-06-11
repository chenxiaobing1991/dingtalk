<?php
declare(strict_types=1);

namespace Cxb\DingTalk;

use Cxb\DingTalk\Contract\DriverInterface;
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
    function getContainer(): ContainerInterface
    {
        return ApplicationContext::getContainer();
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
    public function getAccessToken()
    {
        $params = ['appkey' => $this->config->getAppId(), 'appsecret' => $this->config->getAppSecret()];
        $key = md5(json_encode($params));
        $accessToken = $this->config->getCache()->get($key);
        if (!$accessToken) {
            $body = $this->request('get', '/gettoken?' . http_build_query($params));
            $accessToken = $body['access_token'];
            $this->config->getCache()->set($key, $accessToken, $body['expires_in']);
        }
        return $accessToken;
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
        try {
            $request = new RequestClient($method, $this->config->getUri() . $path, $body, $headers);
            $this->getEventDispatcher()?->dispatch(new BeforeHandler($this, $request));
            $result = $this->handleResponse(ClientFactory::send($request));
            $this->getEventDispatcher()?->dispatch(new AfterHandler($this, $result));
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
        if ($body['errcode'] != 0)
            return new BusinessException($body['errmsg'], $body['errcode']);
        return $body;
    }

}