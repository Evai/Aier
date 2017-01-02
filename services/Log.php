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

        $path = $path ? $path : $this->path;

        $filePath = $path . '/' . date('Y-m-d');

        if (!is_dir($filePath)) mkdir($filePath, 0777, true);

        $nowTime = date('H');

        $fileName = $filePath . '/' . $nowTime . '.log';

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