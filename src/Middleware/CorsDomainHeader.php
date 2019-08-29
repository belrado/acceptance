<?php

namespace Api\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

class CorsDomainHeader {
    public function __invoke(Request $request, Response $response, $next){
        $response = $next($request, $response);
        $allowed_host = 'http://psuedu.org';
        $allowed_origin_host = array($allowed_host, 'http://mediprep.co.kr', 'https://umvietnam.com');
        $origin_host = $request->getHeaderLine('Origin');
        if(in_array($origin_host, $allowed_origin_host)){
            $allowed_host = $origin_host;
        }
        return $response
        ->withHeader('Access-Control-Allow-Origin', $allowed_host)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET');
    }
}
 ?>
