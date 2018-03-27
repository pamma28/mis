<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
class Sms{
	private $usersms;
	private $passsms;
	private $nohp;
	private $urlsms;
	private $apibalance;
	
    public function __construct() {
		$CI =& get_instance();
		$CI->load->model('Msetting');
		$this->usersms= $CI->Msetting->getset('smsuserkey');
		$this->passsms= $CI->Msetting->getset('smspasskey');
		$this->urlsms= $CI->Msetting->getset('smsurl');
		$this->apisms= $CI->Msetting->getset('smsapi');
		$this->apibalance= $CI->Msetting->getset('smsapibalance');
    }

	public function sendsms($no,$code){
		$ret = simplexml_load_string(file_get_contents($this->urlsms.$this->apisms."?userkey=".$this->usersms."&passkey=".$this->passsms."&nohp=".$no."&pesan=".urlencode($code)));
		$json = json_encode($ret);
		$jsondecode = json_decode($json);
		$r = ($jsondecode->message->text=='Success') ? true : false;
		return $r;
	}

	public function checkcredit(){
		$ret = simplexml_load_string(file_get_contents($this->urlsms.$this->apibalance."?userkey=".$this->usersms."&passkey=".$this->passsms));
		$json = json_encode($ret);
		return $json;
	}
	
	// userkey mwlp7j
	// passkey haha
	// https://reguler.zenziva.net/apps/smsapi.php?userkey=$userkeyanda&passkey=$passkeyanda&nohp=$nohptujuan&pesan=isi pesan
	// https://reguler.zenziva.net/apps/smsapibalance.php?userkey=$userkeyanda&passkey=$passkeyanda
	
	
	
	
	
}