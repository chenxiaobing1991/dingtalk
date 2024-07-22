<?php


namespace Cxb\DingTalk\Provider;


use Cxb\DingTalk\AbstractProvider;
use Cxb\GuzzleHttp\ResponseClient;

/**
 * 消息发送引擎
 * Class UserProvider
 * @package Cxb\HyperfDingTalk\Async
 */
class AsyncProvider extends  AbstractProvider
{
    /**
     * 发送消息
     * @param $id
     * @param array $msg
     * @return ResponseClient
     */
    public function send($user_id,string $content,string $type='text') :ResponseClient {
        $params = [
            'to_all_user' => 'false',
            'agent_id' =>$this->config->getAgentId(),
            'userid_list' =>$user_id,
            'msg' => json_encode(['msgtype' =>$type, 'text' => ['content' =>$content]], true)
        ];
        $response=$this->request('post', '/topapi/message/corpconversation/asyncsend_v2?access_token='.$this->getAccessToken(),$params);
        return $response;
    }

    /**
     * 通过手机号发送消息
     * @param $mobile
     * @param string $content
     * @param string $type
     */
    public function sendByMobile($mobile,string $content,string $type='text'):ResponseClient{
        $user=make(UserProvider::class,['app'=>$this->app,'config'=>$this->config]);
        $res=$user->getIdByMobile(intval($mobile));
        if($res->statusCode!=200)
            return $res;
        $userid=$res->body['userid']??'';
        return $this->send($userid,$content,$type);
    }
}