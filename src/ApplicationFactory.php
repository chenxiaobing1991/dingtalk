<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Cxb\DingTalk;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class ApplicationFactory
{
    private $app;
    public function __construct(protected ContainerInterface $container){
        $config = $container->get(ConfigInterface::class)->get('dingtalk', []);
        $this->app=new Application(new Config([
            'app_id' => $config['app_id'] ?? null,
            'app_secret' => $config['app_secret'] ?? null,
            'agent_id' => $config['agent_id'] ?? null,
            'access_token' => $config['access_token'] ?? null,
            'cache' => $config['cache'] ?? null,
        ]));
    }
    /**
     * 获取具体的执行引擎
     * @param $name
     * @return mixed
     */
    public function __get($name){
        return $this->app->{$name};
    }
}
