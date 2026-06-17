<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
 * 离职员工花名册
 * Class EmployeeDriver
 * @package Cxb\DingTalk\Driver
 */
class EmployeeDriver extends AbstractDriver
{
    /**
     * @param string $path
     * @param string $method
     * @param null $body
     * @param array $headers
     * @return mixed
     */
    protected function request(string $path, string $method = 'GET', $body = null, array $headers = []): mixed
    {
        $body = parent::request($path, $method, $body, $headers);
        return $body['result'] ?? null;
    }

    /**
     * 离职员工列表
     * @param array $filter
     * @return mixed
     */
    public function  list(array $filter = []): mixed
    {
        $size = $filter['size'] ?? null;
        $this->validate($filter, [
            'size' => 'integer',
            'offset' => 'integer'
        ]);
        $filter['size'] = isset($filter['size']) && intval($filter['size']) < 50 ? $filter['size'] : 50;
        $list = [];
        do {
            $filter['offset'] = intval($filter['offset']??0);
            $filter['size'] = isset($filter['size']) && intval($filter['size']) < 50 ? $filter['size'] : 50;
            $result = $this->request('/topapi/smartwork/hrm/employee/querydimission?access_token=' . $this->getAccessToken(), 'POST', $filter);
            $list = array_merge($list, $result['data_list'] ?? []);
            if (!isset($result['next_cursor']))
                break;
            $filter['offset'] = $result['next_cursor'];
        } while ($size === null);
        $result['data_list'] = $list;
        return $result;
    }

    /**
     * 离职员工信息
     * @param array $userids
     * @return mixed
     */
    public function listdimission(array $userids): mixed
    {
        return $this->request('/topapi/smartwork/hrm/employee/listdimission?access_token=' . $this->getAccessToken(), 'POST', [
            'userid_list' => implode(',', $userids)
        ]);
    }
}