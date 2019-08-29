<?php
namespace Api\Controller;

use Api\Model\Acceptance;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

class PutAcceptanceUser extends BaseController {
    public function __invoke(Request $request, Response $response, array $args) {
        $antiXss = new AntiXSS();
        $this->container->get('logger')->info("Slim-Skeleton '/' route");
        $postParams = $request->getParsedBody();
        $results_json = array(
            'status'    => "error",
            'message'   => '필수 파라메터가 없습니다.',
            'code'      => 400
        );
        foreach($postParams as $key=>$val){
            $postParams[$key]   = $antiXss->xss_clean($val);
        }
        $acceptance_model = new Acceptance($this->container->get('db_pdo'), 'pdo');
        if($acceptance_model->userPut($postParams)){
            $results_json['status']     = "success";
            $results_json['message']    = '수정완료';
            $results_json['code']       = 200;
        }else{
            $results_json['message'] = 'Server error try again.';
        }
        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus($results_json['code']);
    }
}
 ?>
