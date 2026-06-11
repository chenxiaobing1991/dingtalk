<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;
use Cxb\DingTalk\Exception\BusinessException;
use Cxb\GuzzleHttp\ResponseClient;

/**
 * Class UserDriver
 * @package Cxb\DingTalk\Driver
 */
class UserDriver extends AbstractDriver
{

    protected function handleResponse(ResponseClient $response)
    {
        $body = parent::handleResponse($response);
        return $body['result'] ?? null;
    }

    /**
     * @param string $userid
     * @param array $filter
     * @return mixed
     */
    public function info(string $userid, array $filter = []): mixed
    {
        return $this->request( '/topapi/v2/user/get?access_token=' . $this->getAccessToken(),'POST', array_merge($filter, ['userid' => $userid]));
    }

    /**
     * @param mixed $mobile
     * @param array $filter
     * @return mixed
     */
    public function infoByMobile(mixed $mobile, array $filter = []): mixed
    {
        return $this->request('/topapi/v2/user/getbymobile?access_token=' . $this->getAccessToken(),'POST', ['mobile' => $mobile]);
    }

    /**
     * @param array $filter
     * @return array
     */
    public function list(array $filter = []): array
    {
        if (!isset($filter['dept_id']))
            throw new BusinessException(sprintf('dept_id is required'));
        $limit = intval($filter['size'] ?? 50);
        $filter = ['dept_id' => $filter['dept_id'], 'cursor' => $filter['cursor'], 'size' => $limit];
        $result = $this->request('/topapi/v2/user/list?access_token=' . $this->getAccessToken(),'POST', $filter);
        $list = $result['list'] ?? [];
        if (($result['has_more'] ?? false)) {
            $filter = ['dept_id' => $filter['dept_id'], 'cursor' => $result['next_cursor'], 'size' => $limit];
            $list = array_merge($list, $this->list($filter));
        }
        return $list;
    }
}