<?php
namespace Api\Controller;

use Api\Model\Acceptance;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

class InsertAcceptanceUser extends BaseController {
    public function __invoke(Request $request, Response $response, array $args){
        $antiXss = new AntiXSS();

        $postParams = $request->getParsedBody();
        $getParams  = $request->getQueryParams();
        $checkArrayPostParams = array('app_univ_type', 'app_name', 'app_year', 'app_school', 'app_enrolled');
        $results_json = array(
            'status'    => "error",
            'message'   => '필수 파라메터가 없습니다.',
            'code'      => 400
        );
        foreach($checkArrayPostParams as $key=>$param){
            if(isset($postParams[$param]) && !empty($postParams[$param])){
                if(is_array($postParams[$param])){
                    foreach($postParams[$param] as $val){
                        if(empty(trim($val))){
                            $response->getBody()->write(json_encode($results_json));
                            return $response->withHeader('Content-Type', 'application/json; charset=utf-8;')->withStatus(400);
                        }
                    }
                }
            }else{
                $response->getBody()->write(json_encode($results_json));
                return $response->withHeader('Content-Type', 'application/json; charset=utf-8;')->withStatus(400);
            }
        }
        $ndata = array();
        foreach($postParams as $name=>$val){
            if($name === 'mode') continue;
            if(is_array($val)){
                foreach($val as $len=>$val2){
                    $ndata[$len][$name] = $antiXss->xss_clean($val2);
                }
            }else{
                $ndata[$name]  = $antiXss->xss_clean($val);
            }
        }
        $acceptance_model = new Acceptance($this->container->get('db_pdo'), 'pdo');
        if($acceptance_model->userInsertMultiple($ndata)){
            $results_json['status']     = "success";
            $results_json['message']    = '등록완료';
            $results_json['code']       = 200;
        }else{
            $results_json['message'] = 'Server error try again.';
        }
        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus($results_json['code']);
    }
}
?>
