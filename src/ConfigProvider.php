<?php


namespace Cxb\DingTalk;

/**
 * 执行类映射绑定
 * Class ConfigProvider
 * @package Cxb\DingTalk
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Application::class => ApplicationFactory::class,
            ]
        ];
    }
}