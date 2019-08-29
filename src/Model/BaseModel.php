<?php
namespace Api\Model;

use \PDO;

abstract class BaseModel {

    protected $db;

    public function __construct($con=false, $type="pdo"){
        if($con){
            $this->db = $con;
        }else{
            $dbconfig = require __DIR__ . '/../../src/settings.php';
            $db = $dbconfig['settings']['db'];
            $this->db = new PDO('mysql:host=' . $db['host'] . ';port='.$db['port'].';dbname=' . $db['dbname'], $db['user'], $db['pass']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
    }
}
?>
