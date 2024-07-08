<?php


namespace Cxb\DingTalk\Cache;


class FileCache  implements CacheInterface
{
    private $path=__DIR__.'/cache';//文件存储路径

    /**
     * FileCache constructor.
     * @param string|null $path
     */
    public function __construct(string $path=null){
        $this->path=$path===null?$this->path:$path;
        if(!is_dir($this->path))
            mkdir($this->path,'0777');
    }
    /**
     * 获取配置
     * @param string $name
     */
    public function get(string $name)
    {
        $data=@file_get_contents($this->path.'/'.md5($name));
         if(!$data)
             return null;
         $data=json_decode($data,true);
         return $data['expire']>time()?($data['access_token']):null;
    }

    /**
     * 获取配置
     * @param string $name
     * @param $data
     */
    public function set(string $name, $data,$ttl)
    {
        $result=[
            'expire'=>$ttl+time(),
            'access_token'=>$data
        ];
        file_put_contents($this->path.'/'.md5($name),json_encode($result));
    }
}