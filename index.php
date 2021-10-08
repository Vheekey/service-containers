<?php

use App\Database\Database;
use App\Service\Home;

require_once __DIR__.'/vendor/autoload.php';

$container = new \App\Container\Container;

//$container->share('config', function (){
//    return new App\Config\Config;
//});

$container->share(Database::class, function ($container){
    return new Database($container->get(App\Config\Config::class));
});

dump($container->get(Home::class)->index());

//dump((new \App\Service\Home($container->get(\App\Config\Config::class), $container->get(\App\Database\Database::class)))->index());
