<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertroman'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mcerti','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['certificate.idcerti','nocerti','certidate','uname','unim','lvlname','cspeak','cread','clisten','cwrite','cgrammar'];
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
			$rows = $this->Mcerti->countcerti($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mcerti->countcerti();	
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
						'ucreated' => 'Period',
						'nocerti' => 'Certificate No',
						'certidate' => 'Date Certificate Created',
						'uname' => 'Full Name',
						'unim' => 'NIM'
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
						'id'=>'createdon',
						'placeholder'=>'Period of Member',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		
		$adv['Certificate No'] = form_input(
						array('name'=>'nocerti',
						'id'=>'certino',
						'placeholder'=>'Certificate Number',
						'value'=>isset($tempfilter['nocerti']) ? $tempfilter['nocerti'] : null,
						'class'=>'form-control'));
						
		$adv['Created'] = form_input(
						array('name'=>'certidate',
						'id'=>'createdon',
						'placeholder'=>'Date Certificate Created',
						'value'=>isset($tempfilter['desdate']) ? $tempfilter['desdate'] : null,
						'class'=>'form-control'));
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'fullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['NIM'] = form_input(
						array('name'=>'unim',
						'id'=>'NIM',
						'placeholder'=>'NIM',
						'value'=>isset($tempfilter['unim']) ? $tempfilter['unim'] : null,
						'class'=>'form-control'));
						
			$optlevel = $this->Mcerti->getoptlevel(); 
		$adv['Level'] = form_dropdown(
						array('name'=>'idlevel',
						'id'=>'Level',
						'class'=>'form-control'),$optlevel,isset($tempfilter['idlevel']) ? $tempfilter['idlevel'] : null);
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Certificate/?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Certificate?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mcerti->datacerti($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idcerti']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['uname']='<span class="idname">'.$temp[$key]['uname'].'</span>';
				$temp[$key]['certidate']=date('d-M-Y',strtotime($temp[$key]['certidate'])).'<br/>'.date('H:i:s',strtotime($temp[$key]['certidate']));
				//manipulation menu
				$enc = $value['idcerti'];
				unset($temp[$key]['idcerti']);
				$temp[$key]['menu']='<div class="btn-group-vertical"><a href="'.base_url('Organizer/Certificate/printcerti?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> </a>'.
				'<a href="'.base_url('Organizer/Certificate/editcerti?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Certificate/deletecerti?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Certificate/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Certificate/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'nocerti' => 'Certificate No',
						'certidate' => 'Date Certificate Created',
						'uname' => 'Full Name',
						'unim' => 'NIM',
						'lvlname' => 'Level',
						'cspeak' => 'Speaking',
						'cread' => 'Reading',
						'clisten' => 'Listening',
						'cwrite' => 'Writing',
						'cgrammar' => 'Grammar'
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
			$data['factexp'] = site_url('Organizer/Certificate/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Certificate/printcerti');
		
		//=============== setting certificate ============
			$format = $this->Msetting->getset('certiformat');
			$page = $this->Msetting->getset('certipage');
			$data['fformat']= form_input(array('id'=>'setformat',
								'class'=>'form-control',							
								'name'=>'certiformat',							
								'size'=>'40',							
								'placeholder'=>'Certificate Format',							
								'value'=>$format,							
								'required'=>'required'));
				$optpage = $this->Mcerti->getoptpage();
			$data['fpage']= form_dropdown(array('id'=>'setpage',
								'class'=>'form-control',							
								'name'=>'certipage',							
								'placeholder'=>'Certificate Page',							
								'required'=>'required'),$optpage,$page);
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Certificate/savesetting');
			
				
			$data['nocerti'] = $this->generatenocerti('{nocerti}/{date}/{month}/{year}');
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Certificate Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/certi/certilist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatCertificate.xls') ){
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
                   $dtxl[$i-1]['uuser'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['nocerti'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['cspeak'] = $objWorksheet->getCell('C'.($i+1))->getValue();
                   $dtxl[$i-1]['cread'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['clisten'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['cwrite'] = $objWorksheet->getCell('F'.($i+1))->getValue();
                   $dtxl[$i-1]['cgrammar'] = $objWorksheet->getCell('G'.($i+1))->getValue();
				 }
              }
			  
			  //save data through model
			  $report = $this->Mcerti->importdata($dtxl);
 
              
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
		redirect(base_url('Organizer/Certificate'));
        
    }
	
	
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['nocerti','certidate','uname','unim','lvlname','cspeak','cread','clisten','cwrite','cgrammar']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mcerti->exportcerti($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mcerti->exportcerti(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Certificate Data');
	
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'CERTIFICATE DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Certificate Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Username','Certificate Number','Speaking','Reading','Listening','Writing','Grammar']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatCertificate');
		
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
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Username");			
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow),"Details");
		$objPHPExcel->getActiveSheet()->getStyle(chr(ord($tempcol)+1).$Hrow)->getFont()->setBold(true);
		$dtuser= $this->Mcerti->getoptuser();
		unset($dtuser['']);
		$Hrow=$Hrow+1;
		foreach ($dtuser as $al=>$val){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Hrow,$al);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Hrow,$val);
			$Hrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow-1),"Remember: Always Put 'Username' instead of 'Details' in Colomn 'Username'");			
		
		$Nrow= $Hrow+1;
		//set colomn header Allow
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Nrow),"Format Certificate Number");
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Nrow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells($tempcol.$Nrow.':'.chr(ord($tempcol)+1).$Nrow);		
		$Nrow=$Nrow+1;
				$nocerti = $this->generatenocerti();
				$objPHPExcel->getActiveSheet()->setCellValue($tempcol.$Nrow,$nocerti);
				$objPHPExcel->getActiveSheet()->mergeCells($tempcol.$Nrow.':'.chr(ord($tempcol)+1).$Nrow);		
		$Nrow=$Nrow+1;
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Nrow-1),"Remember: Always Use Higher Number in 'Certificate Number' to avoid replication.");			
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($Hrow+1).':'.chr(ord($lastcol)-1).($Nrow-1))->applyFromArray($styleArray);
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'1:'.chr(ord($lastcol)).($Nrow-1))->applyFromArray($fillArray);
		
		//Freeze pane
		$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Hrow+6));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatCertificate.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function detailaccount(){
		//fecth data from db
		$col = ['ucreated','uupdate','uuser','uname','uemail','uhp','ulastip','ulastlog','uallow'];
		$id = $this->input->get('id');
		$dbres = $this->Mlogin->detailacc($col,$id);
		
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
			if ($key<>"Allow"){
				$dtable[$a] = array(
					"dtcol"=>'<b>'.$key.'</b>',
					"dtval"=>' : '.$dbres[0][$col[$a]]
					);
			}
			else if (($key=='Allow') and ($dbres[0][$col[$a]]=='1')){
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : Allowed'
				);
			} else if (($key=='Allow') and ($dbres[0][$col[$a]]=='0')){
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : Denied'
				);
			}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/akun/detailacc', $data);
		
		
	}
	
	public function addcerti(){
		//============ form add account ===========
			$optuser = $this->Mcerti->getoptuser(); 
		$funame = array('name'=>'fuser',
						'id'=>'Username',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker');
		$r[] = form_dropdown($funame,$optuser,set_value('fuser'));
		
		$fnocerti = array('name'=>'fnocerti',
						'id'=>'NoCerti',
						'required'=>'required',
						'placeholder'=>'Certificate Number',
						'value'=>$this->generatenocerti(),
						'class'=>'form-control');
		$r[] = form_input($fnocerti).'<span id="usuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Certificate Number Is Available</span><span id="ufailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Certificate Number Is Not Available</span>';
		
			$optmark=array(''=>'Please Select');
			for($i='A';$i<='E';$i++)
			{$optmark[$i]=$i;} 
		$fspeak = array('name'=>'fspeak',
						'id'=>'Speaking',
						'required'=>'required',
						'placeholder'=>'Speaking Score',
						'class'=>'form-control');
		$r[] = form_dropdown($fspeak,$optmark,set_value('fspeak'));
		
		$fread = array('name'=>'fread',
						'id'=>'Reading',
						'required'=>'required',
						'placeholder'=>'Reading Score',
						'value'=>set_value('fread'),
						'class'=>'form-control'
						);
		$r[] = form_dropdown($fread,$optmark,set_value('fread'));
		
		$flisten = array('name'=>'flisten',
						'id'=>'Listen',
						'required'=>'required',
						'placeholder'=>'Listening Score',
						'value'=>set_value('flisten'),
						'class'=>'form-control');
		$r[] = form_dropdown($flisten,$optmark,set_value('flisten'));
		
		$fwrite = array('name'=>'fwrite',
						'id'=>'Write',
						'required'=>'required',
						'placeholder'=>'Writing Score',
						'value'=>set_value('fwrite'),
						'class'=>'form-control');
		$r[] = form_dropdown($fwrite,$optmark,set_value('fwrite'));
		
		$fgram = array('name'=>'fgram',
						'id'=>'Grammar',
						'required'=>'required',
						'placeholder'=>'Grammar Score',
						'value'=>set_value('fgram'),
						'class'=>'form-control');
		$r[] = form_dropdown($fgram,$optmark,set_value('fgram'));
		
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['uname','nocerti','cspeak','cread','clisten','cwrite','cgrammar'];
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
		
		$this->load->view('dashboard/org/certi/addcerti', $data);
	}
	
	public function editcerti(){					
		// ============== Fetch data ============
		$col = ['certidate','uname','unim','lvlname','nocerti','cspeak','cread','clisten','cwrite','cgrammar'];
		$id = $this->input->get('id');
		$g = $this->Mcerti->detailcerti($col,$id);
		
		// ========= form edit ================ 
		$r[] = '<label class="form-control" disabled>'.date('d-M-Y H:i:s',strtotime($g[0]['certidate'])).'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['uname'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['unim'].'</label>';
		$r[] = '<label class="form-control" disabled>'.$g[0]['lvlname'].'</label>';
			
		$fnocerti = array('name'=>'fnocerti',
						'id'=>'NoCerti',
						'required'=>'required',
						'placeholder'=>'Certificate Number',
						'value'=>$g[0]['nocerti'],
						'class'=>'form-control');
		$r[] = form_input($fnocerti).'<span id="valsuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Certificate Number Is Available</span><span id="valfailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Certificate Number Is Not Available</span>';
		
			$optmark=array(''=>'Please Select');
			for($i='A';$i<='E';$i++)
			{$optmark[$i]=$i;} 
		$fspeak = array('name'=>'fspeak',
						'id'=>'Speaking',
						'required'=>'required',
						'placeholder'=>'Speaking Score',
						'class'=>'form-control');
		$r[] = form_dropdown($fspeak,$optmark,$g[0]['cspeak']);
		
		$fread = array('name'=>'fread',
						'id'=>'Reading',
						'required'=>'required',
						'placeholder'=>'Reading Score',
						'class'=>'form-control'
						);
		$r[] = form_dropdown($fread,$optmark,$g[0]['cread']);
		
		$flisten = array('name'=>'flisten',
						'id'=>'Listen',
						'required'=>'required',
						'placeholder'=>'Listening Score',
						'class'=>'form-control');
		$r[] = form_dropdown($flisten,$optmark,$g[0]['clisten']);
		
		$fwrite = array('name'=>'fwrite',
						'id'=>'Write',
						'required'=>'required',
						'placeholder'=>'Writing Score',
						'class'=>'form-control');
		$r[] = form_dropdown($fwrite,$optmark,$g[0]['cwrite']);
		
		$fgram = array('name'=>'fgram',
						'id'=>'Grammar',
						'required'=>'required',
						'placeholder'=>'Grammar Score',
						'class'=>'form-control');
		$r[] = form_dropdown($fgram,$optmark,$g[0]['cgrammar']);
		
		$data['inid'] = form_hidden('fid',$id);
		$fsend = array(	'id'=>'submit',
						'value'=>'Update',
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
		
		$this->load->view('dashboard/org/certi/editcerti', $data);
	
	
	}
	
	public function updatecerti(){
		$id = $this->input->post('fid');
			$fdata = array (
					'nocerti' => $this->input->post('fnocerti'),
					'cspeak' => $this->input->post('fspeak'),
					'cread' => $this->input->post('fread'),
					'clisten' => $this->input->post('flisten'),
					'cwrite' => $this->input->post('fwrite'),
					'cgrammar' => $this->input->post('fgram')
					);
		$r = $this->Mcerti->updatecerti($fdata,$id);
		if ($r){
		$this->session->set_flashdata('v','Update Certificate Success');
		} else {		
		$this->session->set_flashdata('x','Update Certificate Failed');
		}
		redirect(base_url('Organizer/Certificate'));
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
		redirect(base_url('Organizer/Certificate'));
	}
		
	public function savecerti(){
	$user = $this->input->post('fuser');
	$fdata = array (
					'certidate' => date("Y-m-d H:i:s"),
					'uuser' => $user,
					'nocerti' => $this->input->post('fnocerti'),
					'cspeak' => $this->input->post('fspeak'),
					'cread' => $this->input->post('fread'),
					'clisten' => $this->input->post('flisten'),
					'cwrite' => $this->input->post('fwrite'),
					'cgrammar' => $this->input->post('fgram')
					);
		$r = $this->Mcerti->savecerti($fdata);
			if($r) {
			$idcerti = $this->Mcerti->findidcerti($user);
			$m = $this->Mcerti->updatecertiuser($idcerti,$user);
			}
		if ( $r and $m){
		$this->session->set_flashdata('v','Add Certificate Success');
		} else {		
		$this->session->set_flashdata('x','Add Certificate Failed');
		}
		redirect(base_url('Organizer/Certificate'));
	
	}

	public function deletecerti(){
		$id = $this->input->get('id');
		$r = $this->Mcerti->deletecerti($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Certificate Success');
		} else{
		$this->session->set_flashdata('x','Delete Certificate Failed');
		} 
		redirect(base_url('Organizer/Certificate'));
	}

	public function printcerti(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['nocerti','certidate','uname','unim','lvlname','cspeak','cread','clisten','cwrite','cgrammar'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mcerti->exportcerti($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mcerti->exportcerti(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		// config table
		$header = $this->returncolomn($dtcol);
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
				//foreach($dexp as $key=>$val){
					//manipulation allow data
					
				//}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Member Account Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/akun/printacc', $data);
		
	}

	public function preview(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['certificate.idcerti as idcer','nocerti','uname','unim','lvlname'];
		$header = $this->returncolomn($column);
		unset($header[0]);
		
		// checkbox checkalldata
				$checkall = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'form-class',
							'value'=>'all',
							'id'=>'c_all'
							));	
				//array_unshift($header,$checkall);
		//$header[]='Menu';
		$tmpl = array ( 'table_open'  => '<table class="table table-hover header-fixed">','cell_start'=> '<td style="display:block">');
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
			
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 100;
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
			$rows = $this->Mcerti->countcerti($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mcerti->countcerti();	
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
						'ucreated' => 'Period',
						'nocerti' => 'Certificate No',
						'uname' => 'Full Name',
						'unim' => 'NIM'
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
						'id'=>'createdon',
						'placeholder'=>'Period of Member',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		
		$adv['Certificate No'] = form_input(
						array('name'=>'nocerti',
						'id'=>'certino',
						'placeholder'=>'Certificate Number',
						'value'=>isset($tempfilter['nocerti']) ? $tempfilter['nocerti'] : null,
						'class'=>'form-control'));
						
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'fullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['NIM'] = form_input(
						array('name'=>'unim',
						'id'=>'NIM',
						'placeholder'=>'NIM',
						'value'=>isset($tempfilter['unim']) ? $tempfilter['unim'] : null,
						'class'=>'form-control'));
						
			$optlevel = $this->Mcerti->getoptlevel(); 
		$adv['Level'] = form_dropdown(
						array('name'=>'idlevel',
						'id'=>'Level',
						'class'=>'form-control'),$optlevel,isset($tempfilter['idlevel']) ? $tempfilter['idlevel'] : null);
		
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Certificate/preview?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Certificate/preview?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Mcerti->datacerti($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idcer']
							));
				//array_unshift($temp[$key],$ctable);

				//manipulation menu
				$enc = $value['idcer'];

				$temp[$key]['nocerti']='<a href="#" class="prevcerti" data-href="'.base_url().'Organizer/Certificate/previewcerti/'.$enc.'">'.$temp[$key]['nocerti'].'</a>';
				$temp[$key]['uname']='<a href="#" class="prevcerti" data-href="'.base_url().'Organizer/Certificate/previewcerti/'.$enc.'">'.$temp[$key]['uname'].'</a>';
				$temp[$key]['unim']='<a href="#" class="prevcerti" data-href="'.base_url().'Organizer/Certificate/previewcerti/'.$enc.'">'.$temp[$key]['unim'].'</a>';
				$temp[$key]['lvlname']='<a href="#" class="prevcerti" data-href="'.base_url().'Organizer/Certificate/previewcerti/'.$enc.'"">'.$temp[$key]['lvlname'].'</a>';
				
				unset($temp[$key]['idcer']);
				}
		$data['listlogin'] = $this->table->generate($temp);
		
		
		//=============== setting certificate ============
			$data['font'] = $this->Msetting->getset('fontcerti');
			$data['ffont']= form_upload(array('name'=>'ffont',
							'class'=>'btn btn-default btn-sm'));

			$pretext = str_replace('//',"\n",$this->Msetting->getset('pretextcerti'));
			$lvltext = $this->Msetting->getset('leveltextcerti');
			$data['fpretext']= form_textarea(array('id'=>'setpretext',
								'class'=>'form-control',
								'name'=>'fcertitext',
								'value'=>$pretext,
								'rows'=>'2'));
			$data['fleveltext']= form_textarea(array('id'=>'setlvltext',
								'class'=>'form-control',
								'name'=>'fleveltext',
								'value'=>$lvltext,
								'rows'=>'2'));
			
				$arrsize = explode(',', $this->Msetting->getset('sizecerti'));
				$arrmargin = explode(',', $this->Msetting->getset('margincerti'));
				$arrcolor = explode(',', $this->Msetting->getset('colorcerti'));
				$arrcolumn = explode(',', $this->Msetting->getset('columncerti'));
				$arrcenter = explode(',', $this->Msetting->getset('centercerti'));
				$optcolor = array(
							''=>'Please Select',
							'black'=>'Black',
							'green'=>'Green',
							'red'=>'Red',
							'blue'=>'Blue'
							);
				$optcolumn = array(
							''=>'Please Select',
							'1'=>'1',
							'2'=>'2'
							);
				$optcenter = array(
							''=>'Please Select',
							'0' => 'No',
							'1' => 'Yes'
							);
			foreach ($arrsize as $k => $v) {
				$data['fsize'][$k]= form_input(array('id'=>'setsize',
								'class'=>'form-control',					
								'name'=>'ftextsize[]',						
								'placeholder'=>'Font Size',					
								'value'=>$v,
								'size'=>2,							
								'required'=>'required'));
			$data['fmargin'][$k]= form_input(array('id'=>'setmargin',
								'class'=>'form-control',					
								'name'=>'ftextmargin[]',
								'size'=>2,					
								'placeholder'=>'Margin Text',				
								'value'=>$arrmargin[$k],							
								'required'=>'required'));
			$data['fcolor'][$k]= form_dropdown(array('id'=>'setcolor',
								'class'=>'form-control',							
								'name'=>'fcolor[]',							
								'placeholder'=>'Color',							
								'required'=>'required'),$optcolor,$arrcolor[$k]);
			$data['fcolumn'][$k]= form_dropdown(array('id'=>'setcolumn',
								'class'=>'form-control',							
								'name'=>'fcolumn[]',							
								'placeholder'=>'Column',							
								'required'=>'required'),$optcolumn,$arrcolumn[$k]);
			$data['fcenter'][$k]= form_dropdown(array('id'=>'setcenter',
								'class'=>'form-control',							
								'name'=>'fcenter[]',							
								'placeholder'=>'Justify',							
								'required'=>'required'),$optcenter,$arrcenter[$k]);
			}

				$arrtitle = explode('--',$this->Msetting->getset('titletextcerti'));
				$arrsignname = explode('--',$this->Msetting->getset('namesigntextcerti'));
				$arrnosign = explode('--',$this->Msetting->getset('nosigntextcerti'));
			foreach ($arrtitle as $k => $v) {
				$data['ftitletext'][$k]= form_input(array('id'=>'settitle',
								'class'=>'form-control',					
								'name'=>'ftexttitle[]',
								'size'=>10,					
								'placeholder'=>'Name Title',				
								'value'=>$arrtitle[$k],							
								'required'=>'required'));		
				$data['fsignnametext'][$k]= form_input(array('id'=>'setnamesign',
								'class'=>'form-control',					
								'name'=>'fnamesign[]',
								'size'=>10,					
								'placeholder'=>"Signed Full Name",				
								'value'=>$arrsignname[$k],							
								'required'=>'required'));
				$data['fsignnotext'][$k]= form_input(array('id'=>'setnosign',
								'class'=>'form-control',					
								'name'=>'fnosign[]',
								'size'=>10,					
								'placeholder'=>'Signed ID Number',				
								'value'=>$arrnosign[$k],							
								'required'=>'required'));
			}
				
				

			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Certificate/savesettingcerti');
			
				
			$data['nocerti'] = $this->generatenocerti('{nocerti}/{date}/{month}/{year}');
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Preview Certificate";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/certi/prevlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function previewcerti($id = null){
		$this->load->model(array('Msetting','Mcerti'));
		
		ob_clean();
		// Set the content type header - in this case image/jpeg
		header('Content-Type: image/jpeg');

		$fontname = FCPATH.'assets/fonts/'.$this->Msetting->getset('fontcerti');
		$filename = base_url('upload/design/'.$this->Mcerti->fileDefault());
		$arrsize = explode(',', $this->Msetting->getset('sizecerti'));
		$arrmargin = explode(',', $this->Msetting->getset('margincerti'));
		$arrcolor = explode(',', $this->Msetting->getset('colorcerti'));
		$arrcolumn = explode(',', $this->Msetting->getset('columncerti'));
		$arrcenter = explode(',', $this->Msetting->getset('centercerti'));
		$pretext = $this->Msetting->getset('pretextcerti');
		$lvltext = $this->Msetting->getset('leveltextcerti');
		$title = $this->Msetting->getset('titletextcerti');
		$namesign = $this->Msetting->getset('namesigntextcerti');
		$nosign = $this->Msetting->getset('nosigntextcerti');
		
		
		list($image_width, $height) = getimagesize($filename);
		

		 if (isset($id))
		 	{
		 		$this->load->model('Mcerti'); 
		 		$arrcerti = $this->Mcerti->detailcerti(array('nocerti','uname','unim','lvlname','cread','clisten','cwrite','cgrammar','cspeak'),$id);
				
		 		$texts = array($arrcerti[0]['uname'],$arrcerti[0]['nocerti']);
		 		array_push($texts, $pretext);
		 		array_push($texts,'LISTENING COMPREHENTION--'.$arrcerti[0]['clisten'].'//GRAMMAR AND STRUCTURE--'.$arrcerti[0]['cgrammar'].'//READING COMPREHENTION--'.$arrcerti[0]['cread'].'//WRITING EXPRESSION--'.$arrcerti[0]['cwrite'].'//SPEAKING--'.$arrcerti[0]['cspeak']);
		 		array_push($texts, str_replace('{LEVEL}', strtoupper($arrcerti[0]['lvlname']), $lvltext));
		 		array_push($texts, $title,$namesign,$nosign);

		 	} else {


		 		$texts = array('FULL NAME','Certificate Number');
		 		array_push($texts, $pretext);
		 		array_push($texts,
		 				'LISTENING COMPREHENTION--A//GRAMMAR AND STRUCTURE--A//READING COMPREHENTION--A//WRITING EXPRESSION--A//SPEAKING--A');
		 		array_push($texts, str_replace('{LEVEL}','ELEMENTARY', $lvltext));
		 		array_push($texts, $title,$namesign,$nosign);
			}
			

		 		// define the base image that we lay our text on
				$im = imagecreatefromjpeg($filename);

				// setup the text colours
				$color['black'] = imagecolorallocate($im, 0, 0, 0);
				$color['green'] = imagecolorallocate($im, 55, 189, 102);

				// this defines the starting height for the text block
				$y = imagesy($im) - $height;
					 
				// loop through the array and write the text
				$u=1; $i=0;	$prevsize=0;end($texts); $last = prev($texts);
				foreach ($texts as $k=>$value){
					$i += $arrmargin[$k];
				
						//count how many line	
						$arrline = explode('//', $value);
					if ($arrcolumn[$k]=='1')	
					{ 
						if(count($arrline)>1){
							$ay = $y;
							foreach ($arrline as $a => $line) {
								// center the text in our image - returns the x value
								$dimensions = imagettfbbox($arrsize[$k], 0, $fontname, $line);
								$x = ceil(($image_width - $dimensions[4]) / 2);
								imagettftext($im, $arrsize[$k], 0, $x, $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $line);
								$ay+=$arrsize[$k]+ceil($arrsize[$k]/2);
								$y = $ay;
							}
						} else {
							// center the text in our image - returns the x value
							$dimensions = imagettfbbox($arrsize[$k], 0, $fontname, $value);
							$x = ceil(($image_width - $dimensions[4]) / 2);
							imagettftext($im, $arrsize[$k], 0, $x, $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $value);
							($u==1) ?  imageline( $im, $x , $y+$i+$prevsize+3 , $x+$dimensions[4]+10 , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]] ): null;
						}
					} else {
						//if column were 2 and >1 line
						if(count($arrline)>1){
							$ay = $y;
							foreach ($arrline as $a => $line) {
								$arrtext = explode('--',$line);
								$firsttext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[0]);
								$column = ceil($image_width/$arrcolumn[$k]);
								$secondtext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[1]);
								
								if($arrcenter[$k]){	
								imagettftext($im, $arrsize[$k], 0, ceil(($column - $firsttext[4])/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, ceil(((3*$column)-$secondtext[4])/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);
								} else {
									imagettftext($im, $arrsize[$k], 0, $column-ceil($column/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
									imagettftext($im, $arrsize[$k], 0, $column+ceil($column/2), $ay+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);

								}
								$ay += $arrsize[$k] + ceil($arrsize[$k]/2);
								$y = $ay;
							}
						} else {
							$arrtext = explode('--',$value);
							$firsttext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[0]);
							$column = ceil($image_width/$arrcolumn[$k]);
							$secondtext = imagettfbbox($arrsize[$k], 0, $fontname, $arrtext[1]);
							if($arrcenter[$k]){	
								imagettftext($im, $arrsize[$k], 0, ceil(($column - $firsttext[4])/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, ceil(((3*$column)-$secondtext[4])/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);
							} else {
								imagettftext($im, $arrsize[$k], 0, $column-ceil($column/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[0]);
								imagettftext($im, $arrsize[$k], 0, $column+ceil($column/2), $y+$i+$prevsize, $color[$arrcolor[$k]], $fontname, $arrtext[1]);

							}
						}
						
						//create underline
						if ($last==$value){
							imageline( $im, ceil(($column - $firsttext[4])/2) , $y+$i+$prevsize+3 , ceil(($column + $firsttext[4])/2) , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]]);
							imageline( $im, ceil(((3*$column)-$secondtext[4])/2), $y+$i+$prevsize+3 , ceil(((3*$column)+$secondtext[4])/2) , $y+$i+$prevsize+3 ,  $color[$arrcolor[$k]]);
							
						}
					}

					// add 32px to the line height for the next text block
					$u++; $prevsize=$arrsize[$k];
					
				}

		 	
		// Skip the filename parameter using NULL, then set the quality to 75%
		imagejpeg($im, NULL, 75);

		// Free up memory
		imagedestroy($im);
	}

	public function checknocerti(){
		$em = $this->input->post('nocerti');
		echo $this->Mcerti->checknocerti($em);
	}
	
	public function generatenocerti($format=null){
		$preformat=['{nocerti}','{date}','{month}','{year}'];
		$replace=[$this->Mcerti->maxnocerti($this->convertroman->romanNumerals(date("m")).'/'.date("Y"))+1,date('d'),$this->convertroman->romanNumerals(date("m")),date("Y")];
		($format==null) ? $format = $this->Msetting->getset('certiformat'):null;
		$arrformat= explode('/',$format);
		foreach($arrformat as $key=>$val){
			$arrformat[$key]  = str_replace($preformat, $replace, $val);
		}
		return implode('/',$arrformat);
	}
	
	public function savesetting(){
		if(null!= $this->input->post('period')){
		$dtset=array(
				'period'=>$this->input->post('period'));
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Certificate Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Certificate Failed.");
		}
		redirect(base_url('Organizer/Certificate'));
	}

	public function savesettingcerti(){
		if(is_array($_POST)){
		$f = true;
		$dtset=array(
				'sizecerti'=>implode($this->input->post('ftextsize'),','),
				'margincerti'=>implode($this->input->post('ftextmargin'),','),
				'colorcerti'=>implode($this->input->post('fcolor'),','),
				'columncerti'=>implode($this->input->post('fcolumn'),','),
				'centercerti'=>implode($this->input->post('fcenter'),','),
				'pretextcerti'=>str_replace("\n", "//", $this->input->post('fcertitext')),
				'leveltextcerti'=>$this->input->post('fleveltext'),
				'titletextcerti'=>implode($this->input->post('ftexttitle'),'--'),
				'namesigncerti'=>implode($this->input->post('fnamesign'),'--'),
				'nosigncerti'=>implode($this->input->post('fnosign'),'--')
				);
			//check new font
			if (!empty($_FILES['ffont']['name'])) {
					$arrfile =explode('.', $_FILES['ffont']['name']);
				$ext = end($arrfile);
				 // config upload
	            $config['upload_path'] = FCPATH.'assets/fonts/';
	            $config['allowed_types'] = '*';
	            $config['max_size'] = '1000';
	            $this->load->library('upload', $config);
	            if (($ext == 'ttf')){
					if ( (! $this->upload->do_upload('ffont'))){
	                // if file validation failed, send error to view
	                $error = 'Failed uploading new font'; $f=false;
		            } else {
		              // if upload success, upload new delete old font
		              $upload_data = $this->upload->data();
		              $dtfont = array(
		              			'fontcerti'=>$upload_data['file_name']
		              			);
		              unlink($config['upload_path'].$this->Msetting->getset('fontcerti'));
		              $this->Msetting->savesetting($dtfont);
		          	}
		        } else {
		        	$error = 'Failed uploading new font'; $f=false;
		        }
			}
			$this->Msetting->savesetting($dtset);
			($f)? $this->session->set_flashdata('v',"Update Setting Design Success."):$this->session->set_flashdata('x',"Update Setting Design Failed.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Design Failed.");
		}
		redirect(base_url('Organizer/Certificate/preview'));
	}
	
	public function returncolomn($header) {
	$find=['nocerti','certidate','uname','unim','lvlname','cspeak','cread','clisten','cwrite','cgrammar'];
	$replace = ['Certicate Number','Certificate Date','Full Name','NIM','Level','Speak','Read','Listen','Write','Grammar'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
