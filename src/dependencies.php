<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };

    // db pdo
    $container['db_pdo'] = function ($c) {
        $db = $c->get('settings')['db'];
        $con = new PDO('mysql:host=' . $db['host'] . ';port='.$db['port'].';dbname=' . $db['dbname'], $db['user'], $db['pass']);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $con;
    };

    // db mysqli
    $container['db_mysqli'] = function ($c) {
        $db = $c->get('settings')['db'];
        $con = new mysqli($db['host'], $db['user'], $db['pass'], $db['dbname'], $db['port']);
        if(!$con->connect_error){
            return $con;
        }else{
            return false;
        }
    };
};
