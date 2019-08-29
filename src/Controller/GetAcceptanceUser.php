<?php
namespace Api\Controller;

use Api\Model\Acceptance;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use voku\helper\AntiXSS;

class GetAcceptanceUser extends BaseController {
    public function __invoke(Request $request, Response $response, array $args) {
        $antiXss = new AntiXSS();
        $this->container->get('logger')->info("Slim-Skeleton '/' route");
        $getParams  = $request->getQueryParams();
        $page       = (isset($getParams['page'])&&is_numeric($getParams['page']))?$getParams['page']:1;
        $limit      = (isset($getParams['limit'])&&is_numeric($getParams['limit']))?$getParams['limit']:14;
        $offset     = (isset($getParams['offset'])&&is_numeric($getParams['offset']))?$getParams['offset']:0;
        $univ_type  = (isset($getParams['univ_type']))?$antiXss->xss_clean($getParams['univ_type']):'all';
        $pass       = (isset($getParams['pass']))?$antiXss->xss_clean($getParams['pass']):'none';
        $select     = (isset($getParams['select']))?$antiXss->xss_clean($getParams['select']):'';
        $search     = (isset($getParams['search']))?$antiXss->xss_clean($getParams['search']):'';
        $admin      = (isset($getParams['admin']))?$antiXss->xss_clean($getParams['admin']):'';
        $acceptance_model = new Acceptance($this->container->get('db_pdo'), 'pdo');
        $results_json = array(
            'status'    => "error",
            'message'   => '',
            'code'      => 400
        );
        if (isset($args['id'])&&is_numeric($args['id'])) {
            $results = $acceptance_model->getUser($args['id']);
        } else {
            $total  = $acceptance_model->getTotal($univ_type, $pass, $select, $search);
            $results = $acceptance_model->getList($limit, $offset, $univ_type, $pass, $select, $search, $admin);
        }
        if ($results) {
            $results_json['status'] = "success";
            $results_json['total']  = $total;
            $results_json['data']   = $this->value_strip_tags($results, '<br />,<br>', $admin);
            $results_json['code']   = 200;
        } else {
            $results_json['message'] = 'Server error try again.';
        }
        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8;')->withStatus($results_json['code']);
    }
}
?>
