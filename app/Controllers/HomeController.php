<?php

class HomeController extends BaseController
{

    function __construct()
    {

    }

    public function home()
    {
        $user = Users::find(1); //your model

        return View::make('home')
            ->with('user', $user)
            ->withTitle('Aier')
            ->withShowMsg('hello Aier');
    }

    function mail()
    {
        /*Mail::to(['xxx@qq.com'])
            ->from('Evai <xxx@163.com>')
            ->title('Hello World')
            ->content('<h1>Hello World !</h1>');
        echo '发送邮件成功';*/
    }

    function redis()
    {
        Redis::set('name', 'Evai', 5);

        echo Redis::get('name');

    }

}
