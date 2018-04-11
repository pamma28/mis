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
					$this->load->model('Mpds');
					$number = $this->Mpds->detailpds(array('uhp'),$user)[0]['uhp'];
				$to = $number;
				$sub = $title.' '.$period." - Test Reminder\n";
				if ((null!=$to) and (null!=$sub)){
					//====== decode message ============
					$decode = $this->convertcode->decodesmsmsg($to,$smscontent);	
						
					//================= gmail send ===========
					$ret = $this->sms->sendsms();		
				}
				return $ret;
				break;
			//-------------------------------------------------------------------
			case 'backupdb':
				//backup db periodically
				$dtcol = ['agenda','answer','article','broadcast','cat_artcle','certidesign','certificate','fac','jdwl_mem','jdwl_tes','jk','jns_trans','level','logstatus','notif','nread','question','ques_attach','qtype','quo_sbjct','resultqa','resulttest','role','setting','subject','template','test','transaksi','ttransfer','user'];
				$this->load->dbutil();
				$this->load->model('Msetting');
				$appname = $this->Msetting->getset('webtitle');
				$period = $this->Msetting->getset('period');
				// Backup your entire database and assign it to a variable
					$prefs = array(
							'tables'        => $dtcol,   												// Array of tables to backup.
							'ignore'        => array(),                     							// List of tables to omit from the backup
							'format'        => 'zip',                       							// gzip, zip, txt
							'filename'      => $appname.'.sql',     // File name - NEEDED ONLY WITH ZIP FILES
							'add_drop'      => TRUE,
							'add_insert'    => TRUE,
							'newline'       => "\n",
							'foreign_key_checks' => false 	
						);
				$backupdb = $this->dbutil->backup($prefs);

				// Load the file helper and write the file to your server
				$this->load->helper('file');
				$nowtime = date("d-m-Y_H:i:s");
				write_file(FCPATH.'temp_upload/backupdb_'.$nowtime.'.zip', $backupdb);
				$attfile = ['backupdb_'.$nowtime.'.zip'];


				//email handler 
				$to = $this->input->post('maildb');
				$ccmail=null;
				$bcfrom = $appname;
				$sub = $appname.' '.$period." - Database Backup";
				$idtmp = htmlspecialchars_decode($this->Msetting->getset('mailtemplate'));
				$mailcontent = '<h3 align="center">This is automated backup database sent to your email by our system</h3>' ;
				$rawtext = str_replace("{content_email}", $mailcontent, $idtmp);
				if ((null!=$to) and (null!=$sub)){
					//====== decode message ============
					$this->load->library('convertcode');
					$decode = $this->convertcode->decodemailmsg($rawtext,$to);	
						
					//================= gmail send ===========
					$ret = $this->gmail->sendmail($to,$ccmail,$sub,$bcfrom,$decode,$attfile);		
				}
				delete_files(FCPATH.'temp_upload/',true,true);
				return $ret;
				break;

				break;
			default:
				//send sms default
				break;
		}
	}

	
}
