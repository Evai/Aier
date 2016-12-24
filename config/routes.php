<?php

use NoahBuscher\Macaw\Macaw as Route;

Route::get('/', function() {
  echo "Welcome";
});

Route::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Route::get('home', 'HomeController@home');
Route::get('mail', 'HomeController@mail');
Route::get('redis', 'HomeController@redis');

Route::error(function() {
    throw new Exception("404 Not Found");
});


Route::dispatch();
