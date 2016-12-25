<?php

use NoahBuscher\Macaw\Macaw;

Macaw::get('/', function() {
  echo "Welcome";
});

Macaw::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Macaw::get('home', 'HomeController@home');
Macaw::get('mail', 'HomeController@mail');
Macaw::get('redis', 'HomeController@redis');

Macaw::error(function() {
    throw new Exception("404 Not Found");
});


Macaw::dispatch();
