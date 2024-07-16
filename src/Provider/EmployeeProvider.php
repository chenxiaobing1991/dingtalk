<?php


namespace Cxb\DingTalk\Provider;


use Cxb\DingTalk\AbstractProvider;
use Cxb\DingTalk\Exception\BadQueryDingTalkExection;
use Cxb\GuzzleHttp\ResponseClient;

/**
 * 离职数据
 * Class EmployeeProvider
 * @package Cxb\DingTalk\Provider
 */
class EmployeeProvider extends AbstractProvider
{
    private $uri='https://api.dingtalk.com';
    /**
     * 获取离职员工列表
     * @param int $limit
     * @param int $nextToken
     */
    public function getList(int $limit=-1,$nextToken=0):ResponseClient{
        $limit=$limit<=0||$limit>50?50:$limit;
        $filter=['nextToken'=>$nextToken,'maxResults'=>$limit];
        $uri=$this->uri.'/v1.0/hrm/employees/dismissions?'.http_build_query($filter);
        $response=$this->request('get', $uri,[],['x-acs-dingtalk-access-token'=>$this->getAccessToken()]);
        return new ResponseClient($response->statusCode,$response->duration,$response->headers,$response->body??[],$response->error);
    }
    /**
     * 请求处理--钉钉请求是json
     * @param ResponseClient $response
     */
    protected function handleResponse(ResponseClient $response):ResponseClient{
        if($response->statusCode!=200)
            throw new BadQueryDingTalkExection($response->error);
        $body =is_array($response->body)?$response->body:json_decode($response->body,true);
        return new ResponseClient(200, $response->duration,$response->headers,$body);
    }
}