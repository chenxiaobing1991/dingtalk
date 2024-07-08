<?php

return [
    "app_id"=>"",//应用ID
    "app_secret"=>"",//应用密钥
    "agent_id"=>"",
    "access_token"=>'',
    "cache"=>function(){
        return make(\Cxb\DingTalk\Cache\FileCache::class);
    }
];