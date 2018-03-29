<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('gmail','sms','encryption','convertcode','Cronjob'));
		
		$this->load->model(array('Msetting','Mlogin','Mtmp'));
    }

	public function index(){
		$user = 'member17';
		$tags = array(
			'user'=>$user,
			'type'=>'schemem'
			);

		$postdata = array(
				'do'=>'schemem'
				);
		
        $startdate = "2018-03-29 18:15:20";
		$this->cronjob->createcron($user,$tags,$startdate,$postdata);

	}

	public function test(){
		$userkey = 'org';
		$passkey = md5('org');
		$url = base_url('API/api/sendsms/'.urlencode($userkey).'/'.urlencode($passkey));
		$myvars = 'do=schemem';

		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POST, 1);
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt( $ch, CURLOPT_HEADER, 0);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1); 
		$response = curl_exec( $ch );

		print $response;
	}

	public function sendsms($userkey,$passkey){
		if(($userkey!=null) and ($passkey!=null)){
			$user = urldecode(($userkey));
			$pass = urldecode(($passkey));
			if (md5($user)==$pass){
				$do = $this->input->post('do');
				$res = $this->switchsmsdo($do,'085728828648');
				$return = array(
					'status'=>$res,
					'valid'=>1,
					'message' => 'Sent'
					);
			} else {
				$return = array(
					'status'=>0,
					'valid'=>0,
					'error' => "Userkey/Passkey Invalid"
					);
			
			}
		} else {
			$return = array(
					'status'=>0,
					'valid'=>0,
					'error' => "Userkey/Passkey Can Not Be Null"
					);
		}
		
		echo json_encode($return);

	}

	public function switchsmsdo($do,$number){
		switch ($do) {
			case 'schemem':
				//send sms schedule reminder
				//$this->Msetting->getset('smsreminderschedule');
				$period = $this->Msetting->getset('period');
				$smscontent =$this->Mtmp->gettmpdata($this->Msetting->getset('smsreminderschedule'))->tmpcontent;
				
				$to = 'pamma.cyber@gmail.com';//$email;
				$ccmail=null;
				$bcfrom = "API";
				$sub = 'Regular Class '.$period.' - Test API';
				$attfile = null;
				
				if ((null!=$to) and (null!=$sub)){
					//====== decode message ============
					$decode = $this->convertcode->decodemailmsg($smscontent,$to);	
						
					//================= gmail send ===========
					$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);		
				}
				return $ret;
				break;
			case 'regist':
				//send sms registration success
				break;
			default:
				//send sms default
				break;
		}
	}

	
}
