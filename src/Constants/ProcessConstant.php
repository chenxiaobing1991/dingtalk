<?php


namespace Cxb\DingTalk\Constants;

/**
 * 审批常量管理器
 * Class ProcessConstant
 * @package Cxb\DingTalk\Constants
 */
class ProcessConstant
{
    /* 事件类型  */
    public const EVENT_TYPR_INSTANCE_CHANGE = 'bpms_instance_change';//审核实例变更

    public const EVENT_TYPR_TASK_CHANGE = 'bpms_task_change';//审核任务变更

    public const EVENT_TYPR_CHECKOUT_URL = 'check_url';//URL验证事件

    public const EVENT_TYPR_USER_ADD_ORG = 'user_add_org';//用户入职

    public const EVENT_TYPR_USER_MODIFY_ORG = 'user_modify_org';//用户信息变更

    public const EVENT_TYPR_USER_LEAVE_ORG = 'user_leave_org';//用户离职

    public const EVENT_TYPR_DEPT_CREATE_ORG = 'org_dept_create';//部门创建

    public const EVENT_TYPR_DEPT_MODIFY_ORG = 'org_dept_modify';//部门修改

    public const EVENT_TYPR_DEPT_REMOVE_ORG = 'org_dept_remove';//部门删除

    /*    实例状态     */
    public const STATUS_SUCCESS = 'COMPLETED';//完成

    public const STATUS_CLOSE = 'TERMINATED';//终止
    /*     行为                */
    public const RESULT_AGREE = 'agree';//同意

    public const RESULT_REHECT = 'reject';//驳回


    /*    表单主键类型----常用       */

    public const FORM_COMPONENT_TYPE_ATTACHMENT = 'DDAttachment';//附件

    public const FORM_COMPONENT_TYPE_TABLE = 'TableField';//表格


    public const FORM_COMPONENT_TYPE_LIST = [
        self::FORM_COMPONENT_TYPE_ATTACHMENT => '附件',
        self::FORM_COMPONENT_TYPE_TABLE => '表格',
        'DDSelectField' => '下拉选择器',
        'DDDateField' => '时间控件',
        'TextareaField' => '输入框'
    ];
}