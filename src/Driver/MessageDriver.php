<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;
use Cxb\DingTalk\Constants\MessageConstant;
use Hyperf\Coroutine\Parallel;

/**
 * 工作通知
 * Class MessageDriver
 * @package Cxb\DingTalk\Driver
 */
class MessageDriver extends AbstractDriver
{
    /**
     * @param string $userid
     * @param string $content
     * @param string $type
     * @param array $filter
     * @return mixed
     */
    public function send(string $userid, string $content, string $type = MessageConstant::MSG_TYPE_TEXT, array $filter = [])
    {
        $filter = array_merge([
            'to_all_user' => 'false',
            'agent_id' => $this->config->get('agent_id'),
            'userid_list' => $userid,
            'msg' => json_encode(['msgtype' => $type, 'text' => ['content' => $content]], true)
        ], $filter);
        return $this->request('/topapi/message/corpconversation/asyncsend_v2?access_token=' . $this->getAccessToken(), 'POST',$filter);
    }

    /**
     * @param string $userid
     * @param string $content
     * @param string $type
     * @param array $filter
     * @return mixed
     */
    public function sendByMobile(mixed $mobile, string $content, string $type = 'text', array $filter = [])
    {
        $list = !is_array($mobile) ? explode(',', $mobile) : $mobile;
        $parallel = new Parallel(5);
        foreach ($list as $mobile) {
            $parallel->add(function () use ($mobile) {
                $info = $this->driver->get(UserDriver::class)->infoByMobile($mobile);
                return $info['userid'] ?? '';
            });
        }
        $userids = $parallel->wait();
        return $this->send(implode(',', $userids), $content, $type, $filter);
    }

    /**
     * 撤销/移除工作通知
     * @param mixed $msg_id
     * @return mixed
     */
    public function remove(mixed $msg_id): mixed
    {
        return $this->request('/topapi/message/corpconversation/recall?access_token=' . $this->getAccessToken(),'POST', [
            'agent_id' => $this->config->get('agent_id'),
            'msg_task_id' => $msg_id
        ]);
    }
}