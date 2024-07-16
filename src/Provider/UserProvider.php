<?php


namespace Cxb\DingTalk\Provider;


use Cxb\DingTalk\AbstractProvider;
use Cxb\DingTalk\Exception\BadQueryDingTalkExection;
use Cxb\GuzzleHttp\ResponseClient;

/**
 * 员工账号数据
 * Class UserProvider
 * @package Cxb\HyperfDingTalk\User
 */
class UserProvider extends AbstractProvider
{
    /**
     * 通过部门编号获取部门ID
     * @param int $dept_id
     * @param int $limit
     * @param int $next_cursor
     * @return ResponseClient
     */
    public function getAllByDeptId(int $dept_id):ResponseClient {
        $next_cursor=0;
        $list=[];
        while(true){
            $response=$this->getListByDeptId($dept_id,50,$next_cursor);
            if($response->statusCode!=200)
                return $response;
            $list=array_merge($list,$response->body['list']??[]);
            if (!$response->body['has_more'])
                break;
            $next_cursor = $response->body['next_cursor'];
        }
        return new ResponseClient($response->statusCode,$response->duration,$response->headers,$list,$response->error);
    }
    /**
     * 通过部门编号获取部门ID
     * @param int $dept_id
     * @param int $limit
     * @param int $next_cursor
     * @return ResponseClient
     */
    public function getListByDeptId(int $dept_id,$limit=10,$next_cursor=0):ResponseClient {
        $filter=['dept_id'=>$dept_id, 'cursor'=>$next_cursor, 'size'=>$limit];
        $response=$this->request('post', '/topapi/v2/user/list?access_token='.$this->getAccessToken(),$filter);
        return new ResponseClient($response->statusCode,$response->duration,$response->headers,$response->body['result']??null,$response->error);
    }

    /**
     * 通过手机号获取员工ID
     * @param int $mobile
     * @return ResponseClient
     */
    public function getIdByMobile(int $mobile){
        $response=$this->request('post', '/topapi/v2/user/getbymobile?access_token='.$this->getAccessToken(),['mobile' => $mobile]);
        return new ResponseClient($response->statusCode,$response->duration,$response->headers,$response->body['result']??null,$response->error);
    }
}