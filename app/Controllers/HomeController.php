<?php

class HomeController extends BaseController
{

    function __construct()
    {

    }

    public function home()
    {
        /*return View::make('home')
            ->with('article', Articles::find(1))
            ->withTitle('Frame')
            ->withShowMsg('hello world');*/

    }

    function mail()
    {
       /* Mail::to(['xxx@qq.com'])
            ->from('Evai <xxx@163.com>')
            ->title('Hello World')
            ->content('<h1>Hello World !</h1>');
        echo '发送邮件成功';*/
    }

    function redis()
    {
        /*Redis::set('name', 'Evai', 5);

        echo Redis::get('name');*/

    }

}
