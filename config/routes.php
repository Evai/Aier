<?php

use Evai\Tenden\Tenden;

Tenden::get('/', function() {
  echo "Welcome";
});

Tenden::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Tenden::get('home', 'HomeController@home');
Tenden::get('mail', 'HomeController@mail');
Tenden::get('redis', 'HomeController@redis');
Tenden::get('test', 'HomeController@test');

Tenden::error(function() {
    throw new Exception("404 Not Found");
});


Tenden::dispatch();
