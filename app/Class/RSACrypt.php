<?php

/**
 * Class RSA
 */
class RSACrypt
{

    private $publicKey;

    private $privateKey;

    private $publicKeyPath;

    private $privateKeyPath;

    /**
     * RSACrypt constructor.
     * @param string $keyPath
     */
    public function __construct($keyPath = null)
    {
        if (empty($keyPath))
        {
            return;
        }

        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 1024,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $resource = openssl_pkey_new($config);

        // Extract the private key from $res to $priKey
        openssl_pkey_export($resource, $this->privateKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($resource);

        $this->publicKey = $pubKey['key'];

        $this->publicKeyPath = $keyPath.'rsa_public_key.pem';

        $this->privateKeyPath = $keyPath.'rsa_private_key.pem';

        @file_put_contents($this->publicKeyPath, $this->publicKey);

        @file_put_contents($this->privateKeyPath, $this->privateKey);

    }

    /**
     * 设置公钥
     * @param $pubKey
     */
    public function setPublicKey($pubKey)
    {
        $this->publicKey = $pubKey;
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param $priKey
     */
    public function setPrivateKey($priKey)
    {
        $this->privateKey = $priKey;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param $pubKeyPath
     */
    public function setPublicKeyPath($pubKeyPath)
    {
        $this->publicKeyPath = $pubKeyPath;
    }

    /**
     * @return string
     */
    public function getPublicKeyPath()
    {
        return $this->publicKeyPath;
    }

    /**
     * @param $priKeyPath
     */
    public function setPrivateKeyPath($priKeyPath)
    {
        $this->privateKeyPath = $priKeyPath;
    }

    /**
     * @return string
     */
    public function getPrivateKeyPath()
    {
        return $this->privateKeyPath;
    }

    /**
     * 公钥加密
     * @param $data
     * @return mixed
     */
    public function publicEncrypt($data)
    {
        $publicKey = $this->checkPublicKey();

        openssl_public_encrypt($data, $encrypted, $publicKey);

        openssl_free_key($publicKey);

        return $encrypted;
    }

    /**
     * 公钥解密
     * @param $data
     * @return mixed
     */
    public function publicDecrypt($data)
    {
        $publicKey = $this->checkPublicKey();

        openssl_public_decrypt($data, $decrypted, $publicKey);

        openssl_free_key($publicKey);

        return $decrypted;
    }

    /**
     * 私钥加密
     * @param $data
     * @return mixed
     */
    public function privateEncrypt($data)
    {
        $privateKey = $this->checkPrivateKey();

        openssl_private_encrypt($data, $encrypted, $privateKey);

        openssl_free_key($privateKey);

        return $encrypted;
    }

    /**
     * 私钥解密
     * @param $data
     * @return mixed
     */
    public function privateDecrypt($data)
    {
        $privateKey = $this->checkPrivateKey();

        openssl_private_decrypt($data, $decrypted, $privateKey);

        openssl_free_key($privateKey);

        return $decrypted;
    }

    /**
     * 读取公钥
     * @return resource|string
     */
    private function checkPublicKey()
    {
        if($this->publicKeyPath){

            //读取公钥文件
            $pubKey = @file_get_contents($this->publicKeyPath);

            $pubKey or exit('公钥不存在!');

            //转换为openssl格式密钥
            $res = openssl_pkey_get_public($pubKey);

        } elseif ($this->publicKey) {

            //初始化公钥
            $pubKey = str_replace("-----BEGIN PUBLIC KEY-----", "", $this->publicKey);
            $pubKey = str_replace("-----END PUBLIC KEY-----", "", $pubKey);
            $pubKey = str_replace("\n", "", $pubKey);

            $pubKey = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($pubKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";

            //转换为openssl格式密钥
            $res = openssl_pkey_get_public($pubKey);

        } else {

            exit('公钥不存在');

        }

        return $res;
    }

    /**
     * 读取私钥
     * @return resource|string
     */
    private function checkPrivateKey()
    {
        if($this->privateKeyPath){

            //读取私钥文件
            $priKey = @file_get_contents($this->privateKeyPath);

            $priKey or exit('私钥不存在!');

            //转换为openssl格式密钥
            $res = openssl_get_privatekey($priKey);

        } elseif ($this->privateKey) {

            //初始化私钥
            $priKey = str_replace("-----BEGIN PRIVATE KEY-----", "", $this->privateKey);
            $priKey = str_replace("-----END PRIVATE KEY-----", "", $priKey);
            $priKey = str_replace("\n", "", $priKey);

            $priKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";

            //转换为openssl格式密钥
            $res = openssl_get_privatekey($priKey);

        } else {

            exit('私钥不存在!');

        }

        return $res;
    }

}
