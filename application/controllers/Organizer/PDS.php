<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PDS extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mpds','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['ucreated','uuser','uname','unim','uhp','ustatus','ulunas'];
		$header = $this->returncolomn($column);
		unset($header[1]);
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
			$rows = $this->Mpds->countpds($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mpds->countpds();	
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
	
		$temp = $this->Mpds->datapds($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				if($value['ulunas']){
					$temp[$key]['ulunas']='<span class="label label-success">Fully Paid</span>';
				} else{
					$temp[$key]['ulunas']='<span class="label label-warning">Not yet</span>';
				}
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['uuser']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['uuser']='<span class="idname">'.$temp[$key]['uuser'].'</span>';
				$temp[$key]['ucreated']=date('d-M-Y', strtotime($value['ucreated'])).'<br/>'.date('H:i:s', strtotime($value['ucreated']));
					$txtstatus = str_replace(',', '<br/>', $value['ustatus']);
				$temp[$key]['ustatus'] = $txtstatus; 
				//manipulation menu
				$enc = $value['uuser'];
				unset($temp[$key]['uuser']);
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/PDS/detailpds?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/PDS/editpds?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/PDS/deletepds?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$regist = $this->Msetting->getset('registphase');
			$data['fregist']= form_input(array('id'=>'registrange',
								'class'=>'form-control',							
								'style'=>'width:200px',							
								'name'=>'fregistphase',							
								'placeholder'=>'Registration Phase',							
								'value'=>$regist,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/PDS/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','inputmask/inputmask.numeric.extensions');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/pds/pdslist', $data, TRUE);
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
	
	public function detailpds(){
		//fecth data from db
		$col = ['ufoto','ucreated','uupdate','uuser','uname','jkname','ubplace','ubdate','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','umin','ustatus','ulunas'];
		$id = $this->input->get('id');
		$dbres = $this->Mpds->detailpds($col,$id);
		
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
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
			if (($key=='Photo') and ($dbres[0][$col[$a]]=='')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <img src="'.base_url('upload/foto/avatar.png').'" class="img-thumbnail" style="height:100px" align="center">'
						);
					} else if (($key=='Photo') and ($dbres[0][$col[$a]]<>'')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <img src="'.base_url('upload/foto/'.$dbres[0][$col[$a]].'').'" class="img-thumbnail" style="height:100px" align="center">'
						);
					}
			if (($key=='Birthdate')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.date('d-M-Y',strtotime($dbres[0][$col[$a]]))
						);
					}
			
			if (($key=='Full Payment') and ($dbres[0][$col[$a]]=='1')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-success">Fully Paid</span>'
						);
					} else if (($key=='Full Payment') and ($dbres[0][$col[$a]]=='0')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : <span class="label label-warning">Not Yet</span>'
						);
					}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/pds/detailpds', $data);
		
		
	}
	
	public function addpds(){
		//============ form add pds available account ===========
		{	$optuname = $this->Mpds->optuser();
		$funame = array('name'=>'fpilusername',
						'id'=>'Username',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fusername'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($funame,$optuname,'');
		
		$fname = array('name'=>'ffullname',
						'id'=>'inpname',
						'required'=>'required',
						'placeholder'=>'Full Name',
						'value'=>set_value('ffullname'),
						'class'=>'form-control');
		$r[] = form_input($fname);
		
			$optjk = $this->Mpds->optjk();
		$fjk = array('name'=>'fjk',
						'id'=>'Gender',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fjk'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($fjk,$optjk,'');
		
		$fnim = array('name'=>'fnim',
						'id'=>'NIM',
						'required'=>'required',
						'placeholder'=>'NIM',
						'value'=>set_value('fnim'),
						'class'=>'form-control',
						'disabled'=>'disabled',
						'size'=>'13');
		$r[] = form_input($fnim);
		
			$optfac = $this->Mpds->optfac();
		$ffac = array('name'=>'ffaculty',
						'id'=>'Faculty',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('ffaculty'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ffac,$optfac,'');
		
			$fbplace = array('name'=>'fbplace',
						'id'=>'bplace',
						'required'=>'required',
						'placeholder'=>'Birthplace',
						'value'=>set_value('fbplace'),
						'class'=>'form-control');
		$r[] = form_input($fbplace);
		
			$fbdate = array('name'=>'fbdate',
						'id'=>'bdate',
						'placeholder'=>'Birthdate format (dd-mm-yyyy), eg: 16-09-1996',
						'value'=>set_value('fbd'),
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'datamask' => ''
						);
		$r[] = form_input($fbdate);
		
			$femail = array('name'=>'femail',
						'id'=>'inpemail',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>set_value('femail'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($femail).'<span style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
			
			$fhp = array('name'=>'fhp',
						'id'=>'inphp',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>set_value('fhp'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fhp);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'SocialMedia',
						'placeholder'=>'Social Media',
						'value'=>set_value('fsocmed'),
						'class'=>'form-control',
						'size'=>'30');
		$r[] = form_input($fsoc);
		
		$faddrnow = array('name'=>'faddrnow',
						'id'=>'addresnow',
						'placeholder'=>'Current Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrnow,set_value('faddrnow'));
		
		$faddrhome = array('name'=>'faddrhome',
						'id'=>'addreshome',
						'placeholder'=>'Home Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrhome,set_value('faddrhome'));
		}
		{
		//============ form add pds with account ===========
		
		$fname['id'] = 'Fullname';
		$r2[] = form_input($fname);

		unset($fnim['disabled']);$fnim['id']='username2';
		$fnim['name']='fusername';
		$r2[] = form_input($fnim).'<span id="usuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Username Available</span><span id="ufailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Username Not Available</span>';
		
		$femail['id']='Email';
		$r2[] = form_input($femail).'<span style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
		
		$r2[] = form_dropdown($fjk,$optjk,'');
		
		$r2[] = form_dropdown($ffac,$optfac,'');
		
		$fbplace['id'] ='bplace2';
		$r2[] = form_input($fbplace);
		
		$fbdate['id'] = 'bdate';
		$r2[] = form_input($fbdate);
		
		$fhp['id'] = 'fhp';
		$r2[] = form_input($fhp);
		
		$r2[] = form_input($fsoc);
		
		$r2[] = form_textarea($faddrnow,set_value('faddrnow'));
		
		$r2[] = form_textarea($faddrhome,set_value('faddrhome'));
		
		//set row title
		$col2 = ['uname','unim','uemail','jkname','fname','Birthplace','Birthdate','uhp','ubbm','uaddrnow','uaddhome'];
		$row2 = $this->returncolomn($col2);
		
		}
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['uuser','uname','jkname','unim','fname','Birthplace','Birthdate','uemail','uhp','ubbm','uaddrnow','uaddhome'];
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
		
		$this->table->clear();
		
		//=========== generate add form new user =========================
		$this->table->set_template($tmpl);
		$a = 0;
		foreach($row2 as $key)
		{
			$dtable2[$a] = array(
					"dtcol"=>'<div class="form-group"><label for="l'.$key.'" class="col-sm-3 control-label"><b>'.$key.'</b></label>',
					"dtval"=>'<div class="col-sm-9">'.$r2[$a].'</div></div>'
					);
			$a++;
		}
		$data['r2data']=$this->table->generate($dtable2);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Add Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/pds/addpds', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function editpds(){					
		// ============== Fetch data ============
		$col = ['ucreated','uuser','uname','user.idjk','unim','user.idfac','ubplace','ubdate','uemail','upass','uhp','ubbm','uaddrnow','uaddhome','ustatus'];
		$id = $this->input->get('id');
		$g = $this->Mpds->detailpds($col,$id);
		$stat = $g[0]['ustatus'];
		unset($col[14]);
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.$g[0]['ucreated'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['uuser'].'</label>';
			
		$ffname = array('name'=>'ffullname',
						'id'=>'Fullname',
						'required'=>'required',
						'placeholder'=>'Fullname',
						'value'=>$g[0]['uname'],
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($ffname);
		
		$optjk = $this->Mpds->optjk();
		$fjk = array('name'=>'fjk',
						'id'=>'Gender',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('fjk'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($fjk,$optjk,$g[0]['idjk']);
		
		$fnim = array('name'=>'fnim',
						'id'=>'NIM',
						'required'=>'required',
						'placeholder'=>'NIM',
						'value'=>$g[0]['unim'],
						'class'=>'form-control',
						'size'=>'13');
		$r[] = form_input($fnim);
		
			$optfac = $this->Mpds->optfac();
		$ffac = array('name'=>'ffaculty',
						'id'=>'Faculty',
						'required'=>'required',
						'data-live-search'=>'true',
						'value'=>set_value('ffaculty'),
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($ffac,$optfac,$g[0]['idfac']);
		
		$fbplace = array('name'=>'fbplace',
						'id'=>'bplace',
						'required'=>'required',
						'placeholder'=>'Birthplace',
						'value'=>$g[0]['ubplace'],
						'class'=>'form-control');
		$r[] = form_input($fbplace);
		
			$fbdate = array('name'=>'fbdate',
						'id'=>'bdate',
						'placeholder'=>'Birthdate format (dd-mm-yyyy), eg: 22-12-1996',
						'value'=>$g[0]['ubdate'],
						'class'=>'form-control',
						'type'=>'text',
						'size'=>'10',
						'required'=>'required',
						'data-inputmask' => "'alias': 'dd-mm-yyyy'",
						'datamask' => ''
						);
		$r[] = form_input($fbdate);
		
		$femail = array('name'=>'femail',
						'id'=>'Email',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>$g[0]['uemail'],
						'class'=>'form-control',
						'size'=>'50'
						);
		$r[] = form_input($femail).'<span id="valsuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span id="valfailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
		
		$fpass = array('name'=>'fpass',
						'id'=>'password',
						'placeholder'=>'New Password',
						'value'=>'',
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fpass).'<span class="text-danger"><i class="fa fa-exclamation"></i> Let it blank, to keep old password.</span>';
		
		$fhp = array('name'=>'fhp',
						'id'=>'return',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>$g[0]['uhp'],
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fhp);
		
		$fsoc = array('name'=>'fsocmed',
						'id'=>'SocialMedia',
						'placeholder'=>'Social Media',
						'value'=>$g[0]['ubbm'],
						'class'=>'form-control',
						'size'=>'30');
		$r[] = form_input($fsoc);
		$faddrnow = array('name'=>'faddrnow',
						'id'=>'addresnow',
						'placeholder'=>'Current Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrnow,$g[0]['uaddrnow']);
		
		$faddrhome = array('name'=>'faddrhome',
						'id'=>'addreshome',
						'placeholder'=>'Home Address',
						'rows'=>'5',
						'class'=>'form-control');
		$r[] = form_textarea($faddrhome,$g[0]['uaddhome']);
		
		$data['inid'] = form_hidden('fuser',$g[0]['uuser']);
		$data['inst'] = form_hidden('fstats',$stat);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($col);
		$row[3]="Gender";
		$row[5]="Faculty";
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
		
		$this->load->view('dashboard/org/pds/editpds', $data);
	
	
	}
	
	public function updatepds(){
		if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => $this->input->post('fstats')
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
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => date("Y-m-d",strtotime($this->input->post('fbdate'))),
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
		$mem = $this->input->post('fusername');
		$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uupdate' => date("Y-m-d H:i:s"),
					'uuser' => $mem,
					'uname' => $this->input->post('ffullname'),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fusername'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => date("Y-m-d",strtotime($this->input->post('fbdate'))),
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
		if ($r){
			//======= set notif to org===========
			$idnotif = $this->Msetting->getset('notifnewsignup');
			$this->notifications->pushNotifToOrg(array('idnotif'=>$idnotif,'uuser'=>$mem,'nlink'=>base_url('Organizer/PDS')));

			//======= set notif to member ========
			$idnotifmem = $this->Msetting->getset('notifregistsuccess');
			$this->notifications->pushnotif(array('idnotif'=>$idnotifmem,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$mem,'nlink'=>null));
		}
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
		$dtset=array(
				'registphase'=>$dtrange
				);
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Range Date Registration Phase Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Range Date Registration Phase Failed.");
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	public function returncolomn($header) {
	$find=['ucreated','uupdate','uuser','uname','jkname','idjk','ubplace','ubdate','unim','uemail','uhp','ufoto','ubbm','uaddrnow','uaddhome','umin','upaycode','ustatus','ulunas','fac.idfac','idfac','fname','uallow','upass'];
	$replace = ['Date Registered','Last Updated','Username','Full Name','Gender','Gender','Birthplace','Birthdate','NIM','Email','Phone Number','Photo','Social Media','Current Address','Home Address','Member Index Number','Payment','Status','Full Payment','Faculty','Faculty','Faculty','Allow/Deny','Password'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
