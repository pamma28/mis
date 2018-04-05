<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Managelogin extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model('Mlogin');
    }

	public function index(){
		$this->logindata();				
	}
	
	public function logindata() {		
		//===================== table handler =============
		$column=['ucreated','uuser','uname','uemail','uhp','rolename','ulastlog','uallow'];
		$header = $this->returncolomn($column);
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
			$rows = $this->Mlogin->countlogin($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mlogin->countlogin();	
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
						'ucreated' => 'Date Created',
						'uuser' => 'Username',
						'uname' => 'Name',
						'uemail' => 'Email'
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
						'placeholder'=>'Period',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		$adv['Created'] = form_input(
						array('name'=>'ucreated',
						'id'=>'createdon',
						'placeholder'=>'Date Created',
						'value'=>isset($tempfilter['ucreated']) ? $tempfilter['ucreated'] : null,
						'class'=>'form-control'));
		
		$adv['Username'] = form_input(
						array('name'=>'uuser',
						'id'=>'username',
						'placeholder'=>'Username',
						'value'=>isset($tempfilter['uuser']) ? $tempfilter['uuser'] : null,
						'class'=>'form-control'));
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'fullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['Email'] = form_input(
						array('name'=>'uemail',
						'id'=>'email',
						'placeholder'=>'Email',
						'value'=>isset($tempfilter['uemail']) ? $tempfilter['uemail'] : null,
						'class'=>'form-control'));
						
		$adv['Phone Number'] = form_input(
						array('name'=>'uhp',
						'id'=>'hp',
						'placeholder'=>'Phone Number',
						'value'=>isset($tempfilter['uhp']) ? $tempfilter['uhp'] : null,
						'class'=>'form-control'));
		$adv['Role'] = form_dropdown(array(
							'name'=>'rolename',
							'id'=>'role',
							'class'=>'form-control'),
							array(''=>'No filter','Admin'=>'Admin',
							'Organizer'=>'Organizer','Member'=>'Member'),isset($tempfilter['rolename']) ? $tempfilter['rolename'] : null);
		$adv['Allow/Deny'] = form_dropdown(array(
							'name'=>'uallow',
							'id'=>'allow',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Allow',
							'0'=>'Deny'),isset($tempfilter['uallow']) ? $tempfilter['uallow'] : null);
		$dtfilter = '';
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;

		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/admin/Managelogin/?view='.$offset.$addrpage,
				'total_rows' => $rows,
				'per_page' => $offset,
				'use_page_numbers' => true,
				'page_query_string' =>true,
				'query_string_segment' =>'page',
				'num_links' => $rows,
				'cur_tag_open' => '<span class="disabled"><a href="#">',
				'cur_tag_close' => '<span class="sr-only"></span></a></span>',
				'next_link' => 'Next',
				'prev_link' => 'Prev'
				);
		$data["urlperpage"] = base_url().'Admin/Managelogin?view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
		$temp = $this->Mlogin->datalogin($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				if($value['uallow']){
					$temp[$key]['uallow']='<span class="label label-success">Allowed</span>';
				} else{
					$temp[$key]['uallow']='<span class="label label-danger">Denied</span>';
				}
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['uuser']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['uuser']='<span class="idname">'.$temp[$key]['uuser'].'</span>';
				$temp[$key]['ucreated'] = date('d-M-Y H:i:s',strtotime($value['ucreated']));
				//manipulation last login
				$temp[$key]['ulastlog']=$this->converttime->time_elapsed_string($temp[$key]['ulastlog']);
				//manipulation menu
				$enc = $value['uuser'];
				$temp[$key]['menu']='<small><a href="'.base_url('Admin/Managelogin/detaillogin?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a> | '.
				'<a href="'.base_url('Admin/Managelogin/editlogin?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a> | '.
				'<a href="#" data-href="'.base_url('Admin/Managelogin/deletelogin?id=').$enc.'" alt="Delete Data" class="btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Delete</a></small>';
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
			$data['factselected'] = site_url('Admin/Managelogin/updateselected');
				
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Admin/Managelogin/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'ucreated'=>'Date Created',
						'uuser' => 'Username',
						'uname' => 'Name',
						'rolename' => 'Role',
						'uemail' => 'Email',
						'uhp' => 'Phone Number',
						'ulastip' => 'Last IP',
						'ulastlog' => 'Last Login',
						'uallow' => 'Allow'
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
			$data['factexp'] = site_url('Admin/Managelogin/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Admin/Managelogin/printlogin');
		
		//=============== Template ============
		$data['jsFiles'] = array(
							'moment/moment.min','daterange/daterangepicker','print/printThis','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'daterange/daterangepicker','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Account Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/akun/loginlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatLoginData.xls')) {
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
                   $dtxl[$i-1]['upass'] = md5($objWorksheet->getCell('B'.($i+1))->getValue());
                   $dtxl[$i-1]['uname'] = $objWorksheet->getCell('C'.($i+1))->getValue();
                   $dtxl[$i-1]['uemail'] = $objWorksheet->getCell('D'.($i+1))->getValue();
                   $dtxl[$i-1]['uhp'] = $objWorksheet->getCell('E'.($i+1))->getValue();
                   $dtxl[$i-1]['idrole'] = $objWorksheet->getCell('F'.($i+1))->getValue();
                   $dtxl[$i-1]['uallow'] = $objWorksheet->getCell('G'.($i+1))->getValue();
				 }
              }
			  
			//save data through model
			  $report = $this->Mlogin->importdata($dtxl);
			
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
        
		//redirect ke halaman awal
		redirect(base_url('Admin/Managelogin/logindata'));
        
    }
	
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['Date Created','Username','Full Name','Email','Phone Number','Last Login IP','Last Login','Role Access','Allow']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mlogin->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mlogin->exportlogin(null,null,$dtcol);
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
				if(array_key_exists('uallow',$val)){
							if ($val['uallow']==1){
							$val['uallow']='Allowed';
							}else{
							$val['uallow']='Denied';
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
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LOGIN DATA USER ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Login Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Username','Password','Full Name','Email','Phone Number','Role Access','Allow']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormat');
		
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
		
		// put hint role data
		$keycol = ['idrole','rolename'];
		$dtrole = $this->Mlogin->getallrole();
		
		//set colomn header Role
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.($Hrow+1),"IDRole");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).($Hrow+1),"Role Name");			
				
		$Hrow= 3;
		$tempcol = $Dcol;
		foreach ($dtrole as $rl=>$v){
			$Dcol=$tempcol;
			foreach($v as $val){
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Hrow,$val);
				$Dcol++;
				}
			$Hrow++;
		}
		$rowrole=$Hrow;
		$objPHPExcel->getActiveSheet()->setCellValue($Dcol.($Hrow-1),"Remember: Always Put 'IDRole' instead of 'Role Name' in Colomn 'Role Access'");			
		
		//set colomn header Allow
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow+2),"IDAllow");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow+2),"Value");			
		$dtallow= array(
					'1'=>'Allow',
					'2'=>'Deny'
					);		
		$Hrow= $Hrow+3;
		foreach ($dtallow as $al=>$val){
			$Dcol=$tempcol;
				$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Hrow,$al);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Hrow,$val);
			$Hrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow-1),"Remember: Always Put 'IDAllow' instead of 'Value' in Colomn 'Allow'");			
		
		
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
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.'2:'.chr(ord($lastcol)-1).($rowrole-1))->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle($Bordercol.($rowrole+2).':'.chr(ord($lastcol)-1).($Hrow-1))->applyFromArray($styleArray);
		
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
		$objPHPExcel->getActiveSheet()->freezePane(chr(ord($Bordercol)-1).($Hrow));
		
		
		//create output file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="ImportFormatLoginData.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function detaillogin(){
		//fecth data from db
		$col = ['ucreated','uupdate','uuser','uname','uemail','uhp','ulastip','ulastlog','rolename','uallow'];
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
		$this->load->view('dashboard/admin/akun/detaillogin', $data);
		
		
	}
	
	public function addlogin(){
		//============ form add account ===========
		$funame = array('name'=>'fusername',
						'id'=>'Username',
						'required'=>'required',
						'placeholder'=>'Username',
						'value'=>set_value('fusername'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($funame).'<span id="usuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Username Available</span><span id="ufailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Username Not Available</span>';
		
		$ffname = array('name'=>'ffullname',
						'id'=>'Fullname',
						'required'=>'required',
						'placeholder'=>'Fullname',
						'value'=>set_value('ffullname'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($ffname);
		
		$fpass = array('name'=>'fpass',
						'id'=>'Password',
						'required'=>'required',
						'placeholder'=>'Password',
						'value'=>set_value('fpass'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fpass);
		
		$femail = array('name'=>'femail',
						'id'=>'Email',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>set_value('femail'),
						'class'=>'form-control',
						'size'=>'50'
						);
		$r[] = form_input($femail).'<span id="valsuccess" style="display:none;" class="text-primary"><i class="fa fa-check"></i> Email Available</span><span id="valfailed" class="text-danger" style="display:none;"><i class="fa fa-ban"></i> Email Not Available</span>';
		
		$fhp = array('name'=>'fhp',
						'id'=>'return',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>set_value('fhp'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($fhp);
		
			$optrole = $this->Mlogin->getrole();
		$frole = array('name'=>'frole',
						'id'=>'Role',
						'placeholder'=>'Role',
						'required'=>'required',
						'class'=>'form-control');
		$r[] = form_dropdown($frole,$optrole,set_value('frole'));
		
		$r[] = form_checkbox(array(
							'name'=>'fallow',
							'data-toggle'=>'toggle',
							'data-on'=>'Allow',
							'data-off'=>'Deny',
							'data-size'=>'big',
							'id'=>'idallow',
							'checked'=>set_value('fallow'),
							'value'=>'1')
							);
		
		$fsend = array(	'id'=>'submit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['uuser','uname','upass','uemail','uhp','idrole','uallow'];
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
		
		$this->load->view('dashboard/admin/akun/addlogin', $data);
	}
	
	public function editlogin(){					
		// ============== Fetch data ============
		$col = ['ucreated','uuser','uname','uemail','upass','uhp','user.idrole','uallow'];
		$id = $this->input->get('id');
		$g = $this->Mlogin->detailacc($col,$id);
		
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
		
		
		
			$optrole = $this->Mlogin->getrole();
		$frole = array('name'=>'frole',
						'id'=>'Role',
						'placeholder'=>'Role',
						'required'=>'required',
						'class'=>'form-control');
		$r[] = form_dropdown($frole,$optrole,$g[0]['idrole']);
		
		$r[] = form_checkbox(array(
							'name'=>'fallow',
							'data-toggle'=>'toggle',
							'data-on'=>'Allow',
							'data-off'=>'Deny',
							'data-size'=>'big',
							'id'=>'idallow',
							'checked'=>$g[0]['uallow'],
							'value'=>'1')
							);
		
		$data['inid'] = form_hidden('fuser',$g[0]['uuser']);
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
		
		$this->load->view('dashboard/admin/akun/editlogin', $data);
	
	
	}
	
	public function updatelogin(){
		$id = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),
					'uupdate' => date("Y-m-d H:i:s"),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'idrole' => $this->input->post('frole'),
					'uallow' => $this->input->post('fallow')
					);
		($this->input->post('fpass')!='') ? $fdata['upass']=md5($this->input->post('fpass')):null;
		$r = $this->Mlogin->updateacc($fdata,$id);
		if ($r){
		$this->session->set_flashdata('v','Update Account Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Account Data Failed');
		}
		redirect(base_url('Admin/Managelogin'));
	}
		
	public function savelogin(){
	if ($this->input->post('fallow')==1){
	$allow = 1;} else {$allow=0;}
	
	$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uuser' => $this->input->post('fusername'),
					'upass' => md5($this->input->post('fpass')),
					'uname' => $this->input->post('ffullname'),
					'uupdate' => date("Y-m-d H:i:s"),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'idrole' => $this->input->post('frole'),
					'uallow' => $allow
					);
		$r = $this->Mlogin->addacc($fdata);
		if ($r){
		$this->session->set_flashdata('v','Add Account Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Account Data Failed');
		}
		redirect(base_url('Admin/Managelogin'));
	
	}

	public function deletelogin(){
		$id = $this->input->get('id');
		$r = $this->Mlogin->deleteacc($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Admin/Managelogin'));
	}

	public function printlogin(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['ucreated','uuser','uname','rolename','uemail','uhp','ulastip','ulastlog','uallow']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mlogin->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mlogin->exportlogin(null,null,$dtcol);
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
		$this->logindata();
		$this->session->set_flashdata('v',null);
		
		//create title
		$this->load->model('Msetting');
		$period = $this->Msetting->getset('period');
		$data['title']="Account Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('Dashboard/admin/akun/printlogin', $data);
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$type = $this->input->post('ftype');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
			$r = $this->Mlogin->updateselected($dtuser,$type);
			$this->session->set_flashdata('v','Update '.$totuser.' Selected Account success.<br/>Details: '.$r['v'].' success and '.$r['x'].' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Account Failed.');
		}
		redirect(base_url('Admin/Managelogin'));
	}
	
	public function checkemail(){
		$em = $this->input->post('email');
		echo $this->Mlogin->checkmail($em);
	}
	
	public function checkuser(){
		$us = $this->input->post('user');
		echo $this->Mlogin->checkuser($us);
	}
	
	public function returncolomn($header) {
	$find=['ucreated','uupdate','uuser','upass','uname','uemail','uhp','ulastip','ulastlog','rolename','user.idrole','idrole','uallow'];
	$replace = ['Date Created','Last Updated','Username','Password','Full Name','Email','Phone Number','Last Login IP','Last Login','Role Access','Role Access','Role Access','Allow'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
