<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mtransfer','Msetting','Mpay'));
    }

	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idttrans','ttdaterequest','a.uname as mname','a.unim','ttbank','ttname','ttamount','ttapprove','b.uname as rname'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		// checkbox checkalldata
				$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'form-class',
							'value'=>'all',
							'id'=>'c_all'
							));	
				array_unshift($header,$checkall);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 10;
		$perpage= isset($tempfilter['page']) ? $tempfilter['page'] : 1;
		
		if ($durl!=null){
			unset($filter['view']);
			unset($filter['page']);
			$filter= array_filter($filter, function($filter) 
										{return ($filter !== null && $filter !== false && $filter !== '');
							});
			//implode query address
			$addrpage= http_build_query($filter);
			$addrpage = empty($addrpage)? null:$addrpage.'&';
			if ((array_key_exists('column',$filter)) and  (array_key_exists('search',$filter))){
				$vc = $filter['column'];
				$vq = $filter['search'];
				unset($filter['column']);
				unset($filter['search']);
				$filter[$vc]=$vq;
				$data['d']='';
				}
			else if ((empty($filter['view'])) and (!empty($filter))){
			$data['d']='d';
			}
			//count rows of data (with filter/search)
			$rows = $this->Mtransfer->counttransfer($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mtransfer->counttransfer();	
		}
		//================ filter handler ================
		$fq = array('name'=>'search',
						'id'=>'search',
						'required'=>'required',
						'placeholder'=>'Search Here',
						'value'=> isset($tempfilter['search']) ? $tempfilter['search'] : null ,
						'class'=>'form-control');
		$data['inq'] = form_input($fq);
			$optf = array(
						'ttdate' => 'Date Requested',
						'uname' => 'Full Name',
						'unim' => 'NIM',
						'ttbank' => 'Bank Name',
						'ttname' => 'Account Name',
						'ttnorek' => 'Account Number',
						'ttapprove' => 'Approve/Rejected(1/0)'
						);
		$fc = array('name'=>'column',
						'id'=>'col',
						'class'=>'form-control'
					);
		$data['inc'] = form_dropdown($fc,$optf,isset($tempfilter['column']) ? $tempfilter['column'] : null);
		$data['inv'] = form_hidden('view',isset($tempfilter['view']) ? $tempfilter['view'] : 10);
		
		$fbq = array(	'id'=>'bsearch',
						'value'=>'search',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['bq'] = form_submit($fbq);
		
		// ============= advanced filter ===============
		$adv['Period'] = form_input(
						array('name'=>'period',
						'id'=>'period',
						'placeholder'=>'Period',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		$adv['Date Requested'] = form_input(
						array('name'=>'ttdate',
						'id'=>'createdon',
						'placeholder'=>'Date Requested',
						'value'=>isset($tempfilter['ttdate']) ? $tempfilter['ttdate'] : null,
						'class'=>'form-control'));
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'fullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['NIM'] = form_input(
						array('name'=>'unim',
						'placeholder'=>'NIM',
						'value'=>isset($tempfilter['unim']) ? $tempfilter['unim'] : null,
						'class'=>'form-control'));
						
		$adv['Bank Name'] = form_input(
						array('name'=>'ttbank',
						'id'=>'bname',
						'placeholder'=>'Bank Account',
						'value'=>isset($tempfilter['ttbank']) ? $tempfilter['ttbank'] : null,
						'class'=>'form-control'));
		
		$adv['Account Name'] = form_input(
						array('name'=>'ttname',
						'id'=>'bacc',
						'placeholder'=>'Account Name',
						'value'=>isset($tempfilter['ttname']) ? $tempfilter['ttname'] : null,
						'class'=>'form-control'));
		
		$adv['Account Number'] = form_input(
						array('name'=>'ttnorek',
						'id'=>'bno',
						'placeholder'=>'Account Number',
						'value'=>isset($tempfilter['ttnorek']) ? $tempfilter['ttnorek'] : null,
						'class'=>'form-control'));
		
		$adv['Status'] = form_dropdown(array(
							'name'=>'ttapprove',
							'id'=>'status',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Approved',
							'2'=>'Rejected'),isset($tempfilter['ttapprove']) ? $tempfilter['ttapprove'] : null);
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Transfer?'.$addrpage.'view='.$offset,
				'total_rows' => $rows,
				'per_page' => $offset,
				'use_page_numbers' => true,
				'page_query_string' =>true,
				'query_string_segment' =>'page',
				'num_links' => 3,
				'cur_tag_open' => '<span class="disabled"><a href="#">',
				'cur_tag_close' => '<span class="sr-only"></span></a></span>',
				'next_link' => 'Next',
				'prev_link' => 'Prev'
				);
		$data["urlperpage"] = base_url().'Organizer/Transfer?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mtransfer->datatransfer($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				if($value['ttapprove']=='1'){
					$temp[$key]['ttapprove']='<span class="label label-success">Approved</span>';
				} else if($value['ttapprove']=='0'){
					$temp[$key]['ttapprove']='<span class="label label-danger">Rejected</span>';
				} else{
					$temp[$key]['ttapprove']='<span class="label label-warning">Pending</span>';
				}
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idttrans']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['mname']='<span class="idname">'.$temp[$key]['mname'].'</span>';
				$temp[$key]['ttdaterequest']=date('d-M-Y',strtotime($temp[$key]['ttdaterequest'])).'<br/>'.date('H:i:s',strtotime($temp[$key]['ttdaterequest']));
				$temp[$key]['ttamount']=$this->convertmoney->convert($temp[$key]['ttamount']);
				//manipulation menu
				$enc = $value['idttrans'];
				unset($temp[$key]['idttrans']);
				$temp[$key]['menu']='<small><a href="'.base_url('Organizer/Transfer/detailtransfer?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a>  '.
				'<a href="'.base_url('Organizer/Transfer/confirmtransfer?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" data-confirm="1" role="button" alt="Confrim Data" class="btn-info btn-sm"><i class="fa fa-check-square-o"></i> Approve</a> '.
				'<a href="'.base_url('Organizer/Transfer/confirmtransfer?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" data-confirm="0" role="button" alt="Reject Data" class="btn-danger btn-sm"><i class="fa fa-window-close"></i> Reject</a></small>';
				}
		$data['listdata'] = $this->table->generate($temp);
		
		// ======== activate/deactivate account ==============
			$data['idac']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedid',
								'name'=>'fdatas'
								));
			$data['idtype']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedtype',
								'name'=>'ftype'
								));
			$data['factselected'] = site_url('Organizer/Transfer/updateselected');
		
		//=============== setting registration phase ============
			$payment = $this->Msetting->getset('paymentphase');
			$data['fpay']= form_input(array('id'=>'payrange',
								'class'=>'form-control',							
								'name'=>'fpayphase',							
								'placeholder'=>'Payment Phase',							
								'value'=>$payment,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Transfer/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'toggle/bootstrap2-toggle.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'toggle/bootstrap2-toggle.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/transfer/transferlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	
	public function detailtransfer(){
		//fecth data from db
		$col=['ttdaterequest','ttdate','ttdateapp','tnotrans','a.uname as mname','a.unim','ttbank','ttname','ttnorek','ttamount','a.upaycode','ttapprove','b.uname as rname','ttket'];
		$id = $this->input->get('id');
		$dbres = $this->Mtransfer->detailtransfer($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		$col[4]='mname';
		$col[5]='unim';
		$col[10]='upaycode';
		$col[12]='rname';
		//set table template
		$tmpl = array ( 'table_open'  => '<table class="table table-striped">',
					'heading_row_start'   => '<tr>',
                    'heading_row_end'     => '</tr>',
                    'heading_cell_start'  => '<td>',
                    'heading_cell_end'    => '</td>',

                    'row_start'           => '<tr>',
                    'row_end'             => '</tr>',
                    'cell_start'          => '<td>',
                    'cell_end'            => '</td>'

					);
		$this->table->set_template($tmpl);
		//set table data
		$a = 0;
		$stat = ($dbres[0]['ttapprove']=='1') ? '<span class="label label-success">Approved</span>' : (($dbres[0]['ttapprove']=='0') ? '<span class="label label-danger">Rejected</span>' : '<span class="label label-warning">Pending</span>');
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			
			if ($key=='Date Requested'){
						$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y, H:i',strtotime($dbres[0][$col[$a]]))
						);
				}

			if ($key=='Date Processed'){
						$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y, H:i',strtotime($dbres[0][$col[$a]]))
						);
				}
			if ($key=='Date Transfered'){
						$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y',strtotime($dbres[0][$col[$a]]))
						);
				}
			if ($key=='Status'){
						$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.$stat
						);
				}
			if (($key=='Amount')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <b>'.$this->convertmoney->convert($dbres[0][$col[$a]]).'</b>'
						);
					}
			
			if (($key=='Invoice No')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <a href="'.base_url("Organizer/Payment?tnotrans=".$dbres[0][$col[$a]]).'">'.$dbres[0][$col[$a]].'</a>'
						);
					}
			
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/transfer/detailtransfer', $data);
		
		
	}
		
	public function confirmtransfer(){					
		// ============== Fetch data ============
		$col=['ttdate','a.uname as mname','a.uuser','ttbank','ttname','ttnorek','ttamount','a.upaycode','ttapprove','ttket'];
		$id = $this->input->get('id');
		$g = $this->Mtransfer->detailtransfer($col,$id);
		$stat = ($g[0]['ttapprove']) ? true: false;
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.$g[0]['ttdate'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['mname'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['uuser'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['ttbank'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['ttname'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['ttnorek'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['ttamount'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['upaycode'].'</label>';
		$r[] = form_checkbox(array(
							'name'=>'fapprove',
							'data-toggle'=>'toggle',
							'data-on'=>'<h4>Approve</h4>',
							'data-off'=>'<h4>Reject</h4>',
							'data-size'=>'big',
							'id'=>'idconfirm',
							'checked'=>$stat,
							'value'=>'1')
							);
		
		$fket = array('name'=>'fket',
						'id'=>'ket',
						'placeholder'=>'Notes',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($fket,$g[0]['ttket']);
		
		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Confirm',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($col);
		//set table template
		$tmpl = array ( 'table_open'  => '',
					'heading_row_start'   => '',
                    'heading_row_end'     => '',
                    'heading_cell_start'  => '',
                    'heading_cell_end'    => '',

                    'row_start'           => '',
                    'row_end'             => '',
                    'cell_start'          => '',
                    'cell_end'            => '',
					'table_close'         => ''

					);
		$this->table->set_template($tmpl);
		//=========== generate edit form =========================
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r[$a].'</div></div>'
					);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		$this->load->view('dashboard/org/transfer/confirmtransfer', $data);
	
	
	}
	
	public function updatetransfer(){
		if ($this->input->post('fid')!=null){
		$id = $this->input->post('fid');
		$cnf = (null!=$this->input->post('fapprove'))? $this->input->post('fapprove'): 0;
			//====== invoice variable ========
			$idpay = $this->Mtransfer->getidtransbyid($id);
			$muser = $this->Mtransfer->getmemuser($id);
			if ($cnf==1){
				//======= if approved, no invoice made. it will be made =======
				$col = ['a.uuser','ttamount'];
				$dttrans = $this->Mtransfer->detailtransfer($col,$id);
				if ($idpay==null){
					$no = $this->generatenotrans($muser);
					$vto = new DateTime('+6 months');
						$dtpay = array(
								'tnotrans'=>$no,
								'tdate' => date("Y-m-d H:i:s"),
								'idjnstrans' =>3,					
								'uuser' => $muser,
								'use_uuser' => $this->session->userdata('user'),
								'tnomi' =>floor($dttrans[0]['ttamount']/1000)*1000,
								'tpaid' => floor($dttrans[0]['ttamount']/1000)*1000,
								'tchange' => '0',
								'valid_to' => $vto->format("Y-m-d H:i:s")
								);
						$this->load->model('Mpay');
						$this->qr($no);
						$rpay = $this->Mpay->savepayment($dtpay);
						if($rpay){
							$msg[] = 'Invoice No <a href="'.base_url("Organizer/Payment?tnotrans=".$no).'">'.$no.'</a> Made';
							//=============== sent notif ==========
							$idnotifmem = $this->Msetting->getset('notifpayment');
							$this->notifications->pushnotif(array('idnotif'=>$idnotifmem,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$muser,'nlink'=>base_url('Member/Confirmpay/payment')));
						} else {
							$msg[] = 'Make Invoice failed';
						}
						$idtrans = $this->Mtransfer->getidtransbyno($no);
						
						$fdata = array (
								'ttket' => $this->input->post('fket'),					
								'ttapprove' => $cnf,					
								'idtrans' => $idtrans,					
								'use_uuser' => $this->session->userdata('user'),					
								'ttdateapp' => date("Y-m-d H:i:s")
								);
					} else {
					$fdata = array (
								'ttket' => $this->input->post('fket'),					
								'ttapprove' => $cnf,
								'use_uuser' => $this->session->userdata('user'),								
								'ttdateapp' => date("Y-m-d H:i:s")
								);
					}
				//update lunas
				$this->checklunas($dttrans[0]['uuser']);
				$idnotif = $this->Msetting->getset('notifpayconfirmsuccess');
				} else {
					//====== check invoice made or not yet ========
					$idpay = $this->Mtransfer->getidtransbyid($id);
						//======== if ever made, delete!=====
						if ($idpay != null){
						// delete qr and payment record
						$this->load->model('Mpay');
						$this->deleteqr($idpay);
						$no = $this->Mpay->getnotrans($idpay);
						$rpay = $this->Mpay->deletepay($idpay);
						$rpay ? ($msg[] = 'Invoice No '.$no.' Deleted'): ($msg[] = 'Delete Invoice failed');
						}
					// update lunas status
					$us = $this->Mpay->getusertrans($idpay);
					$this->checklunas($us);
					$fdata = array (
								'ttket' => $this->input->post('fket'),					
								'ttapprove' => $cnf,				
								'idtrans' => null,					
								'use_uuser' => $this->session->userdata('user'),				
								'ttdateapp' => date("Y-m-d H:i:s")
								);
				$idnotif = $this->Msetting->getset('notifpayconfirmfailed');
				}
					
		$r = $this->Mtransfer->updatetransfer($fdata,$id);
		}
		if ($r){
		//============ sent notif ==============
		$this->notifications->pushnotif(array('idnotif'=>$idnotif,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$muser,'nlink'=>base_url('Member/Confirmpay/validationresult')));
		$msg[]='Update Transfer Confirmation Success';
		$this->session->set_flashdata('v',implode(', ',$msg));
		} else {		
		$this->session->set_flashdata('x','Update Transfer Confirmation Failed');
		}
		redirect(base_url('Organizer/Transfer'));
	}
	
	public function updateselected(){
		if($this->input->post('fdatas')!='')
		{
		$users = $this->input->post('fdatas');
		$type = $this->input->post('ftype');
		$dtid= explode(',',$users);
		$succ=0;$fail=0;
		// loop each user
			foreach ($dtid as $id)
			{
			$idpay = $this->Mtransfer->getidtransbyid($id);
			$muser = $this->Mtransfer->getmemuser($id);
					if ($type==1){
					//if approve all
					$col = ['ttamount'];
					$dttrans = $this->Mtransfer->detailtransfer($col,$id);
						if ($idpay==null){
						//======= if no invoice made. it will be made =======
							$no = $this->generatenotrans($muser);
							$vto = new DateTime('+6 months');
							$dtpay = array(
									'tnotrans'=>$no,
									'tdate' => date("Y-m-d H:i:s"),
									'idjnstrans' =>3,					
									'uuser' => $muser,
									'use_uuser' => $this->session->userdata('user'),
									'tnomi' =>floor($dttrans[0]['ttamount']/1000)*1000,
									'tpaid' => floor($dttrans[0]['ttamount']/1000)*1000,
									'tchange' => '0',
									'valid_to' => $vto->format("Y-m-d H:i:s")
									);
							$this->load->model('Mpay');
							$this->qr($no);
							$rpay = $this->Mpay->savepayment($dtpay);
							
							// catch variable to store in 'transfer'
							$idtrans = $this->Mtransfer->getidtransbyno($no);
							$fdata = array (					
									'ttapprove' => $type,					
									'idtrans' => $idtrans,					
									'use_uuser' => $this->session->userdata('user'),					
									'ttdateapp' => date("Y-m-d H:i:s")
									);
						} else {
							$fdata = array (					
								'ttapprove' => $type,
								'use_uuser' => $this->session->userdata('user'),								
								'ttdateapp' => date("Y-m-d H:i:s")
								);
							}
							
					} else {
					//====== check invoice made or not yet ========
					$idpay = $this->Mtransfer->getidtransbyid($id);
					//======== if ever made, delete!=====
							if ($idpay != null){
							// delete qr and payment record
							$this->deleteqr($idpay);
							$this->load->model('Mpay');
							$no = $this->Mpay->getnotrans($idpay);
							$this->Mpay->deletepay($idpay);
							}
					$fdata = array (					
							'ttapprove' => $type,				
							'idtrans' => null,					
							'use_uuser' => $this->session->userdata('user'),				
							'ttdateapp' => date("Y-m-d H:i:s")
							);
					}	
			// update transfer & lunas status
			$res = $this->Mtransfer->updatetransfer($fdata,$id);
			$this->checklunas($muser);
			if ($res){$succ++;$msg[] = $no.' ('.$muser.')';}else{$fail++;}
			}
		$totuser = count($dtid);
		$ms = implode('<br/>',$msg);
		$this->session->set_flashdata('v','Update '.$totuser.' Selected Transfer Payment success.<br/>Details: '.$succ.' success and '.$fail.' error(s)<br/>'.$ms);
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Transfer Payment Failed.');
		}
		redirect(base_url('Organizer/Transfer'));
	}
		
	public function savesetting(){
		if(null!= $this->input->post('fpayphase')){
			$dtrange = $this->input->post('fpayphase');
		$dtset=array(
				'paymentphase'=>$dtrange
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Payment Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Payment Phase Failed.");
		}
		redirect(base_url('Organizer/Transfer'));
	}
	
	public function checklunas($user=null){
		$this->load->model('Mpay');
		$price = $this->Msetting->getset('price');
		if ($user!=null){
		$r = $this->Mpay->checkfullpaid($user);
			if ($r>=$price){
			$h = $this->Mpay->updatefullpaid($user,1);
			} else {
			$h = $this->Mpay->updatefullpaid($user,0);
			}
		} else{
			$alluser = $this->Mpay->getalluser();
			$tot=count($alluser);
			foreach($alluser as $k=>$val){
				$r = $this->Mpay->checkfullpaid($val['uuser']);
				if ($r>=$price){
				$h = $this->Mpay->updatefullpaid($val['uuser'],1);
				} else {
				$h = $this->Mpay->updatefullpaid($val['uuser'],0);
				}
			}
			$this->session->set_flashdata('v',"Update Full Paid status of ".$tot." User(s) success");
			$this->index();
			$this->session->set_flashdata('v',null);
		
		}
	}
	
	public function generatenotrans($id){
		$this->load->model('Mpay');
		$n = rand(000,999);
		$r = $this->Mpay->getpaycode($id);
		$no = date('ymd').mb_substr($r,3,3).$n;
		$c = $this->Mpay->checknotrans($no);
		 if($c>=1) {
		 $this->generatenotrans($id);}
		 else{ return $no;}
	}
	
	public function qr($text=null){
		$this->load->library('ciqrcode');
		$params['data'] = $text;
		$params['level'] = 'H';
		$params['size'] = 10;
		$params['savename'] = FCPATH.'/upload/qr/'.$text.'.png';
		$this->ciqrcode->generate($params);
	}
	
	public function deleteqr($id){
		$this->load->model('Mpay');
		$notrans = $this->Mpay->getnotrans($id);
		$path = FCPATH.'upload/qr/' . $notrans.'.png';
        unlink($path);
	}
	
	public function returncolomn($header) {
	$find=['idttrans','ttdaterequest','ttdateapp','tnotrans','ttdate','a.uname as mname','a.uuser','a.unim','ttbank','ttname','ttnorek','ttamount','a.upaycode','ttapprove','b.uname as rname','ttket'];
	$replace = ['Transfer ID','Date Requested','Date Processed','Invoice No','Date Transfered','Full Name','Username','NIM','Bank Account','Account Name','Account Number','Amount','Payment Code','Status','PIC','Notes'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}	
}
