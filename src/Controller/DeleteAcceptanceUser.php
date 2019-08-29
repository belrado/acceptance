<?php
namespace Api\Controller;

use Api\Model\Acceptance;
use Slim\Http\Request;
use Slim\Http\Response;

class DeleteAcceptanceUser extends BaseController {
    public function __invoke(Request $request, Response $response, array $args) {
        $postParams = $request->getParsedBody();
        $results_json = array(
            'status'    => "error",
            'message'   => '필수 파라메터가 없습니다.',
            'code'      => 400
        );
        if(isset($postParams['app_chk']) && is_array($postParams['app_chk']) || isset($args['id']) && is_numeric($args['id'])){
            if(isset($postParams['app_chk']) && is_array($postParams['app_chk'])){
                foreach($postParams['app_chk'] as $val){
                    if(!is_numeric($val)){
                        $response->getBody()->write(json_encode($results_json));
                        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus($results_json['code']);
                    }
                }
            }else{
                $postParams['app_chk'] = array($args['id']);
            }
            $acceptance_model = new Acceptance($this->container->get('db_pdo'), 'pdo');
            if($acceptance_model->userDelete($postParams['app_chk'])){
                $results_json['status']     = "success";
                $results_json['message']    = '삭제완료';
                $results_json['code']       = 200;
            }else{
                $results_json['message'] = 'Server error try again.';
            }
        }
        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus($results_json['code']);
    }
}
?>
