<?php


namespace Cxb\DingTalk\Handler;

/**
 * OA审批回调处理器
 * Class ProcessHandler
 * @package Cxb\DingTalk\Handler
 */
class ProcessHandler extends AbstractHandler
{
    /**
     * 生成签名
     * @return string
     */
    public function generateSignature(): string
    {
        $token = (string)$this->manager->getConfig()->get('token');
        $timestamp = $this->params['timestamp'] ?? '';
        $nonce = $this->params['nonce'] ?? '';
        $info = [$token, $timestamp, $nonce];
        sort($info, SORT_STRING);
        return sha1(implode($info));
    }

    /**
     * 签名验证
     * @return bool
     */
    public function verifySignature(): bool
    {
        return $this->generateSignature() === ($this->params['signature'] ?? '');
    }

    /**
     * 过程处理器
     * @return mixed
     */
    protected function process(): mixed
    {
        $EncodingAESKey = $this->manager->getConfig()->get('EncodingAESKey');//消息加解密
        $key = base64_decode($EncodingAESKey . "=");
        $iv = substr($key, 0, 16);
        $decrypted = openssl_decrypt($this->params['echostr'], 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        // 去除 PKCS#7 填充
        $pad = ord(substr($decrypted, -1));
        $decrypted = substr($decrypted, 0, -$pad);

        // 提取消息内容：random(16字节) + msg_len(4字节) + msg + corpid
        $contentLen = unpack('N', substr($decrypted, 16, 4))[1];
        $msg = substr($decrypted, 20, $contentLen);
        $fromCorpid = substr($decrypted, 20 + $contentLen);
        return json_decode($msg, true);
    }
}