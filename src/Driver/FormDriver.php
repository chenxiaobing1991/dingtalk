<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;

/**
 * 审批报单管理
 * https://oapi.dingtalk.com/topapi/process
 * Class FormDriver
 * @package Cxb\DingTalk\Driver
 */
class FormDriver extends AbstractDriver
{
    /**
     * @param string $name
     * @param array $props
     * @param string $remark
     * @param array $filter
     */
    public function create(string $name, array $props, string $remark, array $filter = [])
    {

        $filter['agentid'] = (int)$this->config->get('agent_id');
        $filter['name'] = $name;
        $filter['description'] = $remark;
        $filter['props'] = $props;


    }
}