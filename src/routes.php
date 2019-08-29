<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
//use Api\Controller;

return function (App $app) {
    $container = $app->getContainer();
    function jwtAuthMiddlewareFnc ($container) {
        return new \Tuupola\Middleware\JwtAuthentication([
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
        ]);
    }

    $app->get('/api/acceptance[/{id:[0-9\w]*}]', '\Api\Controller\GetAcceptanceUser')->add(new \Api\Middleware\CorsDomainHeader());
    $app->post('/api/acceptance', '\Api\Controller\InsertAcceptanceUser')->add(jwtAuthMiddlewareFnc($container));
    $app->post('/api/signup', '\Api\Controller\SignUp');
    // 보안등에 관한 문제로 delete put 은 안쓰고 URL 대체
    $app->post('/api/acceptance/delete[/{id:[0-9]*}]', '\Api\Controller\DeleteAcceptanceUser')->add(jwtAuthMiddlewareFnc($container));
    $app->post('/api/acceptance/put[/{id:[0-9]*}]', '\Api\Controller\PutAcceptanceUser')->add(jwtAuthMiddlewareFnc($container));
    // api 관리자 생성
    //$app->post('/admin/member', '\Api\Controller\InsertMember');
};
