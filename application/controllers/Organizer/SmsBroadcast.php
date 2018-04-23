<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SmsBroadcast extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertroman','Sms'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mbc','Msetting'));
    }

	public function index(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idbcast','bcdate','bctitle','bcrecipient','bccontent','uname','bcket'];
		$header = $this->returncolomn($column);
		$header[0]='';
		//checkbox checkalldata
		$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'btn btn-default btn-sm',
							'value'=>'all',
							'id'=>'c_all'
							));	
			$data['btncheckall']= $checkall;
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
			
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 10;
		$perpage= isset($tempfilter['page']) ? $tempfilter['page'] : 1;
		unset($filter['code']);
		
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
			$rows = $this->Mbc->countbcsms($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mbc->countbcsms();	
		}
		//================ filter handler ================
		$fq = array('name'=>'search',
						'id'=>'search',
						'required'=>'required',
						'placeholder'=>'Search Here',
						'value'=> isset($tempfilter['search']) ? $tempfilter['search'] : null ,
						'class'=>'form-control input-xs');
		$data['inq'] = form_input($fq);
			$optf = array(
						'bcdate' => 'SMS Created',
						'bctitle' => 'Title SMS',
						'bcrecipient' => 'SMS Recipient',
						'bcmailfrom' => 'SMS Sender',
						'bccontent' => 'SMS Content',
						'uname' => 'PIC'
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
		$adv['SMS Created'] = form_input(
						array('name'=>'bcdate',
						'id'=>'createdon',
						'placeholder'=>'SMS Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['bcdate']) ? $tempfilter['bcdate'] : null,
						'class'=>'form-control'));
		
		$adv['SMS Title'] = form_input(
						array('name'=>'bctitle',
						'id'=>'bctitle',
						'placeholder'=>'SMS Title',
						'value'=>isset($tempfilter['bctitle']) ? $tempfilter['bctitle'] : null,
						'class'=>'form-control'));
						
		$adv['SMS Recipient'] = form_input(
						array('name'=>'bcrecipient',
						'id'=>'bcreci',
						'placeholder'=>'SMS Recipient',
						'value'=>isset($tempfilter['bcrecipient']) ? $tempfilter['bcrecipient'] : null,
						'class'=>'form-control'));
						
		$adv['SMS Sender'] = form_input(
						array('name'=>'bcmailfrom',
						'id'=>'bcmailfrom',
						'placeholder'=>'SMS Sender',
						'value'=>isset($tempfilter['bcmailfrom']) ? $tempfilter['bcmailfrom'] : null,
						'class'=>'form-control'));
		
		$adv['SMS Content'] = form_input(
						array('name'=>'bccontent',
						'id'=>'bccontent',
						'placeholder'=>'SMS Content',
						'size'=>'45',
						'value'=>isset($tempfilter['bccontent']) ? $tempfilter['bccontent'] : null,
						'class'=>'form-control'));
		
		$adv['PIC'] = form_input(
						array('name'=>'uname',
						'id'=>'picname',
						'placeholder'=>'PIC',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['Type'] = form_dropdown(
						array('name'=>'smstype',
						'id'=>'smstype',
						'class'=>'form-control'),array(''=>'Please Select','Single SMS'=>'Single SMS','Broadcast SMS'=>'Broadcast'),isset($tempfilter['smstype']) ? $tempfilter['smstype'] : null);
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/SmsBroadcast/?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/SmsBroadcast?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Mbc->databcsms($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				(strpos($value['bcket'], 'Broadcast SMS') !== false) ? $lbl = '<i class="fa fa-users text-primary"></i>' : $lbl ='<i class="fa fa-user text-yellow"></i>';
				$ctable = $lbl.'<br/>'.form_checkbox(array(
							'name'=>'check[]',
							'class'=>'checkbox',
							'value'=>$temp[$key]['idbcast']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text sms
					$txt = $value['bccontent'];
					(strlen($txt)>30) ? $tmptext = mb_substr($txt,0,30).'...':$tmptext=$txt;
				$arrket = explode('; ',$value['bcket']);
				$arrdetail = array();	
					foreach($arrket as $v){
						$arrtemp = explode(': ',$v);
						$arrdetail[$arrtemp[0]] =$arrtemp[1] ;
					}
				
				$temp[$key]['bcrecipient']=str_replace(',','<br/>',$value['bcrecipient']);
				$temp[$key]['bccontent']='<span class="uname hidden">'.$value['bctitle'].'</span>'.$tmptext;
				$temp[$key]['bcdate']=date('d-M-Y', strtotime($value['bcdate'])).'<br/>'.date('H:i:s', strtotime($value['bcdate'])).'<br/>('.$this->converttime->time_elapsed_string(date('d-M-Y H:i:s', strtotime($value['bcdate']))).')';
				$temp[$key]['bcket']='<small>'.$arrdetail['Status'].'<br/>Failed ('.$arrdetail['Failed List'].')</small>';
				
				//manipulation menu
				$enc = $value['idbcast'];
				$temp[$key]['bctitle']='<a href="'.base_url('Organizer/SmsBroadcast/readsms?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read SMS"><b>'.$value['bctitle'].'</b></a>';
				unset($temp[$key]['idbcast']);
				$temp[$key]['menu'] = '<div class="btn-group-vertical">
										<a href="'.base_url('Organizer/SmsBroadcast/composesms?id=').$enc.'" alt="Forward Data" class="btn btn-primary btn-xs" title="Forward"><i class="fa fa-share"></i> </a>
										<a href="#" data-href="'.base_url('Organizer/SmsBroadcast/deletesms?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i> </a>
										</div>';
				
				}
		
		$data['listdata'] = $this->table->generate($temp);
		
		// ======== Delete multiple ==============
			$data['idac']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedid',
								'name'=>'fusers'
								));
			$data['idtype']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedtype',
								'name'=>'ftype'
								));
			$data['factselected'] = site_url('Organizer/SmsBroadcast/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/SmsBroadcast/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'bcdate' => 'SMS Created',
						'bctitle' => 'SMS Title',
						'bcrecipient' => 'SMS Recipient',
						'bcmailfrom' => 'SMS Sender',
						'bcfrom' => 'Pre Text',
						'bccontent' => 'SMS Content',
						'bcket' => 'SMS Details',
						'uname' => 'PIC'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol input-xs',
							'multiple'=>'multiple',
							'required'=>'required'),$optcol);
			$data['fcheckcol']= form_input(array(
							'name'=>'fcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm selectall',
							'value'=>'Select all columns'
							));
			$data['funcheckcol']= form_input(array(
							'name'=>'funcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm unselectall',
							'value'=>'Unselect all columns'
							));
			$data['fusedate']= form_checkbox(array(
							'name'=>'fusedate',
							'id'=>'usedate',
							'value'=>'use'
							),false);
			$data['fdtrange']= form_input(array(
							'name'=>'fdtrange',
							'type'=>'text',
							'class'=>'form-control frange',
							'id'=>'rangedate'
							));
			$data['fbtnexport']= form_submit(array('value'=>'Export',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factexp'] = site_url('Organizer/SmsBroadcast/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/SmsBroadcast/printbcsms');
		
		//=============== setting ============
			$smsfoo = $this->Msetting->getset('smsfooter');
			
			$data['fooeditor']= form_textarea(array(
							'name'=>'feditfoo',
							'id'=>'editfoo',
							'cols'=>'148',
							'rows'=>'2',
							'class'=>'form-control',
							'value'=>$smsfoo)
							);
			
			$data['fbtnupdate']= form_submit(array('value'=>'Update Footer',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/SmsBroadcast/savesetting');
				
			
			$data['totmail'] = $this->Mbc->countbcsms();
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="SMS Broadcast";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/bcsms/bcsmslist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function sendsms(){
	$to = $this->input->post('flistto');
	$arrusersto = explode(',',$this->input->post('fusersto'));	
	
	$title = $this->input->post('msub');
	$redi = $this->input->post('fredi');
	$this->load->library('sms');
	 if ((null!=$to) and (null!=$title)){
		//specify broadcast type
		$target='';$year='';$lvl='';$stat='';
		if ($this->input->post('mbc')==1) {
			$bctype="Broadcast SMS";
			if ($this->input->post('optrole')==''){
				$target='All User';
				} else {
				$target=$this->Mbc->getrole($this->input->post('optrole'));
				}
			if ($this->input->post('optyear')==''){
				$year = "All Year";
				} else {
				$year = $this->input->post('optyear');
				}
			if ($this->input->post('optlvl')==''){
				$lvl= "All Level";
				} else {
				$lvl = $this->Mbc->getlevel($this->input->post('optlvl'));
				}
			if ($this->input->post('optlunas')==''){
				$stat= "All Status";
				} else {
				($this->input->post('optlvl')=='1') ? $stat = 'Fully Paid': $stat= 'Not Fully Paid' ;
				}
			} else {
			$bctype="Single SMS";
			}
		
		$smscode = $this->input->post('mtext');
		($this->input->post('malias')!='') ? $smscode = strtoupper($this->input->post('malias')).'-'.$this->input->post('mtext'): null;
		($this->input->post('fusefoo')=='1') ? $smscode .= "\n".$this->Msetting->getset('smsfooter') : null;
		
		$arrto = explode(',',$to);
		$tot = 0;
		$failed = array();
		$this->load->library('Convertcode');
		$idnotifnewsms = $this->Msetting->getset('notifbcsms');
		foreach($arrto as $k=>$v){
			$decode = $this->convertcode->decodesmsmsg($smscode,$v);	
			//================= send sms ===========
			$ret = $this->sms->sendsms($v,$decode);
			
			($ret) ? $tot++:$failed[]=$v;

			//======= set notif new sms===========
			($ret) ? $this->notifications->pushnotif(array('idnotif'=>$idnotifnewsms,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$arrusersto[$k],'nlink'=>null)):null;
		}
		
		
		($this->input->post('fusefoo')!='') ? $usefoo = true : $usefoo = false; 
		
		$fdata = array (
				'bcdate' => date("Y-m-d H:i:s"),
				'bcrecipient' => $to,
				'uuser' => $this->session->userdata('user'),
				'bcfrom' => strtoupper($this->input->post('malias')),
				'bcmailfrom' => $this->Msetting->getset('nosmsnotif'),
				'bctitle' => $title,
				'bccontent' => $this->input->post('mtext'),
				'bcfooter' => $usefoo ,
				'bcket' => 'Type: '.$bctype.'; Role: '.$target.'; Period: '.$year.'; Level: '.$lvl.'; Payment: '.$stat.'; Status: Success '.$tot.', Failed '.count($failed).'; Failed List: '.implode($failed,','),
				'bctype' => 'SMS'
				);
		$r = $this->Mbc->savebc($fdata);

		//======= set notif ===========
		$idnotif = $this->Msetting->getset('notifbcsmsby');
		($r) ? $this->notifications->pushNotifToOrg(array('idnotif'=>$idnotif,'uuser'=>$this->session->userdata('user'),'nlink'=>base_url('Organizer/SMSbroadcast'))):null;
		
			if ($tot>0){
				//make report
				$detailreport = $tot.' Success, '.count($failed).' Failed';
				$allfailed = implode($failed,', ');
				(count($failed)>0) ? $detailreport .= ' ('.$allfailed.')':null;
				
				$this->session->set_flashdata('v','Send SMS Success. Details: '.$detailreport);
			} else {		
				$this->session->set_flashdata('x','Send SMS Failed');
			}
				(null==$redi) ? redirect(base_url('Organizer/SmsBroadcast')) : redirect(base_url('Organizer/SmsBroadcast/composesms'));
		} else {
			$this->session->set_flashdata('x','Send SMS Failed');
			redirect(base_url('Organizer/SmsBroadcast/composesms'));
		}
		
		
	}

	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatSmsBroadcast.xls') ){
                // if file validation failed, send error to view
                $error = ['file choosen is not match with pre-defined file',$this->upload->display_errors()];
				array_filter($error);
				$this->session->set_flashdata('x','Import Data Failed, details: '.implode(' ',$error));
            } else {
              // if upload success, take file data
              $upload_data = $this->upload->data();
			 
              // load library Excell_Reader
              $this->load->library('Excel');
			  $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);
			  $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
 
              // array data
			  $highestRow = $objWorksheet->getHighestRow();
			  $highestColumn = $objWorksheet->getHighestColumn();
              $dtxl = Array();
			  
			  //variable
			  $creditnorep = $this->Msetting->getset('saldosmsnotif');
			  $creditwithrep = $this->Msetting->getset('saldosmsbc');
			  $footer = $this->Msetting->getset('smsfooter');
			  
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
				//determine some variables
				$to = $objWorksheet->getCell('A'.($i+1))->getValue();
				(strpos($to, ',') !== false) ? $bctype="Broadcast SMS":$bctype="Single SMS";
				($objWorksheet->getCell('F'.($i+1))->getValue() == 'Y') ? $usefoo = true : $usefoo = false;
				if ($objWorksheet->getCell('E'.($i+1))->getValue() == 'Y') 
					{
						$userep = true; 
						$repstat = "Yes";
					} else {
						$userep = false;
						$repstat = "No";
					}
					
				//sms content
				$smscode = $objWorksheet->getCell('D'.($i+1))->getValue();
				($objWorksheet->getCell('C'.($i+1))->getValue()!='') ? $smscode = strtoupper($objWorksheet->getCell('C'.($i+1))->getValue()).'-'.$objWorksheet->getCell('D'.($i+1))->getValue(): null;
				($usefoo) ? $smscode .= "~".$footer : null;
				
				 $arrto = explode(',',$to);
				 $tot = 0; $failed = array();
				 $this->load->library('Convertcode');
				 foreach($arrto as $k=>$v){
					$val = preg_replace("/[^0-9]/", "", $v);
					$decode = $this->convertcode->decodesmsmsg($smscode,$val);	
					//================= send sms ===========
					//$ret = $this->sms->sendsms($val,$decode,$userep);
					($ret) ? $tot++ : $failed[]=$v;
					}
					
				(($usefoo) and ($tot>0)) ? $creditwithrep = $creditwithrep - (40*$tot) : $creditnorep = $creditnorep - (20*$tot);
                
				   $dtxl[$i-1]['bcdate'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['bcrecipient'] = $to;
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
                   $dtxl[$i-1]['bctitle'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['bccontent'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['bcfooter'] = $usefoo;
                   $dtxl[$i-1]['bcfrom'] = strtoupper($objWorksheet->getCell('C'.($i+1))->getValue());
                   $dtxl[$i-1]['bcmailfrom'] = $this->Msetting->getset('nosmsnotif');
                   $dtxl[$i-1]['bcket'] = 'Type: '.$bctype.'; Last Credit (Reply): '.$creditwithrep.'; Last Credit (No Reply): '.$creditnorep.'; Role: ; Period: ; Level: ; Payment: ; Reply: '.$repstat.'; Status: Success '.$tot.', Failed '.count($failed).'; Failed List: '.implode($failed,',');
				   $dtxl[$i-1]['bctype'] = "SMS";
				 
				 
				 }
              }
			  
			  //save data through model
			  $report = $this->Mbc->importdata($dtxl);
 
              
            //set flashdata
			$flashdata = 'Import '.$report['success'].' Data Success, with '.$report['failed'].' unsuccessful import.';
			if ($report['faillist']<>''){
				$flashdata = $flashdata."<br/>Data error: <br/>".$report['faillist'];
			}
			$this->session->set_flashdata('v',$flashdata);
        
			}
		//delete file
              $file = $this->upload->data()['file_name'];
              $path = FCPATH.'temp_upload/' . $file;
              unlink($path);
		//redirect to data list
		redirect(base_url('Organizer/SmsBroadcast'));
        
    }
		
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['bcdate','bctitle','bcrecipient','bcmailfrom','bcfrom','bccontent','bcket','uname']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exportbcsms($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exportbcsms(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('SMS Broadcast Data');
	
		//Create Heading
		$Hcol = 'A';
		$Hrow = 2;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
				$Hcol++;    
		}
		
		//Insert Data
		$Drow = 3;
		$Dcol = "A";
		$ctot = count($dexp);
		if ((is_array($dexp) || is_object($dexp)) and($ctot<>0)){
			foreach($dexp as $key){
					$Dcol = "A";
				//manipulate data
					(array_key_exists('bccontent',$key)) ? $key['bccontent']=$key['bccontent']: null;
					(array_key_exists('bcket',$key)) ? $key['bcket']=str_replace(";","\n",$key['bcket']): null;
					foreach ($key as $k=>$v){
						$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$v);
						if ($k =='bcket') {
						$objPHPExcel->getActiveSheet()->getStyle($Dcol.$Drow)->getAlignment()->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle($Dcol.$Drow)->getFont()->setSize(8);
						}
						$Dcol++;
					}
				$Drow++;
			}
		} else {
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,'No Data');
		$Drow=$Drow+1;
		}
		
		//set limit col and row
		$Dnewcol = chr(ord($Hcol)-1);
		$Dnewrow = $Drow-1;
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane('A3');
		
		//Create big Title
		$period = $this->Msetting->getset('period');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'SMS BROADCAST DATA ('.$title.')');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$Dnewcol.'1');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
		//setting autowidth
		foreach(range('A',$Dnewcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.$Dnewcol.$Dnewrow)->applyFromArray($styleArray);
		
		//setting footprint date
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$Drow, 'Generated on '.Date("d-m-Y H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setItalic(true);
		
		//Save as an Excel BIFF (xls) file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SMS Broadcast Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['To (use "," to send multiple)','SMS Title','Pre Text','SMS Content','Use Reply (Y/N)','Use Footer (Y/N)']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatSmsBroadcast');
		
		//Create Heading
		$Hcol = 'A';
		$Hrow = 1;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
		//create format column as text
		$objPHPExcel->getActiveSheet()->getStyle($Hcol.'1:'.$Hcol.'100')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$Hcol++;    
		}
		
		//=== Create hint
			//set new colomn
			$Dcol = chr(ord($Hcol)+1);
			$Dnewcol = chr(ord($Hcol)+3);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getFont()->setItalic(true);
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.'1', 'Hint (Please pay attention)');
		$objPHPExcel->getActiveSheet()->mergeCells($Dcol.'1:'.$Dnewcol.'1');
		$objPHPExcel->getActiveSheet()->getStyle($Dcol.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
		$Hrow= 2;
		$tempcol = $Dcol;
		$rowrole=$Hrow;
		
		//set colomn header Allow
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Phone Number");			
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow),"Details");
		$objPHPExcel->getActiveSheet()->getStyle(chr(ord($tempcol)+1).$Hrow)->getFont()->setBold(true);
		$dtuser= $this->Mbc->getphone();
		unset($dtuser['']);
		$Hrow=$Hrow+1;
		
		
		
		
		foreach ($dtuser as $al=>$val){
			$Dcol=$tempcol;
			
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($Dcol.$Hrow,$val['phone'],PHPExcel_Cell_DataType::TYPE_STRING);
				($val['level']!='') ? $lvl = '/'.$val['level']: $lvl='';
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Hrow,$val['name'].' ('.$val['role'].'/'.$val['year'].$lvl.') ');
			$Hrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow-1),"Remember: Always Put 'Phone Number' instead of 'Details' in Colomn 'To'");			
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow),"Remember: Always Put 'Y' or 'N' in Colomn 'Use Reply' and 'Use Footer'");			
		
		
		//set autowidth
		foreach(range('A',$lastcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Hcol)-1).'10')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($rowrole).':'.chr(ord($lastcol)-1).($Hrow-1))->applyFromArray($styleArray);
		
		//set background color of HINT 
		$fillArray = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb'=>'E6FF00')
			),
			'font' => array(
				'color' => array('rgb' => '003333')
			) 
		);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Hrow))->applyFromArray($fillArray);
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Hrow+6));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatSmsBroadcast.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function readsms(){
		//fecth data from db
		$col = ['bctitle','bcrecipient','bccontent','bcket','bcmailfrom','bcfrom','uname','bcdate','bcfooter'];
		$id = $this->input->get('id');
		$dbres = $this->Mbc->detailbcmail($col,$id);
		
		//set row title
		$row = $col;
		
		unset($row[4]);
		unset($row[5]);
		unset($row[6]);
		unset($row[7]);
		unset($row[8]);
		//set table template
		$tmpl = array ( 'table_open'  => '<div class="table table-hover">',
					'heading_row_start'   => '',
                    'heading_row_end'     => '',
                    'heading_cell_start'  => '',
                    'heading_cell_end'    => '',

                    'row_start'           => '',
                    'row_end'             => '',
                    'cell_start'          => '',
                    'cell_end'            => ''

					);
		$this->table->set_template($tmpl);
		//set table data
		$a = 0;
		foreach($row as $key)
		{
				($dbres[0]['bcfrom']!='') ? $pretext = 'Pre Text "'.$dbres[0]['bcfrom'].'"' : $pretext = "No Pre Text";
				($dbres[0]['bcfooter']) ? $footext = 'Footer "'.$this->Msetting->getset('smsfooter').'"' : $footext = "No Footer";
				if ($key=='bctitle'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12 bg-primary"><h3>'.$dbres[0]['bctitle'].'</h3>
					<span class="text-left">SMS Sender Account : <code>'.$dbres[0]['bcmailfrom'].'</code></span><code class="mailbox-read-time pull-right"><i class="fa fa-clock-o"></i> '.$dbres[0]['bcdate'].'</code><p>PIC: <i>'.$dbres[0]['uname'].'</i></p><hr/></div>'
					);
				} else if ($key=='bcrecipient'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><h4 class="text-primary">Recipient: </h4>'.$dbres[0]['bcrecipient'].'<hr/></div>'
					);
				} else if ($key=='bccontent'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><h4><strong>Content: </strong></h4><div class="text-justify"><small><code>'.$pretext.'</code></small></div>'.$dbres[0]['bccontent'].'<div class="text-justify"><small><code>'.$footext.'</code></small></div><hr/></div>'
					);
				} else if ($key=='bcket'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><u><h4 class="text-info">Details: </h4></u><p>'.str_replace(';', '<br/>', $dbres[0]['bcket']).'</p><hr/></div>'
					);
				} 
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/bcsms/readsms', $data);
		
		
	}
	
	public function composesms(){
		if ($this->input->get('id')!=''){
			$col =['bctitle','bcfrom','bccontent','bcket','bcfooter'];
			$arrfwd = $this->Mbc->detailbcmail($col,$this->input->get('id'));
			$valsub = $arrfwd[0]['bctitle'];
			$valcode = $arrfwd[0]['bccontent'];
			$valusefoo = $arrfwd[0]['bcfooter'];
			$valpretext = $arrfwd[0]['bcfrom'];
			$arrket = explode(';',$arrfwd[0]['bcket']);
			$valtype1 = false;
			$valtype2 = false;
			foreach ($arrket as $k=>$v){
				$arrkey = explode(':',$v);
				if (strpos($arrkey[0],'Type')!== false) {
				(strpos($arrkey[1],' Single SMS')!== false) ? $valtype1 = true :$valtype2 = true;
				}
			}
		} else{
			$valsub=null;
			$valalias=null;
			$valcode=null;
			$valtype1 = false;
			$valtype2 = false;
			$valpretext = null;
			
		}
		
		
		//============ form to subect type ==============
			$optrole=$this->Mbc->getoptrole();
			$optyear=$this->Mbc->getoptyear();
			$optlvl=$this->Mbc->getoptlevel();
		$adv['Type'] = form_radio('mbc','0',$valtype1).'Single SMS '.form_radio('mbc','1',$valtype2).'Broadcast SMS<br/>
		<div class="hidden" id="optbc">
				<label>Select User Group </label>'.form_dropdown(array("name"=>"optrole","class"=>"form-control","id"=>"optrole"),$optrole).
				'<label>Select Year </label>'.form_dropdown(array("name"=>"optyear","class"=>"form-control","id"=>"optyear"),$optyear).
				'<label class="hidden" id="lbllunas">Payment Status </label>'.form_dropdown(array("name"=>"optlunas","class"=>"hidden","id"=>"optlunas"),array(''=>'All Status','1'=>'Fully Paid','0'=>'Not Yet')).
				'<label class="hidden" id="lbllvl">Select Level </label>'.form_dropdown(array("name"=>"optlvl","class"=>"hidden","id"=>"optlvl"),$optlvl).
				form_button(array("name"=>"apply","class"=>"btn btn-sm btn-primary","id"=>"applyall"),"Apply All").
				'</div>';


		$adv['To'] = form_input(
						array('name'=>'mto',
						'id'=>'mto',
						'placeholder'=>'SMS Recipient',
						'required'=>'required',
						'size'=>'100',
						'style'=>'height:auto;min-height:30px;width:100%;',
						'value'=>null,
						'class'=>'form-control'));
		
		$adv['Title'] = form_input(
						array('name'=>'msub',
						'id'=>'msub',
						'placeholder'=>'SMS Title',
						'size'=>'112',
						'style'=>'height:auto;min-height:30px;width:100%;',
						'required'=>'required',
						'value'=>$valsub,
						'class'=>'form-control'));
		
		$adv['Pre Text'] = form_input(
						array('name'=>'malias',
						'id'=>'mali',
						'placeholder'=>'Placed Before Content',
						'size'=>'112',
						'style'=>'height:auto;min-height:30px;width:100%;',
						'value'=>$valpretext,
						'class'=>'form-control'));
					
		$data['editor'] = form_textarea(
						array('name'=>'mtext',
						'id'=>'smstext',
						'placeholder'=>'SMS Content',
						'style'=>'width:100%',
						'cols'=>'',
						'rows'=>'10',
						'required'=>'required',
						'value'=>$valcode,
						'class'=>'form-control')
						);
		$data['usereply'] = form_checkbox(
						array('name'=>'userep',
						'id'=>'muserep',
						'value'=>'1',
						'class'=>''));
		
		
		$data['counterchar'] = form_input(
						array('name'=>'mcounterchar',
						'id'=>'mcounterchar',
						'size'=>'2',
						'value'=>'0',
						'readonly'=>'readonly',
						'class'=>'form-control'));
		$data['countersms'] = form_input(
						array('name'=>'mcountersms',
						'id'=>'mcountersms',
						'size'=>'2',
						'value'=>'0',
						'readonly'=>'readonly',
						'class'=>'form-control'));
		$data['countercredit'] = form_input(
						array('name'=>'mcountercredit',
						'id'=>'mcountercredit',
						'size'=>'10',
						'value'=>'0',
						'readonly'=>'readonly',
						'class'=>'form-control'));
		$data['listto']=form_hidden('flistto');
		$data['nameto']=form_hidden('fnameto');
		$data['redi']=form_hidden('fredi',1);
		
		$this->form_validation->set_rules('fcode', 'HTML Code', 'required|xss_clean|strip_tags|trim');
		
		$fsend = array(	'id'=>'redi',
						'value'=>'Send, Compose more',
						'class'=>'btn btn-primary btn-lg',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$fsen = array(	'id'=>'submit',
						'value'=>'Send',
						'class'=>'btn btn-info btn-lg',
						'type'=>'submit');
		$data['inred'] = form_submit($fsen);

		$data['footer'] = $this->Msetting->getset('smsfooter');
		$data['codefooter'] = form_hidden('ffooter',$data['footer']);
		
		$data['usefoo'] = form_checkbox(array(
							'name'=>'fusefoo',
							'id'=>'usefoo',
							'checked'=>isset($valusefoo) ? $valusefoo : false,
							'value'=>'1')
							);
		$data['fusersto'] = form_hidden('fusersto','');
		// setting account and programming
		$accuser = $this->Msetting->getset('smsuserkey');
		$accpass = $this->Msetting->getset('smspasskey');
		$urlrep = $this->Msetting->getset('smsurl');
		$funcrep = $this->Msetting->getset('smsapi');
		$funccredit = $this->Msetting->getset('smsapibalance');
			
		$data['sender'] = $accuser;
		$data['accuser']= form_input(array(
						'name'=>'faccuser',
						'id'=>'accsender',
						'class'=>'form-control',
						'value'=>$accuser)
						);
		
		$data['accpass']=form_password(array(
						'name'=>'faccpass',
						'id'=>'accpass',
						'class'=>'form-control',
						'value'=>$accpass));
		
		$data['urlbc']= form_input(array(
						'name'=>'furlbc',
						'id'=>'urlbc',
						'class'=>'form-control',
						'value'=>$urlrep)
						);
		
		$data['funcnotif']= form_input(array(
						'name'=>'fapibc',
						'id'=>'apibc',
						'class'=>'form-control',
						'value'=>$funcrep)
						);
		
		$data['funcsaldo']= form_input(array(
						'name'=>'fapisaldo',
						'id'=>'apisaldo',
						'class'=>'form-control',
						'value'=>$funccredit)
						);
			$opttmp = $this->Mbc->getopttmp('SMS');
		
		$data['opttmp']= form_dropdown(array(
						'name'=>'ftmp',
						'id'=>'opttmp',
						'style'=>'width:100%',
						'class'=>'form-control'),
						$opttmp
						);

		$data['btnupdateset'] = form_submit(array(
						'id'=>'submitsetting',
						'class'=>'btn btn-primary',
						'value'=>'Update Configuration')
						);
		
		$data['furlsave'] = site_url('Organizer/SmsBroadcast/savesetting');
		
		$data['lastsync'] = date("d-M-Y",strtotime($this->Msetting->getset('lastsynccontacts')));
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter .='<div class="row"><div class="col-md-1"><label>'.$a.'</label></div><div class="col-md-11"><span>'.$v.'</span></div></div>';
		}
		$data['metadata'] = $dtfilter;
		
		//=============== some setting var ========
		$data['smscredit'] = $this->Msetting->getset('smscredit');
		$data['expdate'] = $this->Msetting->getset('smsexpiration');

		//=============== Template ============
		$data['jsFiles'] = array(
							'tokchi/tokchi','ajaxupload/jquery.knob','ajaxupload/jquery.ui.widget','ajaxupload/jquery.iframe-transport','ajaxupload/jquery.fileupload','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'tokchi/tokchi','ajaxupload/style','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Compose SMS";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/bcsms/compose', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
				$tot = 0; $failed= array();
			foreach($dtuser as $k=>$v){
				if ($this->input->post() == '1') {
					$r = $this->Mbc->deletebc($v);
					$item = 'Broadcast SMS';
				} else {
					$r = $this->Mbc->deletetmp($v);
					$item = 'SMS Template';
				}
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected '.$item.' success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, Delete '.$item.' Failed.');
		}
		($this->input->post() == '1') ?	redirect(base_url('Organizer/SmsBroadcast')) : redirect(base_url('Organizer/SmsBroadcast/template'));
	}
		
	public function confirmsynch(){
		$dtset=array(
				'lastsynccontacts'=>date("m-d-Y"));
		$this->Msetting->savesetting($dtset);
		$data['title']="Synchronization Success";
		$this->load->view('dashboard/org/bcsms/confirmsynch', $data);
	}
	
	public function helpreply(){
		$data['txtperiod'] = $this->Msetting->getset('period');
		$data['user'] = form_hidden('usr_email',$this->Msetting->getset('usersmsnotif')); 
		$data['pass'] = form_hidden('pwd',$this->Msetting->getset('passsmsnotif')); 
		$data['period'] = form_hidden('dir_name',$data['txtperiod']); 
		$data['code'] = form_hidden('dir_code',"RC".mb_substr($data['txtperiod'],2,2)); 
		
		$this->load->view('dashboard/org/bcsms/helpreply',$data);
	}
	
	public function downloadcontacts(){
		
		$arrphone = $this->Mbc->getphone();
			foreach($arrphone as $k=>$v){
			$txtphone .= '"'.$v['name'].'",'.'"'.$v['phone'].'",'.'"'.$v['year'].'",'.'"'.$v['role'].'",'.'"'.$v['level'].'";';
			(end($arrphone) !== $v) ? $txtphone .= "\r\n" :null;
			}
		$this->load->helper('download');
		force_download('Contacts - '.date("d-M-Y H:i:s").'.txt', $txtphone);
	}
	
	public function deletesms(){
		$id = $this->input->get('id');
		$r = $this->Mbc->deletebc($id);
	if ($r){
		$this->session->set_flashdata('v','Delete SMS Success');
		} else{
		$this->session->set_flashdata('x','Delete SMS Failed');
		} 
		redirect(base_url('Organizer/SmsBroadcast'));
	}

	public function printbcsms(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['bcdate','bctitle','bcrecipient','bcmailfrom','bcfrom','bccontent','bcket','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exportbcsms($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exportbcsms(null,null,$dtcol);
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
					(array_key_exists('bccontent',$val)) ? $dexp[$key]['bccontent']=htmlspecialchars_decode($val['bccontent']): null;
					(array_key_exists('bcket',$val)) ? $dexp[$key]['bcket']=str_replace(";","<br/>",$val['bcket']): null;
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="SMS Broadcast Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/bcsms/printbcsms', $data);
		
	}
	
	public function getcontactlist(){
		$frole= $this->input->post('role');
		$fyear= $this->input->post('period');
		$fstat= $this->input->post('lunas');
		$flevel= $this->input->post('level');
		$temp = $this->Mbc->getphone($frole,$fyear,$fstat,$flevel);
		
		$r = (object) []; 	
		foreach($temp as $k){
			$nm = explode(' ',$k['name']);
			foreach($nm as $n){
				$char = strtolower(mb_substr($n,0,1));	
				(!property_exists($r,$char)) ? $r->$char = array():null;
				array_push($r->$char,$k);
				}
			$r->all = $temp;
		}
		
		echo json_encode($r);
	}
	
	public function checkcredit(){
		$this->load->library('sms');
		$ret = $this->sms->checkcredit();
		$dt = json_decode($ret);
		print(json_encode($dt->message));
		return $dt;
	}
	
	public function updatecredit(){
		$this->load->library('sms');
		$temp = json_decode($this->sms->checkcredit());
		
		if (isset($temp->message->value)) 
		{	$fsetting = array(
					'smscredit'=>$temp->message->value,
					'smsexpiration'=>$temp->message->text
					);
			$r = $this->Msetting->savesetting($fsetting);
		} else {
			$r = false;
		}
	echo $r;
	return $r;
	}

//---------------------------------------- TEMPLATE FUNCTION --------------------------------------------	
	public function template(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idtmplte','tmpdate','tmpname','tmpcontent','uname'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		//checkbox checkalldata
		$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'btn btn-default btn-sm',
							'value'=>'all',
							'id'=>'c_all'
							));	
		array_unshift($header,$checkall);
		$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover table-striped">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
			
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 10;
		$perpage= isset($tempfilter['page']) ? $tempfilter['page'] : 1;
		unset($filter['code']);
		
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
			$rows = $this->Mbc->counttmpsms($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mbc->counttmpsms();	
		}
		//================ filter handler ================
		$fq = array('name'=>'search',
						'id'=>'search',
						'required'=>'required',
						'placeholder'=>'Search Here',
						'value'=> isset($tempfilter['search']) ? $tempfilter['search'] : null ,
						'class'=>'form-control input-xs');
		$data['inq'] = form_input($fq);
			$optf = array(
						'tmpdate' => 'Template Created',
						'tmpname' => 'Template Name',
						'bccontent' => 'Template Content',
						'uname' => 'PIC'
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
		$adv['Template Created'] = form_input(
						array('name'=>'tmpdate',
						'id'=>'createdon',
						'placeholder'=>'Template Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['bcdate']) ? $tempfilter['bcdate'] : null,
						'class'=>'form-control'));
		
		$adv['Template Name'] = form_input(
						array('name'=>'tmpname',
						'id'=>'tmptitle',
						'placeholder'=>'Template Name',
						'value'=>isset($tempfilter['bctitle']) ? $tempfilter['bctitle'] : null,
						'class'=>'form-control'));
							
		$adv['Template Content'] = form_input(
						array('name'=>'tmpcontent',
						'id'=>'tmpcontent',
						'placeholder'=>'Template Content',
						'size'=>'45',
						'value'=>isset($tempfilter['bccontent']) ? $tempfilter['bccontent'] : null,
						'class'=>'form-control'));
		
		$adv['PIC'] = form_input(
						array('name'=>'uname',
						'id'=>'picname',
						'placeholder'=>'PIC',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/SmsBroadcast/template?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/SmsBroadcast/template?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Mbc->datatmpsms($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'checkbox',
							'value'=>$value['idtmplte']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text 
					$txt = strip_tags(htmlspecialchars_decode($value['tmpcontent']));
					(strlen($txt)>30) ? $tmptext = mb_substr($txt,0,30).'...':$tmptext=$txt;
				
				$temp[$key]['tmpcontent']='<span class="idname hidden">'.$value['tmpname'].'</span>'.$tmptext;
				$temp[$key]['tmpdate']=date('d-M-Y', strtotime($value['tmpdate'])).'<br/>'.date('H:i:s', strtotime($value['tmpdate']));
				
				//manipulation menu
				$enc = $value['idtmplte'];
				unset($temp[$key]['idtmplte']);
				$temp[$key]['tmpname']='<a href="'.base_url('Organizer/SmsBroadcast/readtmp?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read SMS Template"><b>'.$value['tmpname'].'</b></a>';
				$temp[$key]['menu']='<div class="btn-group" aria-label="Template Menu" role="group"><a href="'.base_url('Organizer/SmsBroadcast/readtmp?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/SmsBroadcast/edittemplate?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/SmsBroadcast/deletetmp?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a></div>';
				
				}
		
		$data['listlogin'] = $this->table->generate($temp);
		
		// ======== Delete multiple ==============
			$data['idac']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedid',
								'name'=>'fusers'
								));
			$data['idtype']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedtype',
								'name'=>'ftype'
								));
			$data['factselected'] = site_url('Organizer/SmsBroadcast/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/SmsBroadcast/tmpimportxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'tmpdate' => 'Template Created',
						'tmpname' => 'Template Name',
						'tmpcontent' => 'Template Content',
						'uname' => 'PIC'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol input-xs',
							'multiple'=>'multiple',
							'required'=>'required'),$optcol);
			$data['fcheckcol']= form_input(array(
							'name'=>'fcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm selectall',
							'value'=>'Select all columns'
							));
			$data['funcheckcol']= form_input(array(
							'name'=>'funcheckall',
							'type'=>'button',
							'class'=>'btn btn-info btn-sm unselectall',
							'value'=>'Unselect all columns'
							));
			$data['fusedate']= form_checkbox(array(
							'name'=>'fusedate',
							'id'=>'usedate',
							'value'=>'use'
							),false);
			$data['fdtrange']= form_input(array(
							'name'=>'fdtrange',
							'type'=>'text',
							'class'=>'form-control frange',
							'id'=>'rangedate'
							));
			$data['fbtnexport']= form_submit(array('value'=>'Export',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factexp'] = site_url('Organizer/SmsBroadcast/tmpexportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/SmsBroadcast/printtmpsms');
		
		//=============== setting ============
			$smsfoo = $this->Msetting->getset('smsfooter');
			
			$data['fooeditor']= form_textarea(array(
							'name'=>'feditfoo',
							'id'=>'editfoo',
							'cols'=>'148',
							'rows'=>'2',
							'class'=>'form-control',
							'value'=>$smsfoo)
							);
			$data['fbtnupdate']= form_submit(array('value'=>'Update Footer',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/SmsBroadcast/savesetting');
				
			
			$data['totmail'] = $this->Mbc->countbcsms();
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="SMS Template";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/bcsms/tmpsmslist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addtemplate(){
	$id=$this->input->get('id');
	$colq=['tmpname','tmpcontent'];
	//============ form edit quest ===========
		$ftmpname =  array('name'=>'ftmpname',
						'id'=>'ftmpname',
						'required'=>'required',
						'placeholder'=>'Template Name',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($ftmpname);
		
		$fcont = array('name'=>'fcont',
						'id'=>'fcont',
						'required'=>'required',
						'placeholder'=>'Template Content',
						'value'=>'',
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
		$fsend = array(	'id'=>'addtmp',
						'value'=>'Add Template',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		$data['inid'] = form_hidden('fid',$id);
		
		//set row title
		$row = $this->returncolomn($colq);
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
		//=========== generate add form available user =========================
		$this->table->set_template($tmpl);
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
		
		$this->load->view('dashboard/org/bcsms/addtmp', $data);
	}
	
	public function edittemplate(){
	//fecth data from db
		$col = ['tmpdate','tmpname','tmpcontent','uname'];
		$id = $this->input->get('id');
		$dbres = $this->Mbc->detailtmpsms($col,$id);
		$this->load->library('Converttime');
		
	$colq=['tmpname','tmpcontent'];
	//============ form edit quest ===========
		$ftmpname =  array('name'=>'ftmpname',
						'id'=>'ftmpname',
						'required'=>'required',
						'placeholder'=>'Template Name',
						'value'=>$dbres[0]['tmpname'],
						'class'=>'form-control');
		$r[]=form_input($ftmpname);
		
		$fcont = array('name'=>'fcont',
						'id'=>'fcont',
						'required'=>'required',
						'placeholder'=>'Template Content',
						'value'=>$dbres[0]['tmpcontent'],
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
		$fsend = array(	'id'=>'updatetmp',
						'value'=>'Update Template',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inid'] = form_hidden('fid',$id);
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($colq);
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
		//=========== generate add form available user =========================
		$this->table->set_template($tmpl);
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
		
		$this->load->view('dashboard/org/bcsms/edittmp', $data);
	}
	
	public function readtmp(){
		//fecth data from db
		$col = ['tmpdate','tmpname','tmpcontent','uname'];
		$id = $this->input->get('id');
		$dbres = $this->Mbc->detailtmpsms($col,$id);
		$this->load->library('Converttime');
		//set row title
		$row = $this->returncolomn($col);
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
		$dbres[0]['tmpdate'] = date('d-M-Y H:i:s', strtotime($dbres[0]['tmpdate']));
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/bcsms/readtmp', $data);
		
		
	}
	
	public function deletetmp(){
		$id = $this->input->get('id');
		$r = $this->Mbc->deletetmp($id);
	if ($r){
		$this->session->set_flashdata('v','Delete SMS Template Success');
		} else{
		$this->session->set_flashdata('x','Delete SMS Template Failed');
		} 
		redirect(base_url('Organizer/SmsBroadcast/template'));
	}
	
	public function savetmp(){
				// set new data variable
				$fdata = array(
					'tmpdate'=>date('Y-m-d H:i:s'),
					'tmpname'=>$this->input->post('ftmpname'),
					'tmpcontent'=>$this->input->post('fcont'),
					'uuser'=>$this->session->userdata('user'),
					'tmptype'=>'SMS'
					);
			//update to database
			$hsl = $this->Mbc->savetmp($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add SMS Template Succes.') : $this->session->set_flashdata('x','Add SMS Template Failed.');
			
		redirect(base_url('Organizer/SmsBroadcast/template'));
		
	}
	
	public function updatetmp(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'tmpname'=>$this->input->post('ftmpname'),
					'tmpcontent'=>$this->input->post('fcont'),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Mbc->updatetmp($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update SMS Template Succes.') : $this->session->set_flashdata('x','Update SMS Template Failed.');
			
		redirect(base_url('Organizer/SmsBroadcast/template'));
		
	}
	
	public function tmpimportxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatSmsTemplate.xls') ){
                // if file validation failed, send error to view
                $error = ['file choosen is not match with pre-defined file',$this->upload->display_errors()];
				array_filter($error);
				$this->session->set_flashdata('x','Import Data Failed, details: '.implode(' ',$error));
            } else {
              // if upload success, take file data
              $upload_data = $this->upload->data();
			 
              // load library Excell_Reader
              $this->load->library('Excel');
			  $objPHPExcel = PHPExcel_IOFactory::load($upload_data['full_path']);
			  $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
 
              // array data
			  $highestRow = $objWorksheet->getHighestRow();
			  $highestColumn = $objWorksheet->getHighestColumn();
              $dtxl = Array();
			  
			  //variable
			  $creditnorep = $this->Msetting->getset('saldosmsnotif');
			  $creditwithrep = $this->Msetting->getset('saldosmsbc');
			  $footer = $this->Msetting->getset('smsfooter');
			  
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
				 
				   $dtxl[$i-1]['tmpdate'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['tmpname'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['tmpcontent'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['tmptype'] = "SMS";
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
				 
				 }
              }
			  
			  //save data through model
			  $report = $this->Mbc->tmpimportdata($dtxl);
 
              
            //set flashdata
			$flashdata = 'Import '.$report['success'].' Data Success, with '.$report['failed'].' unsuccessful import.';
			if ($report['faillist']<>''){
				$flashdata = $flashdata."<br/>Data error: <br/>".$report['faillist'];
			}
			$this->session->set_flashdata('v',$flashdata);
        
			}
		//delete file
              $file = $this->upload->data()['file_name'];
              $path = FCPATH.'temp_upload/' . $file;
              unlink($path);
		//redirect to data list
		redirect(base_url('Organizer/SmsBroadcast/template'));
        
    }
		
	public function tmpexportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['tmpdate','tmpname','tmpcontent','uname']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exporttmpsms($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exporttmpsms(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('SMS Template Data');
	
		//Create Heading
		$Hcol = 'A';
		$Hrow = 2;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
				$Hcol++;    
		}
		
		//Insert Data
		$Drow = 3;
		$Dcol = "A";
		$ctot = count($dexp);
		if ((is_array($dexp) || is_object($dexp)) and($ctot<>0)){
			foreach($dexp as $key){
					$Dcol = "A";
				//manipulate data
					foreach ($key as $k=>$v){
						$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$v);
						$Dcol++;
					}
				$Drow++;
			}
		} else {
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,'No Data');
		$Drow=$Drow+1;
		}
		
		//set limit col and row
		$Dnewcol = chr(ord($Hcol)-1);
		$Dnewrow = $Drow-1;
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane('A3');
		
		//Create big Title
		$period = $this->Msetting->getset('period');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'SMS TEMPLATE DATA ('.$title.')');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$Dnewcol.'1');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
		//setting autowidth
		foreach(range('A',$Dnewcol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.$Dnewcol.$Dnewrow)->applyFromArray($styleArray);
		
		//setting footprint date
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$Drow, 'Generated on '.Date("d-m-Y H:i:s"));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$Drow)->getFont()->setItalic(true);
		
		//Save as an Excel BIFF (xls) file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="SMS Template Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function tmppredefinedimport(){
		$dtcol = ['Template Name','Template Content']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatSmsTemplate');
		
		//Create Heading
		$Hcol = 'A';
		$Hrow = 1;
		foreach($dtcol as $h){
				$objPHPExcel->getActiveSheet()->setCellValue($Hcol.$Hrow,$h);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle($Hcol.$Hrow)->getFont()->setBold(true);
		//create format column as text
		$objPHPExcel->getActiveSheet()->getStyle($Hcol.'1:'.$Hcol.'100')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$Hcol++;    
		}
		
		$Bordercol = chr(ord($Hcol)-1);
		
		
		//set autowidth
		foreach(range('A',$Bordercol) as $columnID) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
		}
		
		//setting border
		$styleArray = array(
		'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
			))
		);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Hcol)-1).'10')->applyFromArray($styleArray);
		
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane($Hcol.($Hrow+10));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatSmsTemplate.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function printtmpsms(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['tmpdate','tmpname','tmpcontent','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exporttmpsms($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exporttmpsms(null,null,$dtcol);
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
					(array_key_exists('tmpdate',$val)) ? $dexp[$key]['tmpdate']=date('d-M-Y',strtotime($val['tmpdate'])).'<br/>'.date('H:i:s',strtotime($val['tmpdate'])): null;
					
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->template();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="SMS Template Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/bcsms/printtmpsms', $data);
		
	}
	
	
	public function gettmpdata(){
		$id= $this->input->post('idtmp');
		$data = $this->Mbc->gettmpdata($id);
		echo json_encode($data);
	}
	
	public function savesetting(){
		if(null!= $this->input->post('faccuser')){
			$user = $this->input->post('faccuser');
			$pass = $this->input->post('faccpass');
			$urlsms = $this->input->post('furlbc');
			$apisms = $this->input->post('fapibc');
			$apisaldo = $this->input->post('fapisaldo');
		$dtset=array(
				'smsuserkey'=>$user,
				'smspasskey'=>$pass,
				'smsurl'=>$urlsms,
				'smsapi'=>$apisms,
				'smsapibalance'=>$apisaldo
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting SMS Broadcast Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting SMS Broadcast Failed.");
		}
		redirect(base_url('Organizer/Smsbroadcast/composesms'));
	}
	
	public function returncolomn($header) {
	$find=['bcdate','bctitle','bcrecipient','bcmailfrom','bccontent','bcket','uname','bcfrom','tmpdate','tmpname','tmpcontent'];
	$replace = ['SMS Created','SMS Title','SMS Recipient','SMS Sender','SMS Content','Details','PIC','Pretext','Template Created', 'Template Name','Template Content'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
