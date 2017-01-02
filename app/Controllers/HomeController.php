<?php

use Illuminate\Database\Capsule\Manager as DB;
/**
 * Class HomeController
 */
class HomeController extends BaseController
{
    /**
     * HomeController constructor.
     */
    function __construct()
    {

    }

    public function home()
    {
        $user = Users::find(1); //your model

        /*return View::make('home')
            ->with('user', $user)
            ->withTitle('Aier')
            ->withShowMsg('hello Aier');*/
        //setcookie("user", "Alex Porter", time()+60);
        //session_start();
        //session_destroy();
//        if (isset($_SESSION['view'])) {
//            $_SESSION['view'] += 1;
//        } else {
//            $_SESSION['view'] = 1;
//        }
        //echo json_encode($_SESSION);

        $data = DB::table('users')->where('userid', 1)->first();
        //var_dump($data);
        $rsa = new RSACrypt('./');

        //exit(var_dump($rsa->publicKey));
        // 使用公钥加密
        $str = $rsa->publicEncrypt('hello');
        // 这里使用base64是为了不出现乱码，默认加密出来的值有乱码

        $str = base64_encode($str);

        echo "公钥加密（base64处理过）：\n", $str, "\n";
        $str = base64_decode($str);
        $pubstr = $rsa->publicDecrypt($str);
        echo "公钥解密：\n", $pubstr, "\n";
        $privstr = $rsa->privateDecrypt($str);
        echo "私钥解密：\n", $privstr, "\n";

//// 使用私钥加密
        $str = $rsa->privateEncrypt('world');
// 这里使用base64是为了不出现乱码，默认加密出来的值有乱码
        $str = base64_encode($str);
        echo "私钥加密（base64处理过）：\n", $str, "\n";
        $str = base64_decode($str);
        $pubstr = $rsa->publicDecrypt($str);
        echo "公钥解密：\n", $pubstr, "\n";
        $privstr = $rsa->privateDecrypt($str);
        echo "私钥解密：\n", $privstr, "\n";
    }

    function mail()
    {
        Mail::to(['xxx@qq.com'])
            ->from('Evai <xxx@163.com>')
            ->title('Hello World')
            ->content('<h1>Hello World !</h1>');
        echo '发送邮件成功';
    }

    function redis()
    {
        Redis::set('name', 'Evai', 5);

        echo Redis::get('name');

    }

    /**
     * @return Twig
     */
    function test()
    {
        $data = ['data' => ['name' => 'evai', 'mobile' => 12312321213]];

        Log::info(json_encode($_SERVER));

        return Twig::render('index.twig', $data);
    }

}
