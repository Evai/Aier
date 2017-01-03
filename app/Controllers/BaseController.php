<?php

/**
 * Class BaseController
 */

class BaseController

{
    public $resArr = array('code' => -1, 'msg' => 'request error');
    /**
     * BaseController constructor.
     */
    public function __construct()
    {

    }

    /**
     * 请求次数限制
     * @param int $limit  次数限制
     * @param int $range  时间限制(单位：秒)
     * @throws Exception
     */
    public function requestLimit($limit = 60, $range = 60)
    {
        session_start();
        //请求时间
        $RequestTime = $_SERVER['REQUEST_TIME'];
        //请求唯一标识
        $session_id = session_id();
        //请求路由
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        //记录请求次数
        $_SESSION['request' . $requestUrl . $session_id] = isset($_SESSION['request' . $requestUrl . $session_id]) ? $_SESSION['request' . $requestUrl . $session_id] : 1;
        //记录最后请求时间
        $_SESSION['finalRequestTime'] = isset($_SESSION['finalRequestTime']) ? $_SESSION['finalRequestTime'] : $RequestTime;

        $requestRemain = $limit - $_SESSION['request' . $requestUrl . $session_id];

        if ($RequestTime - $_SESSION['finalRequestTime'] < $range) {

            if ($requestRemain <= 0)
            {
                throw new \Exception('Maximum number of requests exceeded limit');
            }

            $_SESSION['request' . $requestUrl . $session_id] += 1;

        } else {

            unset($_SESSION['request' . $requestUrl . $session_id], $_SESSION['finalRequestTime']);

        }

        header('X-RateLimit-Limit:' . $limit);
        header('X-RateLimit-Remaining:' . $requestRemain);

    }

    /**
     * 验证字段
     * @param string $msg
     * @param null $param
     * @param string $default
     * @param int $length
     * @param bool $checkEmpty
     * @return int|null|string
     */
    public function getArgs($msg = '参数名称', $param = null, $default = '', $length = 0, $checkEmpty = false)
    {
        if(isset($param)) {

            if (!is_string($param)) {

                exit($this->set_return(-400, $msg . '字段类型错误，请用 String 类型', 'none'));

            }

            $param = trim($param);

            if ($length > 0 && strlen($param) > $length) {

                exit($this->set_return(-400, $msg . ' 字段名称过长,请不要超过' . $length . '个字节', 'none'));

            }

            $param = stripcslashes($param);

            $param = is_numeric($default) ? intval($param) : $param;

            if ($checkEmpty) $this->is_empty($msg, $param);

            return $param;

        } else {

            if ($checkEmpty) $this->is_empty($msg, $default);

            return $default;

        }

    }

    /**
     * 检测值是否为空
     * @param $msg
     * @param $param
     */
    public function is_empty($msg, $param)
    {
        if(empty($param)) {

            exit($this->set_return(-300, $msg . ' 字段名称不能为空', 'none'));

        }
    }

    /**
     * 请求返回值
     * @param int $code
     * @param string $msg
     * @param string $type
     * @param array $data
     * @return string
     */
    function set_return($code = 500, $msg = '', $type = 'array', $data = array())
    {
        $this->resArr['code'] = $code;
        $this->resArr['msg'] = $msg;

        switch ($type) {
            case 'array':
                $this->resArr['data'] = $data ? $data : array();
                break;
            case 'object':
                $this->resArr['data'] = $data ? $data : (Object)array();
                break;
            case 'string':
                $this->resArr['data'] = $data ? $data : '';
                break;
            case 'none':
                break;
        }

        return json_encode($this->resArr, JSON_UNESCAPED_UNICODE);
    }

}