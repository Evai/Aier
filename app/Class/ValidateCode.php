<?php

/**
 * Class ValidateCode
 */
class ValidateCode {
    /**
     * 随机因子
     * @var string
     */
    private $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    /**
     * 验证码
     * @var
     */
    private $code;
    /**
     * 验证码长度
     * @var int
     */
    public $codeLength = 4;
    /**
     * 宽度
     * @var int
     */
    public $width = 130;
    /**
     * 高度
     * @var int
     */
    public $height = 50;
    /**
     * 图形资源句柄
     * @var
     */
    private $img;
    /**
     * 指定的字体
     * @var string
     */
    private $font;
    /**
     * 指定字体大小
     * @var int
     */
    public $fontSize = 20;
    /**
     * 指定字体颜色
     * @var
     */
    private $fontColor;

    /**
     * ValidateCode constructor.
     * @param $font
     */
    public function __construct($font)
    {
        $font or die('必须填写字体路径');

        $this->font = $font;//注意字体路径要写对，否则显示不了图片

    }

    /**
     * 生成随机码
     */
    private function createCode()
    {

        $len = strlen($this->charset)-1;

        for ($i=0;$i<$this->codeLength;$i++)
        {

            $this->code .= $this->charset[mt_rand(0,$len)];

        }

    }

    /**
     * 生成背景
     */
    private function createBg()
    {

        $this->img = imagecreatetruecolor($this->width, $this->height);

        $color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));

        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);

    }

    /**
     * 生成文字
     */
    private function createFont()
    {

        $_x = $this->width / $this->codeLength;

        for ($i=0;$i<$this->codeLength;$i++)
        {

            $this->fontColor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));

            imagettftext($this->img,$this->fontSize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontColor,$this->font,$this->code[$i]);

        }

    }

    /**
     * 生成线条、雪花
     */
    private function createLine()
    {

        //线条
        for ($i=0;$i<6;$i++)
        {

            $color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));

            imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);

        }

        //雪花
        for ($i=0;$i<100;$i++)
        {

            $color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));

            imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);

        }

    }

    /**
     * 输出图像
     */
    private function output()
    {

        header('Content-type:image/png');

        imagepng($this->img);

        imagedestroy($this->img);

    }

    /**
     * 浏览器输出图像验证码
     */
    public function outputImg()
    {

        $this->createBg();

        $this->createCode();

        $this->createLine();

        $this->createFont();

        $this->output();

    }

    /**
     * 获取验证码
     * @return string
     */
    public function getCode()
    {

        return strtolower($this->code);
        
    }
}