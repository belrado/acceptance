<?php
namespace Api\Controller;
use Slim\Container;
abstract class BaseController {
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    protected function value_strip_tags($data, $allow='', $adm='') {
        $rdata = array();
        if (is_array($data)){
            foreach ($data as $len=>$row) {
                if (is_array($row)) {
                    foreach ($row as $key=>$val) {
                        if ($key === 'app_name' && $adm != 'psu') {
                            $rdata[$len][$key] = stripslashes($this->fnc_name_change($val));
                        } else {
                            $rdata[$len][$key] = strip_tags(stripslashes($val), $allow);
                        }
                    }
                } else {
                    $rdata[$len] = strip_tags(stripslashes($row), $allow);
                }
            }
        } else {
            $rdata = strip_tags(stripslashes($data), $allow);
        }
        return $rdata;
    }

    protected function fnc_name_change($name, $cut=1, $cha='O') {
    	$chName='';
    	$sn = trim($name);
    	$sn = preg_replace("/\s+/", "", $sn);
    	$sn = explode('@', $sn);
    	$mails = (isset($sn[1]))?$sn[1]:'';
    	$len = mb_strlen($sn[0],'UTF-8');
    	$first_name = mb_substr($sn[0],0,$cut,'UTF-8');
    	if ($cut >= $len) {
    		$cut = $len-2;
    	}
    	for ($i=$cut; $i<$len; $i++) {
    		$chName .= $cha;
    	}
    	$sn[0] = $first_name." ".$chName;
    	if ($mails !== '') {
    		return $sn[0].'@'.$mails;
    	} else {
    		return $sn[0].' '.$mails;
    	}
    }
}
 ?>
