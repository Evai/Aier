# Aier

**A Simple PHP Frame**

***
这是个很轻很小的框架，可以快速开发移动端和web端

This is a small frame, and fast development for mobile or web.

## 安装 Installation

```bash
$ cd Aier
$ composer update
$ composer dump-autoload

```

## DEMO

### 路由 Route

在 config/route.php 配置文件中添加路由：

Add routes in config/route.php:

```php
use NoahBuscher\Macaw\Macaw;

Macaw::get('/', function() {
  echo "Welcome";
});

Macaw::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Macaw::get('home', 'HomeController@home'); //your controller class

Macaw::error(function() {
    throw new Exception("404 Not Found"); //not found route then write
});

Macaw::dispatch(); //Don't forget add in the end

```

### 发送邮件 Email Send

假设现在你创建了一个HomeController.php控制器文件：

For this demo lets say you have a folder called controllers with a HomeController.php

```php
class HomeController extends BaseController
{

function email()
{
	Mail::to(['xxx@qq.com'])
        ->from('Evai <xxx@163.com>')
        ->title('Hello World')
        ->content('<h1>Hello World !</h1>');

		echo '发送邮件成功';
}

```

### 模型 Model

在创建模型之前，请确保你的mysql服务已经开启并可以正常访问。然后打开 config/database.php 进行配置。

Before you create the model, make sure your MySQL service is turned on and can be accessed properly. Then open config/database.php for configuration.

我们创建一个新的用户模型：

We will create a new Users Model:

```php
<?php

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{

    public $table = 'users'; //your table name

    public $primaryKey = 'userid'; // your primary key

}


```

更多的用法请参考：[Eloquent] (https://laravel.com/docs/5.3/eloquent)

For more example refer to：[Eloquent] (https://laravel.com/docs/5.3/eloquent)

### 视图装载 View

在 HomeController 中添加如下：

Add HomeController:

```php

public function home()
{
    $user = Users::find(1); //your model

    return View::make('home') //your template
        ->with('user', $user)
        ->withTitle('Aier')
        ->withShowMsg('hello Aier');
}

```

这个模板文件在 app/Views/home.php 文件，你可以来这样获取变量：

This template in app/Views/home.php , you can get variables look like:

```php
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title><?php echo $title ?></title>

</head>

<body>

<div class="article">

    <h1><?php echo $user['name'] ?></h1>

    <div class="content">

        <?php echo $user  ?>

    </div>

</div>

<ul class="msg">

    <h1><?php echo $show_msg ?></h1>

</ul>

</body>

</html>

```

### Redis

在 HomeController 中添加如下：

Add HomeController:

```php
function redis()
{
    Redis::set('name', 'Evai', 5);

    echo Redis::get('name');

}

```

如果你新建了一个控制器或模型，请务必执行下 composer dump-autoload 命令。

if you create a new Controller or Model, please run command : composer dump-autoload.