<?php
//验证是否是字母和数字的组合
// if (!preg_match("/^(?![^a-zA-Z]+$)(?!\D+$).{32}$/",$str)) {
// 	return false;
// }
/**
* AESCrypt
*/
class AESCrypt
{
	private $key;
	private $iv;
	private $method = 'aes-128-cbc';
	
	function __construct($key, $iv)
	{
		if (empty($key) || empty($iv)) {
			return null;
		}
		$this->key = $key;
		$this->iv = $iv;
	}

	public function encrypt($data)
	{
		$encrypted = openssl_encrypt($data, $this->method, $this->key, true, $this->iv);
		return base64_encode($encrypted);
	}
	public function decrypt($data)
	{
		$decrypted = openssl_decrypt(base64_decode($data), $this->method, $this->key, true, $this->iv);
		return $decrypted;
	}
}
    
    

   
    $pass = md5('12345678');
    $iv = substr($pass,0,16);
    $method = 'aes-128-cbc';

    //echo $iv.'<br/>';
    //echo $pass.'<br>';

    $source = json_encode(['name'=>'老司机','mobile'=>123456789],JSON_UNESCAPED_UNICODE);

    $aes = new AESCrypt($pass,$iv);
    $encrypted = $aes->encrypt($source);
    var_dump($encrypted);
    $decrypted = $aes->decrypt($encrypted);
    var_dump($decrypted);


