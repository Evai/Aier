<?php

/**
 * Class AESCrypt
 */
class AESCrypt
{
    private $key;
    private $iv;
    private $method = 'aes-128-cbc';

    /**
     * AESCrypt constructor.
     * @param $key
     * @param $iv
     */
    function __construct($key, $iv)
    {

        if (empty($key) || empty($iv) || !$this->checkKey($key)) {

            return null;

        }

        $this->key = $key;

        $this->iv = $iv;

    }

    /**
     * 加密
     * @param $data
     * @return string
     */
    public function encrypt($data)
    {

        $encrypted = openssl_encrypt($data, $this->method, $this->key, true, $this->iv);

        return base64_encode($encrypted);

    }

    /**
     * 解密
     * @param $data
     * @return string
     */
    public function decrypt($data)
    {

        $decrypted = openssl_decrypt(base64_decode($data), $this->method, $this->key, true, $this->iv);

        return $decrypted;

    }

    /**
     * 验证是否是字母和数字的组合
     * @param $key
     * @return bool
     */
    private function checkKey($key)
    {

        if (!preg_match("/^(?![^a-zA-Z]+$)(?!\d+$).{32}$/", $key)) {

            return false;

        }

        return true;
    }

}
