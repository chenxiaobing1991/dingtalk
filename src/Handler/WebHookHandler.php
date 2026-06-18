<?php


namespace Cxb\DingTalk\Handler;

use Cxb\DingTalk\Exception\BusinessException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * 回调数据处理
 * Class WebHookHandler
 * @package Cxb\DingTalk\Handler
 */
class WebHookHandler extends AbstractHandler
{
    public $corpId;

//    /**
//     * @return string[]|null
//     */
//    public function getParsedBody(): ?array
//    {
//        return [
//            'signature' => '9405f2cf7d4349073f4e2ae336b59bea8d8f67f9',
//            'timestamp' => '1781575140619',
//            'nonce' => '0eYRsCyZ',
//            'encrypt' => 'HnakwwSaWWxxUr3XJzxt7ND4NgWfZVVvT0gNW0MPkyZ/D/yMm3rL7xc3cmFCwqfKOP85mDnL6+AO30fLDyB1bOiTMgrE//qcTW1oLXG/MMO6N36kJ4s0IJ5MkHuFP5B5'
//        ];
//    }

    /**
     * 验证签名
     * @return bool
     */
    public function verifySignature(): bool
    {
        $timestamp = $this->get('timestamp', '');//请求时间戳
        $nonce = $this->get('nonce', '');//随机字符串
        $encrypt = $this->get('encrypt', '');//加密消息体
        return $this->get('signature') === $this->signature($timestamp, $nonce, $encrypt);
    }

    /**
     * 获取处理过的加密aes_key
     * @return string
     */
    private function getSesKey(): string
    {
        $aesKey = $this->manager->getConfig()->get('aes_key');//事件与回调的数据加密密钥
        $trimmedKey = trim($aesKey); // 去除可能的空白字符
        $paddedKey = $trimmedKey;
        $padLen = strlen($paddedKey) % 4;
        if ($padLen > 0) {
            $paddedKey .= str_repeat('=', 4 - $padLen);
        }
        return base64_decode($paddedKey, true); // strict模式检查
    }

    /**
     * @param $decrypt
     * @return string
     */
    public function encrypt($decrypt): string
    {
        // 1. 构造明文
        $random = $this->getRandomStr(16);               // 16字节随机字符串
        $msgLen = pack('N', strlen($decrypt));         // 4字节消息长度（大端序）
        $content = $random . $msgLen . $decrypt . $this->corpId;

        // 2. PKCS7填充（AES块大小固定16字节，与密钥长度无关）
        $blockSize = 16;
        $padLength = $blockSize - (strlen($content) % $blockSize);
        if ($padLength == 0) {
            $padLength = $blockSize;
        }
        $content .= str_repeat(chr($padLength), $padLength);

        // 3. AES-256-CBC加密
        $iv = substr($this->getSesKey(), 0, 16);
        $encrypted = openssl_encrypt($content, 'AES-256-CBC', $this->getSesKey(), OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false)
            throw new BusinessException('DingTalk: AES加密失败');
        return base64_encode($encrypted);
    }


    /**
     * 生成签名
     * @param $timestamp
     * @param $nonce
     * @param $encrypt
     * @return string
     */
    public function signature($timestamp, $nonce, $encrypt): string
    {
        $token = (string)$this->manager->getConfig()->get('token');//回调自定义token
        $array = [$token, $timestamp, $nonce, $encrypt];
        sort($array, SORT_STRING);
        return sha1(implode('', $array));
    }

    /**
     * 解密已加密的消息体
     * @param string $encrypt
     * @return string
     */
    protected function decrypt(string $encrypt): string
    {
        if (empty($encrypt))
            throw new BusinessException('加密消息字符串为空');
        // 1. Base64解码
        $ciphertext = base64_decode($encrypt);
        if ($ciphertext === false)
            throw new BusinessException('DingTalk: Base64解码失败');
        $aesKey = $this->getSesKey();//事件与回调的数据加密密钥
        $iv = substr($aesKey, 0, 16); // IV = AES Key的前16字节
        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $aesKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        if ($decrypted === false) {
            throw new BusinessException('DingTalk: AES解密失败');
        }
        // 3. 去除PKCS7填充
        $padLength = ord(substr($decrypted, -1));
        if ($padLength < 1 || $padLength > 32) {
            error_log("DingTalk: 填充长度异常: {$padLength}");
            return false;
        }
        $decrypted = substr($decrypted, 0, -$padLength);

        // 4. 解析结构: random(16) + msg_len(4) + msg + corpId
        // 消息长度（大端序）
        $msgLen = unpack('N', substr($decrypted, 16, 4))[1];
        $msg = substr($decrypted, 20, $msgLen);
        $this->corpId = substr($decrypted, 20 + $msgLen);//解析处理回调
//        $c_corpId = $this->manager->getConfig()->get('corpId');
//        // 5. 验证corpId
//        if ($corpId !== $c_corpId) {
//            error_log("DingTalk: corpId不匹配 - expected: {$c_corpId}, got: {$corpId}");
//            return false;
//        }
        return $msg;
    }

    /**
     * 主体内容解析
     * @return mixed
     */
    public function process(): mixed
    {
        $encrypt = $this->get('encrypt', '');
        $msg = $this->decrypt($encrypt);
        return json_decode($msg, true);
    }

    /**
     * 成功返回
     * @param mixed|null $data
     * @param string $msg
     * @return mixed
     */
    public function success(mixed $data = null, string $msg = 'success'): mixed
    {
        $timestamp = strval(intval(microtime(true) * 1000));
        $nonce = bin2hex(random_bytes(8));
        $result = json_encode(['msg' => 'success', 'data' => $data], JSON_UNESCAPED_UNICODE);
        $encrypt = $this->encrypt($result);
        $signature=$this->signature($timestamp, $nonce, $encrypt);
        $response = $this->getContainer()->get(ResponseInterface::class);
        return $response->json([
            'encrypt' =>$encrypt,
            'signature' =>$signature,
            'msg_signature' =>$signature,
            'timeStamp' => $timestamp,
            'nonce' => $nonce,
        ]);
    }

    /**
     * 失败返回
     * @param \Throwable $throwable
     * @return mixed
     */
    public function error(\Throwable $throwable): mixed
    {
        return 'fail';
    }
}