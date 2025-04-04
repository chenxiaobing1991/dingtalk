<?php


namespace Cxb\DingTalk;
use Cxb\DingTalk\Exception\BadQueryDingTalkExection;
use Cxb\DingTalk\Provider\AccessToken;
use Cxb\GuzzleHttp\ClientFactory;
use Cxb\GuzzleHttp\RequestClient;
use Cxb\GuzzleHttp\ResponseClient;
/**
 * 处理请求基类
 * Class AbstractProvider
 * @package Cxb\HyperfDingTalk
 */
abstract class AbstractProvider
{
  use AccessToken;
  public function __construct(protected Application $app,protected Config $config){

  }

    /**
     * 请求
     * @param string $method
     * @param $uri
     * @param array $options
     * @param array $header
     */
  public function request(string $method,$uri,array $options=[],array $header=[]):ResponseClient{
         if(strpos($uri,'/')===0)
             $uri=$this->config->getBaseUri().$uri;
         $request=new RequestClient($method,$uri,$options,$header);
         return $this->handleResponse(
             ClientFactory::send($request)
         );
  }

    /**
     * 请求处理--钉钉请求是json
     * @param ResponseClient $response
     */
  protected function handleResponse(ResponseClient $response):ResponseClient{
        if($response->statusCode!=200)
            throw new BadQueryDingTalkExection($response->error);
      $body =is_array($response->body)?$response->body:json_decode($response->body,true);
      if ($body['errcode'] != 0)
          return new ResponseClient($body['errcode'], null, [], null, $body['errmsg']);
      return new ResponseClient($response->statusCode, $response->duration,$response->headers,$body);
  }
}