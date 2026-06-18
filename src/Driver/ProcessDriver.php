<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;
use Hyperf\Validation\Rule;

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
     * 发起审批
     * @param array $filter
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Throwable
     */
    public function create(array $filter = []): mixed
    {
        $this->validate($filter, [
            'process_code' => 'string|required',
            'originator_user_id' => 'required',
            'dept_id' => 'required',
            'approvers' => 'string|required',
            'form_component_values.*name' => 'required',
            'form_component_values.*value' => 'required'
        ], [
            'process_code.required' => '模板编码必填',
            'originator_user_id.required' => '发起人必填',
            'dept_id.required' => '发起人所在部门必填',
            'approvers' => '审核人必填,多个审核人以逗号隔开',
            'form_component_values.*name' => '表单明细名称必填',
            'form_component_values.*value' => '表单明细数值必填'
        ]);
        $filter['agent_id'] = $this->config->get('agent_id');
        $result = $this->request('/create?access_token=' . $this->getAccessToken(), 'POST', $filter);
        return $result['process_instance_id'] ?? $result;
    }

    /**
     * 审批单详情
     * @param string $process_id
     * @return mixed
     */
    public function info(string $process_id): mixed
    {
        $info = parent::request('/topapi/processinstance/get?access_token=' . $this->getAccessToken(), 'POST', [
            'process_instance_id' => $process_id
        ]);
        return $info['process_instance'] ?? $info;
    }


    /**
     * 获取审批实例ID列表
     * @param array $filter
     * @return array
     * @throws \Throwable
     */
    public function list(array $filter = []): array
    {
        $filter['start_time'] = isset($filter['start_time']) && !empty($filter['start_time']) ? $filter['start_time'] : strtotime('-10 days') . '000';
        $this->validate($filter, [
            'process_code' => 'required',
            'start_time' => 'integer|required|digits:13',
            'end_time' => 'integer|digits:13'
        ], [
            'process_code.required' => '实例编码必填',
            'start_time.required' => '实例时间区间开始时间必填',
            'start_time.integer' => '实例时间区间开始时间格式必须为数字',
            'start_time.digits' => '实例时间区间开始时间格式必须为毫秒时间戳',
            'end_time.digits' => '实例时间区间结束时间格式必须为毫秒时间戳',
            'end_time.integer' => '实例时间区间结束时间格式必须为数字'
        ]);
        $size = $filter['size'] ?? null;
        $filter['size'] = $size === null ? 20 : $size;
        $list = [];
        do {
            $filter['cursor'] = (int)($filter['cursor'] ?? 0);
            $result = $this->request('/topapi/processinstance/listids?access_token=' . $this->getAccessToken(), 'POST', $filter);
            $data = $result['result'] ?? [];
            $list = array_merge($list, $data['list']);
            if (!isset($data['next_cursor']) || !$data['next_cursor'])
                break;
            $filter['cursor'] = (int)$data['next_cursor'];
        } while ($size === null);
        $data['list'] = $list;
        return $data;
    }

    /**
     * 审批单审核
     * @param array $filter
     * @return mixed
     */
    protected function execute(array $filter): mixed
    {
        $this->validate($filter, [
            'process_instance_id' => 'required',
            'actioner_userid' => 'required',
            'task_id' => 'required',
            'result.in' => Rule::in('agree', 'refuse')
        ], [
            'process_instance_id.required' => '审批实例ID必填',
            'actioner_userid.required' => '操作人必填',
            'task_id.required' => '任务节点id必填',
            'result.in' => '不支持的审批操作'
        ]);
        $result = $this->request('/execute?access_token=' . $this->getAccessToken(), 'POST', ['request' => $filter]);
        return (bool)$result['result'];
    }

    /**
     * 审核同意
     * @param array $filter
     * @return bool
     */
    public function agree(array $filter): bool
    {
        $filter['result'] = 'agree';
        $filter['remark'] = $filter['remark'] ?? '同意';
        return $this->execute($filter);
    }

    /**
     * 审核拒绝
     * @param array $filter
     * @return bool
     */
    public function refuse(array $filter): bool
    {
        $filter['result'] = 'refuse';
        $filter['remark'] = $filter['remark'] ?? '不同意';
        return $this->execute($filter);
    }

    /**
     * 获取附件详情
     * @param $process_id
     * @param $file_id
     * @return mixed
     */
    public function file($process_id, $file_id): mixed
    {
        $info = $this->request('/topapi/processinstance/file/url/get?access_token=' . $this->getAccessToken(), 'POST',
            json_encode(['request' => ['process_instance_id' => $process_id, 'file_id' => $file_id]], true),
            ['Content-Type' => 'application/json']
        );
        return $info['result'] ?? null;
    }


}