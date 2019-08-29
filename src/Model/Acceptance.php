<?php
namespace Api\Model;

use \PDO;

class Acceptance extends BaseModel {
    public function __construct($con=false){
        parent::__construct($con);
    }
    protected function setWhereQuery($univ_type='all', $waiting='none', $search1='', $search2=''){
        $sql_wating = '';
        $sql_search = '';
        $sql_univ   = '';
        if($waiting !== 'none' || empty($waiting)){
            $sql_wating = ' app_pass="'.$waiting.'" ';
        }
        if(!empty($search1) && !empty($search2)){
            $sql_search = ' '.$search1.' LIKE "%'.$search2.'%" ';
        }
        if($univ_type !== 'all'){
            $sql_univ = $this->setUnivTypeSql($univ_type);
            if(empty($sql_univ) && !empty($univ_type)){
                $sql_univ = ' app_univ_type="'.$univ_type.'" ';
            }
        }
        $sql = $sql_wating.((!empty($sql_wating) && !empty($sql_univ))?' AND ':'').$sql_univ.((!empty($sql_search) && (!empty($sql_univ) || !empty($sql_wating)) )? ' AND ':' ').$sql_search;
        if(empty($sql) || empty(preg_replace('/\s+/', '', $sql))){
            return '';
        }else{
            return 'WHERE '.$sql;
        }
    }
    protected function setUnivTypeSql($univ_type=''){
        switch($univ_type){
            case 'mediprep' :
                $query_univ = ' (app_univ_type = "1약대" OR app_univ_type = "4의대" OR app_univ_type = "5치대") ';
                break;
            case 'special' :
                $query_univ = ' app_univ_type = "3특례&수시';
                break;
            case 'psu' :
            case 'us' :
                $query_univ = ' (app_univ_type = "2일반대" OR app_univ_type = "1약대" OR app_univ_type = "4의대" OR app_univ_type = "5치대") ';
                break;
            case 'umv' :
                $query_univ = ' app_univ_type = "6Umveitnam" ';
                break;
            default :
               $query_univ = '';
        }
        return $query_univ;
    }
    public function getTotal($univ_type = 'all', $waiting='none', $search1='', $search2=''){
        $query_univ = $this->setWhereQuery($univ_type, $waiting, $search1, $search2);
        $query = $this->db->prepare('select count(app_id) from psu_successfulapp '.$query_univ);
        $query->execute();
        return $query->fetchColumn();
    }
    public function getList($limit=10, $start=0,  $univ_type = 'all', $waiting='none', $search1='', $search2='', $adm=''){
        $query_univ = $this->setWhereQuery($univ_type, $waiting, $search1, $search2);
        try {
            $sql = 'select * from psu_successfulapp '.$query_univ.' Order by app_year DESC, app_pass DESC, app_index ASC, ';
            if($adm='psu')
                $sql .= ' app_name ASC ';
            else
                $sql .= ' app_enrolled ASC ';
            $sql .= 'LIMIT :limit OFFSET :offset';
            $query = $this->db->prepare($sql);
            $query->bindParam(':limit', $limit, $this->db::PARAM_INT);
            $query->bindParam(':offset', $start, $this->db::PARAM_INT);
            $query->execute();
            if($results = $query->fetchAll()){
                return $results;
            }else{
                return '입력된 내용이 없습니다.';
            }
        }catch(Exception $e){
            return false;
        }
    }
    public function getUser($id){
        $query = $this->db->prepare('select * from psu_successfulapp where app_id = :appid');
        $query->bindParam(':appid', $id, $this->db::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }
    public function userInsert($post){
        $query = $this->db->prepare(
            'insert into psu_successfulapp
            (
                app_univ_type,
                app_name,
                app_year,
                app_school,
                app_enrolled,
                app_scholarship,
                app_admitted,
                app_applied,
                app_pass,
                app_waiting,
                app_index,
                app_update,
                app_register
            )
            values (
                :app_univ_type,
                :app_name,
                :app_year,
                :app_school,
                :app_enrolled,
                :app_scholarship,
                :app_admitted,
                :app_applied,
                :app_pass,
                :app_waiting,
                :app_index,
                :app_update,
                :app_register)'
        );
        try {
            $app_scholarship = (isset($post['app_scholarship'])?$post['app_scholarship']:'');
            $app_admitted   = isset($post['app_admitted'])?$post['app_admitted']:'';
            $app_applied = isset($post['app_applied'])?$post['app_applied']:'';
            $app_pass   = isset($post['app_pass'])?$post['app_pass']:'no';
            $app_waiting    = isset($post['app_waiting'])?$post['app_waiting']:'yes';
            $app_index  = isset($post['app_index'])?$post['app_index']:1;
            $date       = date('Y-m-d H:i:s', time());
            $this->db->beginTransaction();
            $query->bindParam(':app_univ_type', $post['app_univ_type']);
            $query->bindParam(':app_name', $post['app_name']);
            $query->bindParam(':app_year', $post['app_year']);
            $query->bindParam(':app_school', $post['app_school']);
            $query->bindParam(':app_enrolled', $post['app_enrolled']);
            $query->bindParam(':app_scholarship', $app_scholarship);
            $query->bindParam(':app_admitted', $app_admitted);
            $query->bindParam(':app_applied', $app_applied);
            $query->bindParam(':app_pass', $app_pass);
            $query->bindParam(':app_waiting', $app_waiting);
            $query->bindParam(':app_index', $app_index);
            $query->bindParam(':app_update', $date);
            $query->bindParam(':app_register', $date);
            $query->execute();
            $this->db->commit();
            return true;
        } catch(Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function userInsertMultiple($post){
        $placeholders = array(
            'app_univ_type',
            'app_name',
            'app_year',
            'app_school',
            'app_enrolled',
            'app_scholarship',
            'app_admitted',
            'app_applied',
            'app_pass',
            'app_waiting',
            'app_index',
            'app_update',
            'app_register'
        );
        $sql = 'insert into psu_successfulapp('.implode(",", $placeholders).') values ';
        $insertVlue = array();
        $question_array = array();
        foreach($post as $len=>$val){
            $question = array();
            foreach($placeholders as $val2){
                if(isset($val[$val2])){
                    $insertVlue[] = $val[$val2];
                }else{
                    switch($val2){
                        case 'app_pass' :
                        case 'app_letter' :
                            $insertVlue[] = 'no';
                        break;
                        case 'app_waiting' :
                            $insertVlue[] = 'yes';
                        break;
                        case 'app_index' :
                            $insertVlue[] = 1;
                        break;
                        case 'app_update' :
                        case 'app_register' :
                            $insertVlue[] = date('Y-m-d H:i:s', time());
                        break;
                        default :
                            $insertVlue[] = '';
                    }

                }
                $question[] = '?';
            }
            $question_array[] = '('.implode(",", $question).')';
        }
        $sql = $sql.implode(",", $question_array);
        try {
            $this->db->beginTransaction();
            $query = $this->db->prepare($sql);
            $query->execute($insertVlue);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function userPut($post){
        $placeholders = array();
        $question_array = array();
        $duplicate_key  = array();
        $insertVlue = array();
        foreach($post as $len=>$arr){
            $question   = array();
            foreach($arr as $key=>$val2){
                $insertVlue[] = $val2;
                $question[] = '?';
                if($len==0){
                    $placeholders[] = $key;
                    if($key=='app_id'){
                        continue;
                    }else{
                        $duplicate_key[] = '`'.$key.'` = VALUES(`'.$key.'`)';
                    }
                }
            }
            $question_array[] = '('.implode(", ", $question).')';
        }
        $sql = 'INSERT INTO `psu_successfulapp` ('.implode(", ", $placeholders).') VALUES '.implode(", ", $question_array).' ON DUPLICATE KEY UPDATE '.implode(", ", $duplicate_key);
        try {
            $this->db->beginTransaction();
            $query = $this->db->prepare($sql);
            $query->execute($insertVlue);
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    public function userDelete($app_id){
        $sql = 'delete from psu_successfulapp where ';
        $question_array = array();
        foreach($app_id as $len=>$val){
            $question_array[$len] = 'app_id = ?';
        }
        $sql .= implode(" or ", $question_array);
        try{
            $this->db->beginTransaction();
            $query = $this->db->prepare($sql);
            $query->execute($app_id);
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }

    }
}
 ?>
