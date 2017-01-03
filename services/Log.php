<?php

date_default_timezone_set('PRC');
/**
 * Class Log
 */
class Log
{
    public $path = BASE_PATH . '/log';

    /**
     * Log constructor.
     * @param $msg
     * @param string $path
     */
    public function __construct($msg, $path = '')
    {
        //日志路径
        $path = $path ? $path : $this->path;
        //每天生成一个日志文件
        $filePath = $path . '/' . date('Y-m-d');

        if (!is_dir($filePath)) mkdir($filePath, 0777, true);
        //每小时生成一个日志文件，防止日志文件过大
        $nowTime = date('H');
        //文件名
        $fileName = $filePath . '/' . $nowTime . '.log';
        //记录日志时间
        $prefix = date('Y-m-d H:i:s') . "\t---\t";

        if (file_put_contents($fileName, $prefix . $msg . PHP_EOL, FILE_APPEND))
        {
            return true;
        }

        return false;

    }

    /**
     * @param $msg
     * @param string $path
     * @return Log
     */
    public static function info($msg, $path = '')
    {
        return new Log($msg, $path);
    }
}