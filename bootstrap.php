<?php

use Illuminate\Database\Capsule\Manager as Capsule;

// 定义 BASE_PATH

define('BASE_PATH', __DIR__);

define('APP_PATH', __DIR__ . '/app');

define('DEBUG', 1);

// Autoload 自动载入

require BASE_PATH . '/vendor/autoload.php';

// Eloquent ORM

$capsule = new Capsule;

$capsule->addConnection(require BASE_PATH . '/config/database.php');

// Set the event dispatcher used by Eloquent models... (optional)
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

if (DEBUG) {

    require BASE_PATH.'/app/Exceptions/Handler.php';

    $error = new Handler();

    $error->render();

} else {

    ini_set('display_errors', '0');
}

