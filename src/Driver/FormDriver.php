<?php


namespace Cxb\DingTalk\Driver;


use Cxb\DingTalk\AbstractDriver;
use Cxb\DingTalk\Constants\FormConstant;
use Hyperf\Validation\Rule;

/**
 * 审批报单管理
 * https://oapi.dingtalk.com/topapi/process
 * Class FormDriver
 * @package Cxb\DingTalk\Driver
 */
class FormDriver extends AbstractDriver
{
    /**
     * @param array $params
     * @return mixed
     */
    public function create(array $params = [])
    {
        return $this->editor($params);
    }

    /**
     * @param array $filter
     * @return mixed
     */
    private function editor(array $params): mixed
    {
        $filter['agentid'] = (int)$this->config->get('agent_id');
        $this->validate($filter,
            [
                'name' => 'required|string',
                'description' => 'required|string',
                'form_component_list.*.component_name' => Rule::in(FormConstant::FORM_COMPONENT_TYPE_LIST),
                'form_component_list.*.props.id' => 'required|string|max:22',
                'form_component_list.*.props.label' => 'required|string'
            ], [
                'form_component_list.*.component_name.in' => '表单名称仅支持[' . implode(',', FormConstant::FORM_COMPONENT_TYPE_LIST) . ']',
                'form_component_list.*props.id.required' => '表单ID必填',
                'form_component_list.*props.id.string' => '表单ID必须是字符串',
                'form_component_list.*props.id.max' => '表单ID不能超过22个字符',
                'form_component_list.*props.label.required' => '表单名称必填',
                'form_component_list.*props.label.string' => '表单名称必须是字符串',
            ]);
        return $this->request('/topapi/process/save', 'POST', ['saveProcessRequest'=>$filter]);
    }

    /**
     * @param string $process_code
     * @param array $params
     */
    public function update(string $process_code, array $params = [])
    {
        $params['process_code'] = $process_code;
        return $this->editor($params);
    }
}