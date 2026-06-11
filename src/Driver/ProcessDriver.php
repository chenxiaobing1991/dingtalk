<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
 * OA审批
 * Class ProcessDriver
 * @package Cxb\DingTalk\Driver
 */
class ProcessDriver extends AbstractDriver
{
    /**
     * 获取审批列表
     * @param string $process_code 表单编号
     * @param array $filter
     */
    public function list(string $process_code, array $filter = [])
    {
        $size = $filter['size'] ?? null;
        $filter['process_code'] = $process_code;
        $filter['size'] = $size === null ? 20 : $size;
        $result = $this->request('/topapi/processinstance/listids?access_token?' . $this->getAccessToken(), 'POST', $filter);
        $list = $result['list'] ?? [];
        if ($size === null && ($result['has_more'] ?? false)) {//代表获取全部并且是有下一页
            $filter['cursor'] = $result['cursor'];//赋值新游标
            $result = $this->list($process_code, $filter);
            $result['list'] = array_merge($list, $result['list']);
        }
        return $result;
    }

    /**
     * 审批单详情
     * @param string $process_id
     * @return mixed
     */
    public function info(string $process_id): mixed
    {
        return $this->request('/topapi/processinstance/get?access_token?' . $this->getAccessToken(), 'POST', ['process_instance_id' => $process_id]);
    }

}