<?php

use Predis\Client;

/**
 * Class Redis
 */
class Redis

{

    const CONFIG_FILE = '/config/redis.php';

    protected static $redis;

    /**
     * Redis constructor.
     */
    private static function init()

    {

        self::$redis = new Client(require BASE_PATH . self::CONFIG_FILE);

    }

    /**
     * @param $key
     * @param $value
     * @param int $timeout
     * @param string $unit
     * @return mixed
     */
    public static function set($key, $value, $timeout = 0, $unit = 's')

    {
        self::init();

        $result = self::$redis->set($key, $value);

        if ($timeout > 0)
        {

            switch ($unit)
            {
                case 's':   //秒

                    self::$redis->expire($key, $timeout);

                    break;

                case 'unix':    //unix时间戳(单位：秒)

                    self::$redis->expireAt($key, $timeout);

                    break;

                case 'ms':  //毫秒

                    self::$redis->pExpire($key, $timeout);

                    break;

                case 'munix':   //unix时间戳(单位：毫秒)

                    self::$redis->pExpireAt($key, $timeout);

                    break;

                default:

                    break;
            }

        }

        return $result;

    }

    /**
     * @param $key
     * @return string
     */
    public static function get($key)

    {
        self::init();

        return self::$redis->get($key);

    }

    /**
     * @param $key
     * @return mixed
     */
    public static function delete($key)

    {
        self::init();

        return self::$redis->del($key);
    }

}