<?php
namespace Api\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class JwtAuthMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        new \Tuupola\Middleware\JwtAuthentication([
            //"path" => "/api/acceptance", /* or ["/api", "/admin"] */
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
        ]);
    }
}

 ?>
