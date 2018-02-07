<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailbroadcast extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertroman','Gmail'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mbc','Msetting'));
    }

	public function index(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idbcast','bcdate','bctitle','bcrecipient','bccontent','bcattach','uname','bcmailfrom','bcfrom','bcket'];
		$header = ['','Date','Subject','Recipient','Content','<span class="fa fa-paperclip"></span>','PIC','Menu'];
		
		//checkbox checkalldata
		$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'btn btn-default btn-sm',
							'value'=>'all',
							'id'=>'c_all'
							));	
			$data['btncheckall']= $checkall;
		//$header[]='Menu';
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
			$rows = $this->Mbc->countbcmail($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mbc->countbcmail();	
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
						'bcdate' => 'Mail Created',
						'bctitle' => 'Mail Subject',
						'bcrecipient' => 'Mail Recipient',
						'bcmailfrom' => 'Mail Sender',
						'bccontent' => 'Mail Content',
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
		$adv['Mail Created'] = form_input(
						array('name'=>'bcdate',
						'id'=>'createdon',
						'placeholder'=>'Mail Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['bcdate']) ? $tempfilter['bcdate'] : null,
						'class'=>'form-control'));
		
		$adv['Mail Subject'] = form_input(
						array('name'=>'bctitle',
						'id'=>'bctitle',
						'placeholder'=>'Mail Subject',
						'value'=>isset($tempfilter['bctitle']) ? $tempfilter['bctitle'] : null,
						'class'=>'form-control'));
						
		$adv['Mail Recipient'] = form_input(
						array('name'=>'bcrecipient',
						'id'=>'bcreci',
						'placeholder'=>'Mail Recipient',
						'value'=>isset($tempfilter['bcrecipient']) ? $tempfilter['bcrecipient'] : null,
						'class'=>'form-control'));
						
		$adv['Mail Sender'] = form_input(
						array('name'=>'bcmailfrom',
						'id'=>'bcmailfrom',
						'placeholder'=>'Mail Sender',
						'value'=>isset($tempfilter['bcmailfrom']) ? $tempfilter['bcmailfrom'] : null,
						'class'=>'form-control'));
		
		$adv['Mail Content'] = form_input(
						array('name'=>'bccontent',
						'id'=>'bccontent',
						'placeholder'=>'Mail Content',
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
						array('name'=>'mailtype',
						'id'=>'mailtype',
						'class'=>'form-control'),array(''=>'Please Select','Single Mail'=>'Single Mail','Broadcast Mail'=>'Broadcast'),isset($tempfilter['mailtype']) ? $tempfilter['mailtype'] : null);
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Mailbroadcast/?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Mailbroadcast?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Mbc->databcmail($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				(strpos($value['bcket'], 'Broadcast Mail') !== false) ? $lbl = '<i class="fa fa-users text-primary"></i>' : $lbl ='<i class="fa fa-user text-yellow"></i>';
				$ctable = $lbl.'<br/>'.form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser bg-aqua',
							'value'=>$temp[$key]['idbcast']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text email
					$txt = strip_tags(html_entity_decode($value['bccontent']));
					(strlen($txt)>30) ? $tmptext = mb_substr($txt,0,30).'...':$tmptext=$txt;
					
				$temp[$key]['bccontent']='<span class="uname hidden">'.$value['bcmailfrom'].' ('.$value['bctitle'].')</span>'.$tmptext;
				($value['bcattach']!=null) ? $temp[$key]['bcattach']='<span class="fa fa-paperclip"></span>':$temp[$key]['bcattach']='-';
				$temp[$key]['bcdate']=date('d-M-Y', strtotime($value['bcdate'])).'<br/>'.date('H:i:s', strtotime($value['bcdate'])).'<br/>('.$this->converttime->time_elapsed_string(date('d-M-Y H:i:s', strtotime($value['bcdate']))).')';
				$temp[$key]['bcrecipient']='<small>'.str_replace(',','<br/>',$value['bcrecipient']).'</small>';
				
				//manipulation menu
				$enc = $value['idbcast'];
				unset($temp[$key]['idbcast']);
				unset($temp[$key]['bcmailfrom']);
				unset($temp[$key]['bcket']);
				$temp[$key]['bcfrom'] = '<div class="btn-group-vertical">
										<a href="'.base_url('Organizer/Mailbroadcast/composemail?id=').$enc.'" alt="Forward Data" class="btn btn-primary btn-xs" title="Forward"><i class="fa fa-share"></i> </a>
										<a href="#" data-href="'.base_url('Organizer/Mailbroadcast/deletemail?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i> </a>
										</div>';
				
				$temp[$key]['bctitle']='<a href="'.base_url('Organizer/Mailbroadcast/readmail?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Mail"><b>'.$value['bctitle'].'</b></a><br/><small class="text-muted">'.$value['bcmailfrom'].'</small>';
				}
		
		$data['listlogin'] = $this->table->generate($temp);
		
		// ======== delete multiple ==============
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
			$data['factselected'] = site_url('Organizer/Mailbroadcast/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Mailbroadcast/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'bcdate' => 'Mail Created',
						'bctitle' => 'Mail Subject',
						'bcrecipient' => 'Mail Recipient',
						'bcmailfrom' => 'Mail Sender',
						'bcfrom' => 'Sender Alias',
						'bccontent' => 'Mail Content',
						'bcattach' => 'Mail Attachment',
						'bcket' => 'Mail Details',
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
			$data['factexp'] = site_url('Organizer/Mailbroadcast/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Mailbroadcast/printbcmail');
		
		//=============== setting gmail ============
						
			$data['editfooter']= form_textarea(
								array(
								'name'=>'ffooter',
								'id'=>'editfoo',
								'class'=>'form-control',
								'rows'=>'5',
								'cols'=>'150'
								)
								);
			$data['fcode'] = form_hidden('fcode',html_entity_decode($this->Msetting->getset('mailfooter')));
			
			$data['fbtnupdate']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Mailbroadcast/savesetting');
					
			$data['totmail'] = $this->Mbc->countbcmail();
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','summernote/summernote');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Mail Broadcast";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/bcmail/bcmaillist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatMailBroadcast.xls') ){
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
              for ($i = 1; $i <=$highestRow; $i++) {
				if ($objWorksheet->getCell('A'.($i+1))->getValue()!=''){
				//determine some variables
				$to = $objWorksheet->getCell('A'.($i+1))->getValue();
				$cc = $objWorksheet->getCell('C'.($i+1))->getValue();
				(strpos($to, ',') !== false) ? $bctype="Broadcast Mail":$bctype="Single Mail";
				if ($objWorksheet->getCell('F'.($i+1))->getValue()=='Y') 
				{
					$usefoo = true;
					$fcode = html_entity_decode($objWorksheet->getCell('D'.($i+1))->getValue().'<br/>'.$this->Msetting->getset('mailfooter'));
				} else {
					$usefoo = false;
					$fcode = html_entity_decode($objWorksheet->getCell('D'.($i+1))->getValue());
				}
			  
                   $dtxl[$i-1]['bcdate'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['bcrecipient'] = $to;
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
                   $dtxl[$i-1]['bctitle'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['bccontent'] = htmlentities($objWorksheet->getCell('D'.($i+1))->getValue());
                   $dtxl[$i-1]['bcfrom'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['bcmailfrom'] = $this->Msetting->getset('sendermail');
                   $dtxl[$i-1]['bcket'] = 'Type: '.$bctype.'; CC: '.$cc.'; Role: ; Period: ; Level: ; Payment: ';
                   $dtxl[$i-1]['bcfooter'] = $usefoo;
                   $dtxl[$i-1]['bctype'] = "Mail";
				 
		
				 $arrto = explode(',',$to);
				 $tot = 0;
				 $this->load->library('Convertcode');
				 foreach($arrto as $k=>$v){
					$val = str_replace(' ','',$v);
					$decode = $this->convertcode->decodemailmsg($fcode,$val);	
					//================= gmail send ===========
					$ret = false;//$this->gmail->sendmail($v,$cc,$dtxl[$i-1]['bctitle'],$dtxl[$i-1]['bcfrom'],$decode,null);		
					($ret) ? $tot++:$failed[]=($k+1).'. '.$v;

					}
				 
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
		redirect(base_url('Organizer/Mailbroadcast'));
        
    }
	
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['bcdate','bctitle','bcrecipient','bcmailfrom','bcfrom','bccontent','bcattach','bcket','uname']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exportbcmail($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exportbcmail(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Mail Broadcast Data');
	
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
					(array_key_exists('bccontent',$key)) ? $key['bccontent']=html_entity_decode($key['bccontent']): null;
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'MAIL BROADCAST DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Mail Broadcast Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['To (use "," to send multiple)','Email Subject','CC','Email Content','Alias','Use Footer (Y/N)']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatMailBroadcast');
		
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
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Email");			
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow),"Details");
		$objPHPExcel->getActiveSheet()->getStyle(chr(ord($tempcol)+1).$Hrow)->getFont()->setBold(true);
		$dtuser= $this->Mbc->getcontact();
		unset($dtuser['']);
		$Hrow=$Hrow+1;
		foreach ($dtuser as $al=>$val){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Hrow,$val['email']);
				($val['level']!='') ? $lvl = '/'.$val['level']: $lvl='';
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Hrow,$val['name'].' ('.$val['role'].'/'.$val['year'].$lvl.') ');
			$Hrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow-1),"Remember: Always Put 'Email' instead of 'Details' in Colomn 'To' or 'CC'");			
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Hrow-1))->applyFromArray($fillArray);
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Hrow+6));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatMailBroadcast.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function readmail(){
		//fecth data from db
		$col = ['bcmailfrom','bcrecipient','bccontent','bcattach','bcket','uname','bcfrom','bctitle','bcdate'];
		$id = $this->input->get('id');
		$dbres = $this->Mbc->detailbcmail($col,$id);
		
		//set row title
		$row = $col;
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
				
				if ($key=='bcmailfrom'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12 bg-primary"><h3>'.$dbres[0]['bctitle'].'</h3>
					<span class="text-left">Email Sender : <code>'.$dbres[0][$col[$a]].'</code> <i>('.$dbres[0]['bcfrom'].')</i></span><code class="mailbox-read-time  pull-right"><i class="fa fa-clock-o"></i> '.$dbres[0]['bcdate'].'</code><p>PIC: <i>'.$dbres[0]['uname'].'</i></p><hr/></div>'
					);
				} else if ($key=='bccontent'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><h4><strong>Content: </strong></h4>'.html_entity_decode($dbres[0]['bccontent']).'<hr/></div>'
					);
				} else if ($key=='bcattach'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><i><b><h4 class="text-primary">Attachment: </h4></b></i><p>'.$dbres[0]['bcattach'].'</p><hr/></div>',
					"dtval"=>''
					);
				} else if ($key=='bcrecipient'){
				$dtable[$a] = array(
					"dtcol"=>'<div class="col-md-12"><i><b><h4 class="text-primary">Recipient: </h4></b></i><p>'.str_replace(',','<br/>',$dbres[0]['bcrecipient']).'</p><hr/></div>',
					"dtval"=>''
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
		$this->load->view('dashboard/org/bcmail/readmail', $data);
		
		
	}
	
	public function composemail(){
		if ($this->input->get('id')!=''){
			$col =['bctitle','bcfrom','bccontent','bcket','bcfooter'];
			$arrfwd = $this->Mbc->detailbcmail($col,$this->input->get('id'));
			$valsub = $arrfwd[0]['bctitle'];
			$valalias = $arrfwd[0]['bcfrom'];
			$valusefooter = $arrfwd[0]['bcfooter'];
			$valcode = html_entity_decode($arrfwd[0]['bccontent']);
			$arrket = explode(';',$arrfwd[0]['bcket']);
			$valtype1 = false;
			$valtype2 = false;
			foreach ($arrket as $k=>$v){
				$arrkey = explode(':',$v);
				if (strpos($arrkey[0],'Type')!== false) {
				(strpos($arrkey[1],' Single Mail')!== false) ? $valtype1 = true :$valtype2 = true;
				}
			}
		} else{
			(isset($tempfilter['msub']) ? $valsub = $tempfilter['msub'] : $valsub=null);
			(isset($tempfilter['malias']) ? $valalias = $tempfilter['malias'] : $valalias=null);
			(isset($tempfilter['fcode']) ? $valcode = $tempfilter['fcode'] : $valcode=null);
			$valtype1 = false;
			$valtype2 = false;
			
		}
		
		//============ gmail config ================
		$data['urlauth'] = $this->gmail->authorize(base_url('Organizer/Mailbroadcast/composemail'));
		
		
		//============ form to subect type ==============
			$optrole=$this->Mbc->getoptrole();
			$optyear=$this->Mbc->getoptyear();
			$optlvl=$this->Mbc->getoptlevel();
		$adv['Type'] = form_radio('mbc','0',$valtype1).'Single Mail  '.form_radio('mbc','1',$valtype2).'Broadcast Mail<br/>
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
						'placeholder'=>'Mail Recipient',
						'required'=>'required',
						'size'=>'100',
						'style'=>'height:auto;min-height:30px;width:80%;',
						'value'=>isset($tempfilter['mto']) ? $tempfilter['mto'] : null,
						'class'=>'form-control'));
		
		$adv['Subject'] = form_input(
						array('name'=>'msub',
						'id'=>'msub',
						'placeholder'=>'Subject Mail',
						'size'=>'112',
						'style'=>'height:auto;min-height:30px;width:80%;',
						'required'=>'required',
						'value'=>$valsub,
						'class'=>'form-control'));
		
		$adv['Alias'] = form_input(
						array('name'=>'malias',
						'id'=>'mali',
						'placeholder'=>'Sender Name',
						'size'=>'112',
						'style'=>'height:auto;min-height:30px;width:80%;',
						'required'=>'required',
						'value'=>$valalias,
						'class'=>'form-control'));
					
		$adv['CC'] = form_input(
						array('name'=>'mcc',
						'id'=>'mcc',
						'size'=>'100',
						'style'=>'height:auto;min-height:30px;width:80%;',
						'placeholder'=>'CC Mail',
						'value'=>isset($tempfilter['mcc']) ? $tempfilter['mcc'] : null,
						'class'=>'form-control'));
		$data['attach']= form_upload(array('name'=>'mattach[]',
						'id'=>'attach',
						'multiple'=>'multiple',
						'class'=>'btn btn-default'));
		$data['listto']=form_hidden('flistto');
		$data['nameto']=form_hidden('fnameto');
		$data['ccto']=form_hidden('fccto');
		$data['code']=form_hidden('fcode',$valcode);
		$data['listfile']=form_hidden('flistfile');
		$data['cancelfile']=form_hidden('fcancelfile');
		$data['redi']=form_hidden('fredi',1);
		$data['att']=form_hidden('fatt',1);
		
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
		
		$sender = $this->Msetting->getset('sendermail');
		(''!=$sender) ? $val = true: $val=false;
		
		$data['sender']= $sender;
		
		$data['footer'] = html_entity_decode($this->Msetting->getset('mailfooter'));
		
		$data['usefoo'] = form_checkbox(array(
							'name'=>'fusefoo',
							'id'=>'usefoo',
							'checked'=>isset($valusefooter) ? $valusefooter : false,
							'value'=>'1')
							);
		
		$data['status']=form_checkbox(array(
							'name'=>'fallow',
							'data-toggle'=>'toggle',
							'disabled'=>'disabled',
							'data-on'=>'Configured',
							'data-off'=>'Not Configured Yet',
							'data-width'=>200,
							'data-height'=>35,
							'data-onstyle'=>'primary',
							'data-offstyle'=>'danger',
							'id'=>'senderstat',
							'checked'=>$val,
							'value'=>'1')
							);
		$data['fsendper'] = site_url('Organizer/Mailbroadcast/savesetting');
		
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter .='<div class="row"><div class="col-md-1"><label>'.$a.'</label></div><div class="col-md-11"><span>'.$v.'</span></div></div>';
		}
		$data['metadata'] = $dtfilter;
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'tokchi/tokchi','summernote/summernote','ajaxupload/jquery.knob','ajaxupload/jquery.ui.widget','ajaxupload/jquery.iframe-transport','ajaxupload/jquery.fileupload','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'tokchi/tokchi','summernote/summernote','ajaxupload/style','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Compose Mail";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/bcmail/compose', $data, TRUE);
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
				$r = $this->Mbc->deletebc($v);
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Broadcast Mail success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, Delete Selected Broadcast Mail Failed.');
		}
		redirect(base_url('Organizer/Mailbroadcast'));
	}
		
	public function sendmail(){
	$this->load->library('MY_Input');
	$to = $this->input->post('flistto');
	$fatt = $this->input->post('fatt');
	$sub = $this->input->post('msub');
	$redi = $this->input->post('fredi');
		$files = $_FILES['mattach'];
	if(($files!=null) and($fatt==1)){	
			$file['file']= array(
				'name'=>$files['name'][0],
				'type'=>$files['type'][0],
				'tmp_name'=>$files['tmp_name'][0],
				'error'=>$files['error'][0],
				'size'=>$files['size'][0]
			);
			$name[] = $this->uploadfile($file);
		
	} else if ((null!=$to) and (null!=$sub)){
		//filter file uploaded
		$arrdiff = array_diff(explode(',',$this->input->post('flistfile')),	explode(',',$this->input->post('fcancelfile')));
		//specify broadcast type
		$target='';$year='';$lvl='';$stat='';
		if ($this->input->post('mbc')==1) {
			$bctype="Broadcast Mail";
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
			$bctype="Single Mail";
			}
		
		($this->input->post('fusefoo')!='') ? $usefoo = true : $usefoo = false; 
		
		$fdata = array (
				'bcdate' => date("Y-m-d H:i:s"),
				'bcrecipient' => $to,
				'uuser' => $this->session->userdata('user'),
				'bcfrom' => $this->input->post('malias'),
				'bcmailfrom' => $this->Msetting->getset('sendermail'),
				'bctitle' => $this->input->post('msub'),
				'bccontent' => htmlentities($this->input->post('fcode',false)),
				'bcattach' => implode($arrdiff,','),
				'bcket' => 'Type: '.$bctype.'; CC: '.$this->input->post('fccto').'; Role: '.$target.'; Period: '.$year.'; Level: '.$lvl.'; Payment: '.$stat,
				'bcfooter' => $usefoo,
				'bctype' => 'Mail'
				);
		$r = $this->Mbc->savebc($fdata);
		$arrto = explode(',',$to);
		$tot = 0;
		$failed = '';
		
		
		
		($this->input->post('fusefoo')=='1') ? $fcode = html_entity_decode($fdata['bccontent'].'<br/>'.$this->Msetting->getset('mailfooter')) : $fcode = html_entity_decode($fdata['bccontent']);
		
		$this->load->library('Convertcode');
		foreach($arrto as $k=>$v){
			//====== decode message ============
			$decode = $this->convertcode->decodemailmsg($fcode,$v);	
		
			//================= gmail send ===========
			$ret = $this->gmail->sendmail($v,$this->input->post('fccto'),$fdata['bctitle'],$fdata['bcfrom'],$decode,$arrdiff);		
			($ret) ? $tot++:$failed[]=($k+1).'. '.$v;

		}
		
		$this->load->library('upload');
		if((''!=$this->input->post('flistfile')) and ($redi==null))
		{
			$filename = explode(',',$this->input->post('flistfile'));
			foreach($filename as $k=>$v){
			$path = FCPATH.'temp_upload/' . $filename[$k];
            unlink($path);
			}
		}
		if ($r){
			$this->session->set_flashdata('v','Send Mail Success');
			} else {		
			$this->session->set_flashdata('x','Send Mail Failed');
			}
			(null==$redi) ? redirect(base_url('Organizer/Mailbroadcast')) : redirect(base_url('Organizer/Mailbroadcast/composemail'));
		} else {
			$this->session->set_flashdata('x','Send Mail Failed');
			redirect(base_url('Organizer/Mailbroadcast/composemail'));
		}
	}

	public function deletemail(){
		$id = $this->input->get('id');
		$r = $this->Mbc->deletebc($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Mail Broadcast Success');
		} else{
		$this->session->set_flashdata('x','Delete Mail Broadcast Failed');
		} 
		redirect(base_url('Organizer/Mailbroadcast'));
	}

	public function printbcmail(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['bcdate','bctitle','bcrecipient','bcmailfrom','bcfrom','bccontent','bcattach','bcket','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mbc->exportbcmail($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mbc->exportbcmail(null,null,$dtcol);
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
					(array_key_exists('bccontent',$val)) ? $dexp[$key]['bccontent']=strip_tags(html_entity_decode($val['bccontent'])): null;
					(array_key_exists('bcrecipient',$val)) ? $dexp[$key]['bcrecipient']=str_replace(",","<br/>",$val['bcrecipient']): null;
					(array_key_exists('bcket',$val)) ? $dexp[$key]['bcket']=str_replace(";","<br/>",$val['bcket']): null;
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Mail Broadcast Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/bcmail/printbcmail', $data);
		
	}
	
	public function getcontactlist(){
		$frole= $this->input->post('role');
		$fyear= $this->input->post('period');
		$fstat= $this->input->post('lunas');
		$flevel= $this->input->post('level');
		$temp = $this->Mbc->getcontact($frole,$fyear,$fstat,$flevel);
		
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
	
	public function savesetting(){
		if(null!= $this->input->post('fcode')){
		$this->load->library('MY_Input');
		$dtset=array(
				'mailfooter'=>htmlentities($this->input->post('fcode',false)));
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Mail Footer Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Mail Footer Failed.");
		}
		redirect(base_url('Organizer/Mailbroadcast'));
	}
	
	public function returncolomn($header) {
	$find=['bcdate','bctitle','bcrecipient','bcmailfrom','bccontent','bcfrom','bcket','uname'];
	$replace = ['Mail Created','Mail Subject','Mail Recipient','Mail Sender','Mail Content','Alias','Details','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
	
}
