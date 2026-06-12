<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
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
            'text' => 'require|string',
            'comment_userid' => 'require'
        ], [
            'comment_userid.require' => '评论人userid必填'
        ]);
        $params['process_instance_id'] = $process_id;
        return $this->request('/topapi/process/instance/comment/add', 'POST', $params);
    }
}