<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;
use Cxb\DingTalk\Exception\BadQueryDingTalkException;
use Cxb\GuzzleHttp\ClientFactory;

/**
 * Class AuthDriver
 * @package Cxb\DingTalk\Driver
 */
class AuthDriver extends AbstractDriver
{
    /**
     * @return string|null
     * @throws \Throwable
     */
    public function getAccessToken(): ?string
    {
        $params = ['appkey' => $this->config->getAppId(), 'appsecret' => $this->config->getAppSecret()];
        $key = md5(json_encode($params));
        $accessToken = $this->config->getCache()->get($key);
        if (!$accessToken) {
            $body = $this->request('/gettoken?' . http_build_query($params));
            $accessToken = $body['access_token'];
            $this->config->getCache()->set($key, $accessToken, $body['expires_in']);
        }
        return $accessToken;
    }

    /**
     * @return string|null
     * @throws \Throwable
     */
    public function getNewAccessToken(): ?string
    {
        $params = ['appKey' => $this->config->getAppId(), 'appSecret' => $this->config->getAppSecret()];
        $key = 'new-' . md5(json_encode($params));
        $accessToken = $this->config->getCache()->get($key);
        if (!$accessToken) {
            $body = $this->baseRequest($this->config->getUri1() . '/v1.0/oauth2/accessToken', 'POST', json_encode($params), [
                'Content-Type' => 'application/json'
            ]);
            $accessToken = $body['accessToken'];
            $this->config->getCache()->set($key, $accessToken, $body['expireIn']);
        }
        return $accessToken;
    }


}