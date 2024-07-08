<?php


namespace Cxb\DingTalk\Provider;

use Cxb\DingTalk\Exception\BadQueryDingTalkExection;
use Cxb\GuzzleHttp\ResponseClient;

/**
 * 获取accessToken
 * Trait AccessToken
 * @package Cxb\DingTalk\Provider
 * @property $config
 * @method  handleResponse($response)
 * @method request(string $method,$uri,array $options=[]):ResponseClient
 */
trait AccessToken
{
    private ?string $accessToken = null;

    private int $expireTime = 0;

    /**
     *缓存命名空间
     */
    private function  getAccessTokenNameSpace():string {
          return join(':',[
              'token',
              static::class,
              $this->config->getAppId(),
          ]);
    }

    /**
     * 加载缓存
     */
    private function loadCache(){
         $this->accessToken=$this->config->getCache()->get($this->getAccessTokenNameSpace());//获取缓存
    }
    /**
     * 获取令牌
     * @return string
     */
    public function getAccessToken(){
           $this->loadCache();//加载缓存
           if(!$this->isExpired())
               return $this->accessToken;//没有过期,直接获取
        $params=['appkey'=>$this->config->getAppId(),'appsecret'=>$this->config->getAppSecret()];
        $response =$this->request('get','/gettoken?'.http_build_query($params));
        if($response->statusCode!=200)
            throw new BadQueryDingTalkExection('令牌获取失败');
        $this->accessToken = $response->body['access_token'];
        $this->expireTime = $response->body['expires_in'] + time();
        $this->config->getCache()->set($this->getAccessTokenNameSpace(),$this->accessToken,$response->body['expires_in']);
        return $this->accessToken;
    }

    /**
     * 是否已过期
     * @return bool
     */
    private function isExpired():bool{
        if (!empty($this->accessToken))
            return false;
        return true;
    }
}