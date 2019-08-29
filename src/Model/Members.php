<?php
namespace Api\Model;

use \PDO;

class Members extends BaseModel {
    public function insertMember($post) {
        $password = password_hash($post['m_pass'], PASSWORD_DEFAULT);
        $date = date('Y-m-d H:i:s', time());
        $query = $this->db->prepare('insert into api_members (m_email, m_name, m_pass, m_register) values (:email, :name, :pass, :register)');

        try {
            $this->db->beginTransaction();
            $query->bindParam(':email', $post['m_email']);
            $query->bindParam(':name', $post['m_name']);
            $query->bindParam(':pass', $password);
            $query->bindParam(':register', $date);
            $query->execute();
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getMember($post){
        $query = $this->db->prepare('select * from api_members where m_email = :email');
        $query->bindParam(':email', $post['m_email']);
        $query->execute();
        return $query->fetch();
    }

    public function signup($post){
        if($memInfo = $this->getMember($post)){
            if(password_verify($post['m_pass'], $memInfo['m_pass'])){
                return $memInfo;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
?>
