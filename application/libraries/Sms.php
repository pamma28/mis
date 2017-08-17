<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/google/vendor/autoload.php";
 
class Sms{
	private $usersms;
	private $passsms;
	private $nohp;
	private $urlnotif;
	private $urlbc;
	private $apinotif;
	private $apibc;
	
    public function __construct() {
		$CI =& get_instance();
		$CI->load->model('Msetting');
		
	$this->usersms= $CI->Msetting->getset('usersmsnotif');
	$this->passsms= $CI->Msetting->getset('passsmsnotif');
	$this->nohp= $CI->Msetting->getset('nosmsnotif');
	$this->urlnotif= $CI->Msetting->getset('httpsmsnotif');
	$this->urlbc= $CI->Msetting->getset('httpsmsbc');
	$this->apinotif= $CI->Msetting->getset('apismsnotif');
	$this->apibc= $CI->Msetting->getset('apismsbc');
    }
	
	public function sendnotif($no,$code){
		$ret = file_get_contents($this->urlnotif.$this->apinotif."?user=".$this->usersms."&pass=".$this->passsms."&no=".$no."&isi=".urlencode($code));
		return $ret;
	}
	
	public function sendbc($no,$code){
		$ret = file_get_contents($this->urlbc.$this->apibc."?user=".$this->usersms."&hpku=".$this->nohp."&kirimke=".$no."&isi=".urlencode($code));
		return $ret;
	}
	
	
	
	
	
}