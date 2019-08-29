<?php
namespace Api\Controller;

use Api\Model\Members;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;

class InsertMember extends BaseController {
    public function __invoke(Request $request, Response $response, array $args){
        $antiXss = new AntiXSS();
        $postParams = $request->getParsedBody();
        $results_json = array(
            'status'    => 'error',
            'message'   => '필수데이터 입력 오류.',
            'code'      => 400
        );

        if(isset($postParams['m_email']) && isset($postParams['m_pass']) && isset($postParams['m_name']) &&
            preg_match('/^[a-zA-Z0-9]([-_.]?[0-9a-zA-Z])+@[a-zA-Z0-9]+.[a-zA-Z]{2,4}$/', $postParams['m_email'])
            && !empty($postParams['m_pass']) && !empty($postParams['m_name'])){
            $postParams['m_name'] = $antiXss->xss_clean($postParams['m_name']);
            $postParams['m_pass'] = md5($postParams['m_pass']);
            $members_model = new \Api\Model\Members($this->container->get('db_pdo'));

            if($members_model->insertMember($postParams)){
                $results_json['status']     = 'success';
                $results_json['message']    = 'PSU API 멤버 등록완료.';
                $results_json['code']       = 200;
            }else{
                $results_json['message'] = '등록실패 잠시 후 다시 시도해주세요.';
            }
        }

        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus($results_json['code']);
    }
}
 ?>
