<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Result extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mresult','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idresult','jdate','tname','a.uname as mem','a.unim','q_tmpscore','lvlabre','q_score','b.uname as org'];
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
			$rows = $this->Mresult->countresult($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mresult->countresult();	
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
						'ucreated' => 'Date Registered',
						'uname' => 'Full Name',
						'unim' => 'NIM',
						'uhp' => 'Email',
						'ustatus' => 'Status'
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
		$adv['Registered'] = form_input(
						array('name'=>'ucreated',
						'id'=>'createdon',
						'placeholder'=>'Date Created',
						'value'=>isset($tempfilter['ucreated']) ? $tempfilter['ucreated'] : null,
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
						
		$adv['Phone Number'] = form_input(
						array('name'=>'uhp',
						'id'=>'hp',
						'placeholder'=>'Phone Number',
						'value'=>isset($tempfilter['uhp']) ? $tempfilter['uhp'] : null,
						'class'=>'form-control'));
		
		$adv['Status'] = form_input(
						array('name'=>'ustatus',
						'id'=>'status',
						'placeholder'=>'Status',
						'value'=>isset($tempfilter['ustatus']) ? $tempfilter['ustatus'] : null,
						'class'=>'form-control'));
		
		$adv['Fully Paid/Not'] = form_dropdown(array(
							'name'=>'ulunas',
							'id'=>'lunas',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Fully Paid',
							'0'=>'Not Yet'),isset($tempfilter['ulunas']) ? $tempfilter['ulunas'] : null);
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/PDS?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/PDS?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mresult->dataresult($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation level data
				if($value['lvlabre']==null){
					$temp[$key]['lvlabre']='<span class="label label-warning">Not Determined Yet</span>';
				} else{
					$temp[$key]['lvlabre']=$value['lvlabre'];
				}
				//manipulation score data
				if($value['q_score']==null){
					$temp[$key]['q_score']='<span class="label label-warning">Not Assessed Yet</span>';
				} else{
					$temp[$key]['q_score']=$value['q_score'];
				}
				//manipulation assesor data
				if($value['org']==null){
					$temp[$key]['org']='<span class="label label-warning">No Assessor</span>';
				} else{
					$temp[$key]['org']=$value['org'];
				}
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idresult']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['mem']='<span class="idname">'.$temp[$key]['mem'].'</span>';
				
				$temp[$key]['jdate']=date('d-M-Y', strtotime($value['jdate']));
				//manipulation menu
				$enc = $value['idresult'];
				unset($temp[$key]['idresult']);
				$temp[$key]['menu']='<div class="btn-group-vertical"><a href="'.base_url('Organizer/Result/detailresult?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-default btn-sm"><i class="fa fa-list-alt"></i> Details</a>'.
				'<a href="'.base_url('Organizer/Result/assessresult?id=').$enc.'" alt="Asess Data" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Asess</a>'.
				'<a href="#" data-href="'.base_url('Organizer/Result/deleteresult?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Delete</a></div>';
				}
		$data['listlogin'] = $this->table->generate($temp);
		
		// ======== activate/deactivate account ==============
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
			$data['factselected'] = site_url('Organizer/PDS/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/PDS/importxls');
			
			
		
		// ============= export form ==============
			$optcol = array(
						'ucreated'=>'Date Created',
						'uname' => 'Name',
						'jkname' => 'Gender',
						'ubplace' => 'Birthplace',
						'ubdate' => 'Birthdate',
						'unim' => 'NIM',
						'fname' => 'Faculty',
						'uemail' => 'Email',
						'uhp' => 'Phone Number',
						'ubbm' => 'Social Media',
						'uaddrnow' => 'Recent Address',
						'uaddhome' => 'Home Address',
						'umin' => 'Member Index Number',
						'ustatus' => 'Status',
						'ulunas' => 'Fully Paid/Not'
						);
			$data['fcol']= form_dropdown(array('name'=>'fcolomn[]',
							'class'=>'form-control selectcol',
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
			$data['factexp'] = site_url('Organizer/PDS/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/PDS/printpds');
		
		//=============== setting registration phase ============
			$start = $this->Msetting->getset('beginregist');
			$end = $this->Msetting->getset('endregist');
			$data['fregist']= form_input(array('id'=>'registrange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fregistphase',							
								'placeholder'=>'Registration Phase',							
								'value'=>$start.' - '.$end,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/PDS/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Test Result";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/result/resultlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '100000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatRegistrationData.xls')) {
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
					$dtdate = \DateTime::createFromFormat('dmY',$objWorksheet->getCell('G'.($i+1)))->format('Y-m-d');
                   $dtxl[$i-1]['uname'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['uuser'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['unim'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['uemail'] = $objWorksheet->getCell('C'.($i+1))->getValue();
                   $dtxl[$i-1]['idjk'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['idfac'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['ubplace'] = $objWorksheet->getCell('F'.($i+1))->getValue();
                   $dtxl[$i-1]['upass'] = md5(date('dmY',strtotime($dtdate)));
                   $dtxl[$i-1]['ubdate'] = date('Y-m-d',strtotime($dtdate));
                   $dtxl[$i-1]['uhp'] = $objWorksheet->getCell('H'.($i+1))->getValue();
                   $dtxl[$i-1]['ubbm'] = $objWorksheet->getCell('I'.($i+1))->getValue();
                   $dtxl[$i-1]['uaddrnow'] = $objWorksheet->getCell('J'.($i+1))->getValue();
                   $dtxl[$i-1]['uaddhome'] = $objWorksheet->getCell('K'.($i+1))->getValue();
                   $dtxl[$i-1]['uallow'] = '1';
                   $dtxl[$i-1]['ustatus'] = 'Registered';
                   $dtxl[$i-1]['idrole'] = '3';
				 }
              }
			  
			  //save data through model
			  $report = $this->Mpds->importdata($dtxl);
 
              
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
			redirect(base_url('Organizer/PDS'));
        
    }
	
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['ucreated','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','umin','ustatus','ulunas'];
		}
		
		//check use date range
		$dexp = array();
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpds->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpds->exportlogin(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Login Data');
	
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
			foreach($dexp as $key=>$val){
					$Dcol = "A";
				//manipulate data
				if(array_key_exists('ulunas',$val)){
							if ($val['ulunas']==1){
							$val['ulunas']='Fully Paid';
							}else{
							$val['ulunas']='Not Yet';
							}
				}
					foreach ($val as $k){
						$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,$k);
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'REGISTRATION DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Registration Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Fullname','NIM','Email','Gender','Faculty','Birthplace','Birthdate(ddmmyyyy)','Phone Number','Social Media','Current Address','Home Address']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportRegistrationData');
		
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
		
		//set colomn header Faculty Data
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Faculty Data");			
		$objPHPExcel->getActiveSheet()->mergeCells($tempcol.($Hrow).':'.chr(ord($Dcol)+1).$Hrow);
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow+1),"IDFaculty");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow+1),"Value");			
			$dtfac= $this->Mpds->getallfac();
		$Facrow=$Hrow+2;
		foreach ($dtfac as $k=>$v){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Facrow,$v['idfac']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Facrow,$v['fname']);
			$Facrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Facrow-1),"Remember: Always Put 'IDFaculty' instead of 'Value' in Colomn 'Faculty'");			
		
		//set colomn header Gender Data
		$Grow = $Facrow+1;
		$Gcol = $tempcol;
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow),"Gender Data");			
		$objPHPExcel->getActiveSheet()->getStyle($Gcol.$Grow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->mergeCells($Gcol.($Grow).':'.chr(ord($Gcol)+1).$Grow);
		$objPHPExcel->getActiveSheet()->setCellValue($Gcol.($Grow+1),"IDGender");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).($Grow+1),"Value");			
			$dtjk= $this->Mpds->getalljk();
		$Grow = $Grow+2;
		foreach ($dtjk as $k=>$v){
			$Gcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Gcol.$Grow,$v['idjk']);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Gcol)+1).$Grow,$v['jkname']);
			$Grow++;
		}
		$lastcol = chr(ord($Gcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Grow-1),"Remember: Always Put 'IDGender' instead of 'Value' in Colomn 'Gender'");			
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.chr(ord($Hcol)-1).'11')->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Hrow).':'.chr(ord($lastcol)-1).($Facrow-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Facrow+1).':'.chr(ord($lastcol)-1).($Grow-1))->applyFromArray($styleArray);
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Grow-1))->applyFromArray($fillArray);
		
		//Freeze pane
		//$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Grow));
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatRegistrationData.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function detailresult(){
		$this->load->model('Mq');
		//detail test result
		$col = ['jdate','jstart','jsesi','tname','tduration','jroom','a.uname as mem','b.uname as org','q_randcode','q_tmpscore','q_randquest','jactive'];
		$id = $this->input->get('id');
		$dbres = $this->Mresult->detailresult($col,$id);
		$data['t'] = $dbres[0];

		//detail all question and answers taken
		$arrq = explode(',',$dbres[0]['q_randquest']);
		$colq = ['question','subject','question.idsubject','q_bundle','qtype.idqtype','qmanual'];
		foreach ($arrq as $k => $v) {
			$arrdetailq = $this->Mq->getmyquestdetail($colq,$v)[0];

			//get all question
			$finalquestion[$k]['idq'] = $v;
			$finalquestion[$k]['question'] = $arrdetailq->question;
			$finalquestion[$k]['subject'] = $arrdetailq->subject;
			$finalquestion[$k]['idqtype'] = $arrdetailq->idqtype;
			$finalquestion[$k]['qmanual'] = $arrdetailq->qmanual;

			//get all answer
			$tmparridanswer = $this->Mq->getrandanswer($v,$id);
				$arridanswer = ($tmparridanswer<>'') ? explode(',',$tmparridanswer) : array();
			$answer = array();
				foreach ($arridanswer as $a => $b) {
					$currentanswer = $this->Mq->getanswer(array('idans','answer','key_ans'),$b)[0];
					$answer[$a]['idans'] = $currentanswer['idans'];
					$answer[$a]['answer'] = $currentanswer['answer'];
					$answer[$a]['keyanswer'] = $currentanswer['key_ans'];
					}
				//picked answer if any
				$pickedanswer = $this->Mq->getpickedanswer($v,$id);
				$mark = $this->Mq->getmarkanswer($v,$id);
			
			$finalquestion[$k]['pickedanswer'] = $pickedanswer;	
			$finalquestion[$k]['allanswer'] = $answer;
			$finalquestion[$k]['answermark'] = $mark;

		}
		$data['generatedq'] = $finalquestion;

		
		// =============== view handler ============
		$this->load->view('dashboard/org/result/detailresult', $data);
		
		
	}
	
	
	public function assessresult(){					
		$this->load->model('Mq');
		//detail test result
		$col = ['jdate','jstart','jsesi','tname','tduration','jroom','a.uname as mem','b.uname as org','q_randcode','q_tmpscore','q_randquest','jactive'];
		$id = $this->input->get('id');
		$dbres = $this->Mresult->detailresult($col,$id);
		$data['t'] = $dbres[0];

		//detail all question and answers taken
		$arrq = explode(',',$dbres[0]['q_randquest']);
		$colq = ['question','subject','question.idsubject','q_bundle','qtype.idqtype','qmanual'];
		foreach ($arrq as $k => $v) {
			$arrdetailq = $this->Mq->getmyquestdetail($colq,$v)[0];

			//get all question
			$finalquestion[$k]['idq'] = $v;
			$finalquestion[$k]['question'] = $arrdetailq->question;
			$finalquestion[$k]['subject'] = $arrdetailq->subject;
			$finalquestion[$k]['idqtype'] = $arrdetailq->idqtype;
			$finalquestion[$k]['qmanual'] = $arrdetailq->qmanual;

			//get all answer
			$tmparridanswer = $this->Mq->getrandanswer($v,$id);
				$arridanswer = ($tmparridanswer<>'') ? explode(',',$tmparridanswer) : array();
			$answer = array();
				foreach ($arridanswer as $a => $b) {
					$currentanswer = $this->Mq->getanswer(array('idans','answer','key_ans'),$b)[0];
					$answer[$a]['idans'] = $currentanswer['idans'];
					$answer[$a]['answer'] = $currentanswer['answer'];
					$answer[$a]['keyanswer'] = $currentanswer['key_ans'];
					}
				//picked answer if any
				$pickedanswer = $this->Mq->getpickedanswer($v,$id);
				$mark = $this->Mq->getmarkanswer($v,$id);
			
			$finalquestion[$k]['pickedanswer'] = $pickedanswer;	
			$finalquestion[$k]['allanswer'] = $answer;
			$finalquestion[$k]['answermark'] = $mark;

		}
		$data['generatedq'] = $finalquestion;

		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		//=============== Template ============
		$data['jsFiles'] = array(
							'slider-range/bootstrap-slider');
		$data['cssFiles'] = array(
							'slider-range/bootstrap-slider');  
		// =============== view handler ============
		$data['title']="Asess Test Result";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/result/assess', $data, TRUE);
		$this->load->view ('template/main', $data);
	
	
	}
	
	public function updatepds(){
		if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => $this->input->post('fstats'),
					'ulunas' => '0'
					);
		$r = $this->Mpds->updatepds($fdata,$us);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			$r = $this->Mlogin->updateselected($dtuser,$type);
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Member Account success.<br/>Details: '.$r['v'].' success and '.$r['x'].' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Member Account Failed.');
		}
		redirect(base_url('Organizer/PDS'));
	}
		
	public function savepds(){
	
	// separation save method
	if ($this->input->post('fpilusername')!=null){
		$us = $this->input->post('fpilusername');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => 'Registered',
					'ulunas' => '0'
					);
		$r = $this->Mpds->updatepds($fdata,$us);
	} else if ($this->input->post('fusername')!=null){
		$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uupdate' => date("Y-m-d H:i:s"),
					'uuser' => $this->input->post('fusername'),
					'upass' => md5(date("dmY",strtotime($this->input->post('fbdate')))),
					'uname' => $this->input->post('ffullname'),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fusername'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => 'Registered',
					'ulunas' => '0',
					'idrole' => '3',
					'uallow' => '1'
					);
		$r = $this->Mpds->addpds($fdata);
	}
		if ($r){
		$this->session->set_flashdata('v','Add Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	
	}

	public function deletepds(){
		$id = $this->input->get('id');
		$r = $this->Mpds->deletepds($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Organizer/PDS'));
	}

	public function printpds(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['ucreated','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','ulunas'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpds->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpds->exportlogin(null,null,$dtcol);
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
					if(array_key_exists('uallow',$val)){
						if ($val['uallow']==1){
						$dexp[$key]['uallow']='Allowed';
						}else{
						$dexp[$key]['uallow']='Denied';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Member Account Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/akun/printacc', $data);
		
	}
	
	public function getdetailuser(){
		$id = $this->input->post('user');
		echo json_encode($this->Mpds->detailuser($id));
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fregistphase')){
			$dtrange = $this->input->post('fregistphase');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
		$dtset=array(
				'beginregist'=>$dtstart,
				'endregist'=>$dtend
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Registration Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Registration Phase Failed.");
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function returncolomn($header) {
	$find=['jdate','tname','a.uname as mem','a.unim','lvlabre','q_tmpscore','q_score','b.uname as org'];
	$replace = ['Date Test','Test Name','Member Name','NIM','Level','Temporary Score','Final Score','Assessor'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
