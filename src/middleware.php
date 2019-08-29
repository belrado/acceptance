<?php

use Slim\App;

return function (App $app) {
    // e.g: $app->add(new \Slim\Csrf\Guard);
    //$container = $app->getContainer();
    //$app->add(new \Api\Middleware\Cors());
    /*
    $container = $app->getContainer();
    $app->add(new \Tuupola\Middleware\JwtAuthentication([
        "path" => "/api/acceptance",
        //"ignore"    => "/api/acceptance",
        "header"    => "X-token",
        "attribute" => "jwt_token",
        "secret"    => $container->get('settings')['jwt']['secret'],
        "algorithm" => ["HS256"],
        "secure"    => false,
        "error" => function ($response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response->withHeader("Content-Type", "application/json; utf-8")->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
    ]));
    */
};
