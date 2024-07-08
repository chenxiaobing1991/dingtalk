<?php


namespace Cxb\DingTalk;
use Cxb\DingTalk\Exception\BadQueryDingTalkExection;
use Cxb\DingTalk\Provider\AsyncProvider;
use Cxb\DingTalk\Provider\DepartmentProvider;
use Cxb\DingTalk\Provider\UserProvider;

/**
 * 实例化
 * Class Application
 * @package Cxb\DingTalk
 */
class Application
{
    protected array $alias = [
        'user'=>UserProvider::class,
        'dept'=>DepartmentProvider::class,
        'async'=>AsyncProvider::class
    ];
    protected array $providers = [];

    /**
     * 实例化配置
     * Application constructor.
     * @param Config $model
     */
    public function __construct(protected Config $config)
    {

    }

    /**
     * 获取具体的执行引擎
     * @param $name
     * @return mixed
     * @throws BadQueryDingTalkExection
     */
    public function __get($name){
        if (! isset($name) || ! isset($this->alias[$name])) {
            throw new BadQueryDingTalkExection("{$name} is invalid.");
        }
        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }
        $class = $this->alias[$name];
        return $this->providers[$name] = new $class($this, $this->config);
    }
}