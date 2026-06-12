<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
 * https://open.dingtalk.com/document/development
 * OA审批
 * Class ProcessDriver
 * @package Cxb\DingTalk\Driver
 */
class ProcessDriver extends AbstractDriver
{

    public const PROCESS_BASE_PATH = '/topapi/processinstance';

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
        $result = $this->request(self::PROCESS_BASE_PATH . '/listids?access_token?' . $this->getAccessToken(), 'POST', $filter);
        $list = $result['list'] ?? [];
        if ($size === null && ($result['has_more'] ?? false)) {//代表获取全部并且是有下一页
            $filter['cursor'] = $result['cursor'];//赋值新游标
            $result = $this->list($process_code, $filter);
            $result['list'] = array_merge($list, $result['list']);
        }
        return $result;
    }

    /**
     * 发起审批
     * @param string $process_code 审批模板
     * @param string $originator_user_id 发起人
     * @param string $originator_dept_id 发起人所在部门
     * @param string $approvers 审核人
     * @param array $filter 其他
     */
    public function create(string $process_code, string $originator_user_id, string $originator_dept_id, string $approvers, array $filter = [])
    {
        $filter['agent_id'] = $this->config->get('agent_id');
        $filter['process_code'] = $process_code;
        $filter['originator_user_id'] = $originator_user_id;
        $filter['dept_id'] = $originator_dept_id;
        $filter['approvers'] = $approvers;
        return $this->request(self::PROCESS_BASE_PATH . '/create?access_token=' . $this->getAccessToken(), 'POST', $filter);
    }

    /**
     * 审批单详情
     * @param string $process_id
     * @return mixed
     */
    public function info(string $process_id): mixed
    {
        return $this->request(self::PROCESS_BASE_PATH . '/get?access_token?' . $this->getAccessToken(), 'POST', ['process_instance_id' => $process_id]);
    }


}