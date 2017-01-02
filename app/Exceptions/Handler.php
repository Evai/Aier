<?php

/**
 * Class Handler
 */
class Handler extends Exception
{
    //根据业务需求，自定义方法
    /**
     * @param $exception
     */
    public function getErrorInfo($exception)
    {
        $err = [
            'code' => $exception->getCode(),
            'msg'  => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'   => $exception->getLine(),
            'class' => get_class($exception)
        ];

        echo json_encode($err);
    }

    /**
     *
     */
    public function render()
    {

        // whoops 错误提示

        $whoops = new \Whoops\Run;

        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

        $whoops->register();

        //自定义错误格式
        //set_exception_handler([$this, 'getErrorInfo']);
    }

}