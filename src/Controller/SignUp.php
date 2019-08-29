<?php
namespace Api\Controller;

use Api\Model\Members;
use Slim\Http\Request;
use Slim\Http\Response;
use voku\helper\AntiXSS;
use \Firebase\JWT\JWT;

class SignUp extends BaseController {
    public function __invoke(Request $request, Response $response, array $args){
        $antiXss = new AntiXSS();

        $postParams = $request->getParsedBody();
        $results_json = array(
            'status'    => "error",
            'message'   => '아이디 또는 비밀번호를 확인해 주세요.',
            'code'      => 400
        );
        if($request->getHeaderLine('Authorization') === 'Bearer '.$this->container->get('settings')['jwt']['apikey']){
            $members_model = new \Api\Model\Members($this->container->get('db_pdo'));
            if($member_info = $members_model->signup($postParams)){
                $key = $this->container->get('settings')['jwt']['secret'];
                $token_opt = array(
                    'iss' => 'http://115.68.223.169/'.time(),
                    'aud' => 'PSUAPIADM'.time(),
                    'exp'   => (time() + 360*5),
                    'iat'   => time(),
                    'jti'   => time(),
                    'data'  => array(
                        'name' => $member_info['m_name'],
                        'email' => $member_info['m_email']
                    )
                );
                $token = JWT::encode($token_opt, $key, "HS256");
                $results_json['status'] = 'success';
                $results_json['token'] = $token;
                $results_json['code'] = 200;
                $results_json['message'] = '';
            }
        }
        $response->getBody()->write(json_encode($results_json));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8;')->withStatus($results_json['code']);
    }
}
 ?>
