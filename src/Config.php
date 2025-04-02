<?php


namespace Cxb\DingTalk;

use Cxb\DingTalk\Cache\CacheInterface;
use Cxb\DingTalk\Cache\FileCache;

/**
 *配置类
 * Class Config
 * @package Cxb\HyperfDingTalk
 */
final class Config
{
   private $base_uri='https://oapi.dingtalk.com';
   protected ?string $app_id;
   protected ?string $app_secret;
   protected ?string $agent_id;
   protected $access_token;
   protected int $ttl=0;
   protected ?CacheInterface $cache;
   public function __construct(array $config=[]){
       $this->app_id=isset($config['app_id'])?(string) $config['app_id']:'';
       $this->app_secret=isset($config['app_secret'])?(string) $config['app_secret']:'';
       $this->agent_id=isset($config['agent_id'])?(string) $config['agent_id']:'';
       $this->ttl=isset($config['ttl'])?(int) $config['ttl']:60 * 60;
       $this->base_uri=isset($config['base_uri'])?(string) $config['base_uri']:$this->base_uri;
       $this->cache=isset($config['cache'])&&$config['cache'] instanceof CacheInterface?(string) $config['cache']:(new FileCache());
   }

    /**
     * @return string|null
     */
   public function getAppId():?string {
       return $this->app_id;
   }

    /**
     * @return string|null
     */
   public function getAppSecret():?string {
       return $this->app_secret;
   }

    /**
     * @return string
     */
   public function getBaseUri():string{
       return $this->base_uri;
   }

    /**
     * 令牌
     * @return string
     */
   public function getAccessToken(){
       return $this->access_token;
   }

    /**
     * @return string
     */
   public function getAgentId():string {
       return $this->agent_id;
   }

    /**
     * 获取缓存
     * @return CacheInterface
     */
   public function getCache():CacheInterface{
      return new FileCache();
      return $this->cache;
   }
}