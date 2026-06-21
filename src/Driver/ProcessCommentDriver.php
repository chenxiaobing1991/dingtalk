<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
 * 审批评论
 * Class ProcessCommentDriver
 * @package Cxb\DingTalk\Driver
 */
class ProcessCommentDriver extends AbstractDriver
{
    /**
     * @param string $process_id
     * @param array $params
     * @return mixed
     */
    public function create(string $process_id, array $params): mixed
    {
        $this->validate($params, [
            'text' => 'required|string',
            'commentUserId' => 'required|string'
        ], [
            'comment_userid.required' => '评论人userid必填'
        ]);
        $params['processInstanceId'] = $process_id;
        return $this->request('/topapi/process/instance/comment/add?access_token='.$this->getAccessToken(), 'POST',
            json_encode(['request'=>$params]),
            ['Content-Type' => 'application/json']
        );
    }
}