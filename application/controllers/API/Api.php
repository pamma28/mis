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
		$user = 'org2';
		$tags = array(
			
			'type'=>'backupdb'
			);

		$postdata = array(
				'do'=>'backupdb',
				'user'=>'org'
				);
		
        $startdate = "2018-03-30 14:18:20";
		//$this->cronjob->createcron($user,$tags,$startdate,$postdata,'-1','1month');
		$this->cronjob->deletecron($user,$tags);

	}

	public function test(){
		
	}

	public function doing($userkey,$passkey){
		if(($userkey!=null) and ($passkey!=null)){
			$user = urldecode(($userkey));
			$pass = urldecode(($passkey));
			if (md5($user)==$pass){
				$do = $this->input->post('do');

				$res = $this->switchdo($do,'085728828648');
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

	public function switchdo($do,$user){
		switch ($do) {
			case 'schemem':
				//send sms schedule reminder
				$period = $this->Msetting->getset('period');
				$title = $this->Msetting->getset('webtitle');
				$smscontent =$this->Mtmp->gettmpdata($this->Msetting->getset('smsreminderschedule'))->tmpcontent;
				
				$to = 'pamma.cyber@gmail.com';//$email;
				$ccmail=null;
				$bcfrom = "API";
				$sub = $title.' '.$period." - Test Reminder\n";
				$attfile = null;
				
				if ((null!=$to) and (null!=$sub)){
					//====== decode message ============
					$decode = $this->convertcode->decodemailmsg($smscontent,$to);	
						
					//================= gmail send ===========
					$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);		
				}
				return $ret;
				break;
			case 'backupdb':
				//backup db periodically
				print('asd');
				break;
			default:
				//send sms default
				break;
		}
	}

	
}
