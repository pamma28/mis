<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduletest extends Mem_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Msche','Msetting'));
    }

	public function index(){
		
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['jdwl_tes.idjdwl','tname','jdate','jsesi','jroom','jquota'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover" id="mylist">',
				        'heading_cell_start'    => '<th class="col-md-2">'
		 			);
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$perpage = $this->Msche->countpilsche();
		$perpagechoo = $this->Msche->countmysche();
		
		//============ data schedule and choosen ==============
		$mytemp = $this->Msche->datamysche(array('jdwl_mem.idjdwl'),$perpagechoo,1);
			$arrchoosen=array();
			foreach ($mytemp as $v) {
				$arrchoosen[]=$v['idjdwl'];
			}
		$temp = $this->Msche->datapilsche($column,$perpage,1);	
				foreach($temp as $key=>$value){
				//manipulation date data
				$temp[$key]['jdate']=date("D, d-M-Y",strtotime($temp[$key]['jdate']));
				$temp[$key]['jquota']='<span class="jquo">'.$temp[$key]['jquota'].'</span>';
				//manipulation menu
				$enc = $value['idjdwl'];
					if (in_array($enc, $arrchoosen)){
						$btn = '<span class="label label-default disabled" data-id="'.$enc.'"><i class="fa fa-minus"></i>  Choosen </span>' ;
					} else {
					$btn = ($value['jquota']>0) ? '<a href="#mysche" data-href="'.base_url('Member/Scheduletest/choosesche').'"  role="button" alt="Choose" class="btn btn-primary btn-xs btn-choose" title="Choose" data-id="'.$enc.'"><i class="fa fa-check"></i> Choose</a>' : '<span class="label label-default disabled"><i class="fa fa-minus"></i> No Quota </span> ';
					}
				$temp[$key]['menu'] = $btn;
				unset($temp[$key]['idjdwl']);
				}
		$data['listdata'] = $this->table->generate($temp);
		

		//=============== data my schedule ==========
		$column=['jdwl_tes.idjdwl','jmdate','tname','jdate','jsesi','jroom'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover" id="mysche">',
				        'heading_cell_start'    => '<th class="col-md-2">'
		 			);
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$choosen = $this->Msche->datamysche($column,0,1);
				foreach($choosen as $key=>$value){
				//manipulation date data
				$choosen[$key]['jmdate']=date("d-M-Y H:i",strtotime($choosen[$key]['jmdate']));
				$choosen[$key]['jdate']=date("D, d-M-Y",strtotime($choosen[$key]['jdate']));
				$choosen[$key]['tname']='<span class="idname">'.$choosen[$key]['tname'].'</span>';
				//manipulation menu
				$enc = $value['idjdwl'];
				unset($choosen[$key]['idjdwl']);
				$choosen[$key]['menu']= '<a data-id="'.$enc.'" title="Remove" class="btn btn-danger btn-xs btn-remove" alt="Remove" role="button" href="#mylist" data-href="'.base_url('Member/Scheduletest/deletesche').'"><i class="fa fa-times-circle"></i> Remove</a>';
				}
		$data['mysche'] = $this->table->generate($choosen);


		//===================== check phase date =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$schephase = explode(" - ",$this->Msetting->getset('schedulephase'));
		$startsche = strtotime(str_replace('/', '-', $schephase[0]));
		$endsche =  strtotime(str_replace('/', '-', $schephase[1]));
		$today = strtotime(date("d-m-Y"));
		$data['date'] = (($today >= $startsche) and ($today <= $endsche)) ? true : false;
		$data['startdate'] = date('d-M-Y',$startsche);
		$data['enddate'] = date('d-M-Y', $endsche);
		$data['ustatus'] = ($this->Mlogin->getuserstatus($this->session->userdata('user'))=='Completed Data') ? 1 : 0;

		//=============== Template ============
		$data['jsFiles'] = array(
							'');
		$data['cssFiles'] = array(
							'loading/loadingcircle');  
		// =============== view handler ============
		$data['title']="Choose Schedule";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/schedule/schelist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
		
		
	public function choosesche(){
		$this->load->model('Mpds');
		if ($this->input->post('id')!=null){
			//filter same test schedule
			$allmytest = array_column($this->Msche->datamysche('jdwl_tes.idtest',0,1,array('jdwl_mem.uuser'=>$this->session->userdata('user'))),'idtest');
			$willtes = $this->Msche->detailsche('jdwl_tes.idtest',$this->input->post('id'))[0]['idtest'];

			//check same test
			if(!in_array($willtes,$allmytest)){
				//filter 0 quota
				$quo = $this->Msche->detailsche('jquota',$this->input->post('id'))[0]['jquota'];
				//check 0 quota
				if ($quo==0){
					$r[] = false;
					$r[] = '<h4><span class="text-danger"><b>Error</b>, No more Quota available.</span></h4>';
					} else {
						$fdata = array (
								'jmdate' => date('Y-m-d H:i:s'),
								'idjdwl' => $this->input->post('id'),
								'uuser' => $this->session->userdata('user')
								);
						$t= $this->Msche->choosesche($fdata);
							if ($t==true){
							$r[]=true;
							$res ='<tr><td>'.date("d-M-Y H:i").'</td>';
							$coltest = ['tname','jdate','jsesi','jroom'];
							$arrtest = $this->Msche->detailsche($coltest,$fdata['idjdwl']);
							$arrtest[0][] = 'Menu';
								foreach ($arrtest[0] as $k=>$v) {
									$tmpres = '<td>'.$v.'</td>';
									($k == 'jdate') ? $tmpres = '<td>'.date('D d-M-Y', strtotime($v)).'</td>' : null;
									($k == 'Menu') ? $tmpres = '<td><a data-id="'.$fdata['idjdwl'].'" title="Remove" class="btn btn-danger btn-xs btn-remove" alt="Remove" role="button" href="#mylist" data-href="'.base_url('Member/Scheduletest/deletesche').'"><i class="fa fa-times-circle"></i> Remove</a></td>' : null;
									$res .= $tmpres;
								}
							$r[] = '<h4><span class="text-primary">Choose Schedule on <b>'.$arrtest[0]['tname'].'</b> Success</span></h4>';
							$r[] = $res.'</tr>';
							//update status member
							$this->Mpds->updatepds(array('ustatus'=>'Choosen Schedule'),$this->session->userdata('user'));
							} else {
								$r[] = false;
								$r[] = $this->db->_error_message();
								//update status member
								$this->Mpds->updatepds(array('ustatus'=>'Completed Payment'),$this->session->userdata('user'));
							}
					}
			} else {
				$coltest = ['tname'];
				$arrtest = $this->Msche->detailsche($coltest, $this->input->post('id'));
				$r[]= false;
				$r[]= '<h4><span class="text-danger"><b>Error</b>, No More Schedule on Same Test (<i>'.$arrtest[0]['tname'].'</i>).</span></h4>';
			}
		} else {
			$r[]=false;
			$r[]='<h4><span class="text-danger"><b>Error</b>, No Selected Schedule</span></h4>';
		}

		echo json_encode($r);
	}

	public function deletesche(){
		$this->load->model('Mpds');
		$id = $this->input->post('id');
		$r = $this->Msche->deletemysche($id);
		$ret[] =$r;
		if ($r){
			$ret[] = '<h4><span class="text-primary">Delete Schedule Success</span></h4>';
			//update status member
			$this->Mpds->updatestatus('Choosen Schedule',$this->session->userdata('user'),false);
			} else{
			$ret[] = '<h4><span class="text-danger">Delete Schedule Failed</span></h4>';
			}
			echo json_encode($ret);
	}
	
	public function printsche(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol=['jdate','tname','jsesi','jroom','jquota','jactive','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Msche->exportsche($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Msche->exportsche(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		// config table
		$header = $this->returncolomn($dtcol);
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
				foreach($dexp as $key=>$val){
					//manipulation allow data
					if(array_key_exists('jactive',$val)){
						if ($val['jactive']==1){
						$dexp[$key]['jactive']='Allowed';
						}else{
						$dexp[$key]['jactive']='Denied';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Schedule Test ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/mem/schedule/printsche', $data);
		
	}
	
	public function myschedule(){
		
		//=============== data my schedule ==========
		$schephase = explode(" - ",$this->Msetting->getset('schedulephase'));
		$startsche = strtotime(str_replace('/', '-', $schephase[0]));
		$endsche =  strtotime(str_replace('/', '-', $schephase[1]));
		$today = strtotime(date("d-m-Y"));

		//================ upcoming schedule ================
		$column=['jdwl_tes.idjdwl','jmdate','tname','jdate','jsesi','jroom'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		(($today<$endsche) and ($startsche<$today))? $header[]='Menu' : null;
		$tmpl = array ( 'table_open'  => '<table class="table table-hover" id="mysche">',
						'heading_cell_start'    => '<th width="18%">');
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$choosen = $this->Msche->datamysche($column,0,1);
				foreach($choosen as $key=>$value){
				//manipulation date data
				$choosen[$key]['jdate']=date("D, d-M-Y",strtotime($choosen[$key]['jdate']));
				$chosen[$key]['jmdate']=date("d-M-Y H:i",strtotime($choosen[$key]['jmdate']));
				$choosen[$key]['tname']='<span class="idname">'.$choosen[$key]['tname'].'</span>';
				//manipulation menu
				$enc = $value['idjdwl'];
				unset($choosen[$key]['idjdwl']);
				if (($today<$endsche) and ($startsche<$today)) {
				$choosen[$key]['menu']= '<div class="btn-group"><a href="#" data-href="'.base_url('Member/Scheduletest/deletesche?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';}
				}
		$data['mysche'] = $this->table->generate($choosen);

		//============= previous schedule ======================
		$columnp=['jdwl_tes.idjdwl','jmdate','tname','jdate','jsesi','jroom'];
		$headerp = $this->returncolomn($columnp);
		unset($headerp[0]);
		$tmplp = array ( 'table_open'  => '<table class="table table-hover" id="mysche">',
						'heading_cell_start'    => '<th width=20%>');
		$this->table->set_template($tmplp);
		$this->table->set_heading($headerp);
		$choosenp = $this->Msche->datamyprevsche($column,0,1);
				foreach($choosenp as $key=>$value){
				//manipulation date data
				$choosenp[$key]['jdate']=date("D, d-M-Y",strtotime($choosenp[$key]['jdate']));
				$chosenp[$key]['jmdate']=date("d-M-Y H:i",strtotime($choosenp[$key]['jmdate']));
				$choosenp[$key]['tname']='<span class="idname">'.$choosenp[$key]['tname'].'</span>';
				unset($choosenp[$key]['idjdwl']);
				}
		$data['myprevsche'] = $this->table->generate($choosenp);
		
		//=========== reminder test =========
		if($this->Msetting->checkexist('membersche_'.$this->session->userdata('user')) and ($this->Msetting->checkexist('memberscheactive_'.$this->session->userdata('user')))){
			$reminder= $this->Msetting->getset('membersche_'.$this->session->userdata('user'));
			$setactive= $this->Msetting->getset('memberscheactive_'.$this->session->userdata('user'));
		} else {
			$reminder = $this->Msetting->addsetting('membersche_'.$this->session->userdata('user'),'');
			$setactive = $this->Msetting->addsetting('memberscheactive_'.$this->session->userdata('user'),'0');
		}
		
		$data['btnreminder']=form_checkbox(array(
							'name'=>'fsetreminder',
							'id'=>'reminderonoff',
							'checked'=>$setactive,
							'value'=>'1')
							);
		$data['reminder'] = form_input(
							array(
								'id'=>'inputreminder',
								'name'=>'freminder',
								'class'=>'form-control',
								'type' => 'text',
								'required'=>'required',
								'value' => date("d-m-Y H:i:s",strtotime($reminder))
							));
		//========= check lunas status ========
		$this->load->model('Mlogin');
		$data['ustatus'] = ($this->Mlogin->getuserstatus($this->session->userdata('user'))=='Choosen Schedule') ? 1 : 0;
		//=============== Template ============
		$data['jsFiles'] = array(
							'moment/moment.min','daterange/daterangepicker','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'loading/loadingcircle','daterange/daterangepicker','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="My Schedule";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/mem/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/mem/schedule/mysche', $data, TRUE);
		$this->load->view ('template/main', $data);
		
	}
	
	public function savereminder(){
		$datereminder = $this->input->post("freminder");
		if(isset($datereminder)){
			$set = $this->input->post("fsetreminder");
			$setactive = ($set!="") ? "1" : "0";
			$startdate = date("Y-m-d H:i:s",strtotime($datereminder));
			$dt = array(
				"membersche_".$this->session->userdata('user')=>$startdate,
				"memberscheactive_".$this->session->userdata("user") => $setactive
				);
			$this->Msetting->savesetting($dt,$this->session->userdata('user'));

			//------------- create cronjob sms sender -------------
			$this->load->library('cronjob');
			$tags = array(
				'type'=>'schemem',
				"user" => $this->session->userdata('user')
			);

			if($setactive=='1'){

				$postdata = array(
						'do'=>'schemem',
						'user'=>$this->session->userdata('user')
						);
				$this->cronjob->deletecron($this->session->userdata('user'),$tags);
				$res = json_decode($this->cronjob->createcron($this->session->userdata('user'),$tags,$startdate,$postdata,'1','0minute'));
				$msg = ($res->status=='1') ? 'Turn ON Test Reminder Success' : "Turn ON Test Reminder Failed";
				$flash = ($res->status=='1') ? 'v' : 'x';
			} else {
				$res = json_decode($this->cronjob->deletecron($this->session->userdata('user'),$tags));
				$msg = ($res->status=='1') ? 'Turn OFF Test Reminder Success' : "Turn OFF Test Reminder Failed";
				$flash = ($res->status=='1') ? 'v' : 'x';
			}
				$this->session->set_flashdata($flash,$msg);
			redirect("Member/Scheduletest/myschedule");
		}
	}

	public function returncolomn($header) {
	$find=['idjdwl','jmdate','jdate','jstart','tname','jdwl_tes.idtest','jsesi','jroom','jquota','jactive','uname'];
	$replace = ['Schedule ID','<span class="fa fa-calendar"></span> Date Choosen','<span class="fa fa-calendar"></span> Schedule Date','Schedule Activated','<span class="fa fa-book"></span> Test Name','<span class="fa fa-book"></span> Test Name','<span class="fa fa-clock-o"></span> Session','<span class="fa fa-building"></span> Room','<span class="fa fa-info-circle"></span> Quota','Status','Last Updated by'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
