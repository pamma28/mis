<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 

 
class Smssender {
	private $urlsaldo;
	private $urlnotif;
	private $saldofunc;
	private $bcfunc;
	private $notiffunc;
	private $user;
	private $pass;
	private $nohp;
	
    public function __construct() {
		$CI =& get_instance();
		$CI->load->model('Msetting');
		$this->urlsaldo = $CI->Msetting->getset('httpsmsbc');
		$this->urlnotif = $CI->Msetting->getset('httpsmsnotif');
		$this->saldofunc = $CI->Msetting->getset('ceksaldosms');
		$this->bcfunc = $CI->Msetting->getset('apismsbc');
		$this->notiffunc = $CI->Msetting->getset('apismsnotif');
		$this->user = $CI->Msetting->getset('usersmsnotif');
		$this->pass = $CI->Msetting->getset('passsmsnotif');
		$this->nohp = $CI->Msetting->getset('nosmsnotif');
    }
		
	public function sendsms($to,$code,$userep){
		$urlsaldo = $this->urlsaldo;
		$urlnotif = $this->urlnotif;
		$bcfunc = $this->bcfunc;
		$notiffunc = $this->notiffunc;
		$user = $this->user;
		$pass = $this->pass;
		$nohp = $this->nohp;
		
		//alter some chars
		$code = str_replace("\n", "~", $code);
		
		($userep) ?  $result = file_get_contents($urlsaldo.$bcfunc.'?user='.$user.'&hpku='.$nohp.'&kirimke='.$to.'&isi='.urlencode($code)) : $result = file_get_contents($urlnotif.$notiffunc.'?user='.$user.'&pass='.$pass.'&no='.$to.'&isi='.urlencode($code));
		(strpos($result, 'sukses') !== false) ? $res = true : $res = false;
		
		return $res;
	}
	
	public function ceksaldo(){
		$urlsaldo = $this->urlsaldo;
		$saldofunc = $this->saldofunc;
		$user = $this->user;
		$nohp = $this->nohp;
		$result = file_get_contents($urlsaldo.$saldofunc.'?user='.$user.'&hpku='.$nohp);
		$arr_res = explode('<br>',$result);
		foreach ($arr_res as $k=>$v){
			$arr_res[$k] = preg_replace("/[^0-9]/", "", $v);
		}
		
		(count($arr_res)>1) ? $return = $arr_res : $return[]= 'Error';
		return json_encode($return);
	}
}