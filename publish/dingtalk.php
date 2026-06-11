<?php

declare(strict_types=1);

use function Hyperf\Support\env;
use Cxb\DingTalk\Contract\CacheInterface;
use Cxb\DingTalk\Cache\FileCache;

return [
    'default'=>[
        'driver' => env('DINGTALK_DEFAULT_DRIVER', 'default'),
    ],
    'drivers' => [
        'default' => [
            'app_id' => env('DINGTALK_APP_ID', ''),//应用ID
            'app_secret' => env('DINGTALK_APP_SECRET', ''),
            'agent_id' => env('DINGTALK_AGENT_ID', ''),
            'cache' => function (): CacheInterface {
                return new FileCache();
            }
        ]
    ],
];