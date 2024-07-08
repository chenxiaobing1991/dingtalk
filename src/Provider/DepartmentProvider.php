<?php


namespace Cxb\DingTalk\Provider;

use Cxb\DingTalk\AbstractProvider;
use Cxb\GuzzleHttp\ResponseClient;
use Cxb\GuzzleHttp\ClientFactory;
/**
 * 部门相关
 * Class UserProvider
 * @package Cxb\HyperfDingTalk\Department
 */
class DepartmentProvider extends AbstractProvider
{
    /**
     * 部门列表
     * @return ResponseClient
     */
    public function getList(){
        $response=$this->request('post', '/topapi/v2/department/listsub?access_token='.$this->getAccessToken());
        return new ResponseClient($response->statusCode,$response->duration,$response->headers,$response['result']??null,$response->error);
    }
}