<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Memberaccount extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mlogin','Msetting'));
    }

	public function index(){
	
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['ucreated','uuser','uname','uemail','ulastlog','uallow'];
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
				'base_url' => base_url().'/Organizer/Memberaccount/?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Memberaccount?'.$addrpage.'view=';
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
				$temp[$key]['ucreated']=date('d-M-Y', strtotime($value['ucreated'])).'<br/>'.date('H:i:s', strtotime($value['ucreated']));
				$temp[$key]['ulastlog']=$this->converttime->time_elapsed_string($temp[$key]['uuser']);
				//manipulation menu
				$enc = $value['uuser'];
				$temp[$key]['menu']='<div class="btn-group"><a href="'.base_url('Organizer/Memberaccount/detailaccount?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Details"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Memberaccount/editaccount?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Memberaccount/deleteaccount?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
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
			$data['factselected'] = site_url('Organizer/Memberaccount/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Memberaccount/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'ucreated'=>'Date Created',
						'uuser' => 'Username',
						'uname' => 'Name',
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
			$data['factexp'] = site_url('Organizer/Memberaccount/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Memberaccount/printaccount');
		
		//=============== setting period ============
			$period = $this->Msetting->getset('period');
			$data['fperiod']= form_input(array('id'=>'setperiod',
								'class'=>'form-control',							
								'name'=>'period',							
								'placeholder'=>'Year/Period',							
								'value'=>$period,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Memberaccount/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','inputmask/inputmask.numeric.extensions','moment/moment.min','daterange/daterangepicker','print/printThis','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'daterange/daterangepicker','toggle/bootstrap2-toggle.min');  
		// =============== view handler ============
		$data['title']="Member Account";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/akun/acclist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatMemberAccount.xls') ){
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
                   $dtxl[$i-1]['uallow'] = $objWorksheet->getCell('F'.($i+1))->getValue();
                   $dtxl[$i-1]['idrole'] = '3';
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
		//redirect to data list
		redirect(base_url('Organizer/Memberaccount'));
        
    }
	
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['Date Created','Username','Full Name','Email','Phone Number','Last Login IP','Last Login','Allow']; 
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
		$period = $this->Msetting->getset('period');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'MEMBER ACCOUNT DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Member Account Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Username','Password','Full Name','Email','Phone Number','Allow']; 
		
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
		
		$Hrow= 2;
		$tempcol = $Dcol;
		$rowrole=$Hrow;
		
		//set colomn header Allow
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"IDAllow");			
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow),"Value");			
		$dtallow= array(
					'1'=>'Allow',
					'2'=>'Deny'
					);
		
		$Hrow=$Hrow+1;
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
		header('Content-Disposition: attachment;filename="ImportFormatMemberAccount.xls');
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
	
	public function addaccount(){
		//============ form add account ===========
		$funame = array('name'=>'fusername',
						'id'=>'Username',
						'required'=>'required',
						'placeholder'=>'Username',
						'value'=>set_value('fusername'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = form_input($funame).'<span class="text-danger hidden"><b><i class="fa fa-ban"></i><i> Username Is Not Available</i></b></span>';
		
		$ffname = array('name'=>'ffullname',
						'id'=>'Fullname',
						'required'=>'required',
						'placeholder'=>'Fullname',
						'value'=>set_value('ffullname'),
						'class'=>'form-control',
						'max-length'=>'50');
		$r[] = form_input($ffname);
		
		$fpass = array('name'=>'fpass',
						'id'=>'Password',
						'type'=>'password',
						'required'=>'required',
						'placeholder'=>'Password',
						'value'=>set_value('fpass'),
						'class'=>'form-control',
						'size'=>'50');
		$r[] = '<div class="input-group">'.form_input($fpass).'<span class="input-group-addon"><a type="button" id="togglePassword" class=""><i class="fa fa-eye"></i></a></span></div>';
		
		$femail = array('name'=>'femail',
						'id'=>'Email',
						'type'=>'email',
						'required'=>'required',
						'placeholder'=>'Email Account',
						'value'=>set_value('femail'),
						'class'=>'form-control',
						'size'=>'50'
						);
		$r[] = form_input($femail).'<span class="text-danger hidden"><b><i class="fa fa-ban"></i><i> Email Is Not Available</i></b></span>';
		
		$fhp = array('name'=>'fhp',
						'id'=>'nohp',
						'required'=>'required',
						'placeholder'=>'Phone Number',
						'value'=>set_value('fhp'),
						'class'=>'form-control',
						'max-length'=>'13');
		$r[] = form_input($fhp).'<span class="text-danger hidden"><b><i class="fa fa-ban"></i><i> Phone Number Is Not Available</i></b></span>';
		
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
		
		$fsend = array(	'id'=>'btnsubmit',
						'value'=>'Create',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['uuser','uname','upass','uemail','uhp','uallow'];
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
		
		$this->load->view('dashboard/org/akun/addacc', $data);
	}
	
	public function editaccount(){					
		// ============== Fetch data ============
		$col = ['ucreated','uuser','uname','uemail','upass','uhp','uallow'];
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
		$r[] = form_input($fhp).'<span class="text-danger hidden"><b><i class="fa fa-ban"></i><i> Phone Number Is Not Available</i></b></span>';
		
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
		$fsend = array(	'id'=>'btnedit',
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
		
		$this->load->view('dashboard/org/akun/editacc', $data);
	
	
	}
	
	public function updateaccount(){
		$id = $this->input->post('fuser');
			$fdata = array (
					'uname' => $this->input->post('ffullname'),
					'uupdate' => date("Y-m-d H:i:s"),
					'uemail' => $this->input->post('femail'),
					'upass' => md5($this->input->post('fpass')),
					'uhp' => $this->input->post('fhp'),
					'uallow' => $this->input->post('fallow')
					);
		$r = $this->Mlogin->updateacc($fdata,$id);
		if ($r){
		$this->session->set_flashdata('v','Update Member Account Success');
		} else {		
		$this->session->set_flashdata('x','Update Member Account Failed');
		}
		redirect(base_url('Organizer/Memberaccount'));
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
		redirect(base_url('Organizer/Memberaccount'));
	}
		
	public function saveaccount(){
	if ($this->input->post('fallow')==1){
	$allow = 1;} else {$allow=0;}
	$mem = $this->input->post('fusername');
	$fdata = array (
					'ucreated' => date("Y-m-d H:i:s"),
					'uuser' => $mem,
					'upass' => md5($this->input->post('fpass')),
					'uname' => $this->input->post('ffullname'),
					'unim' => $mem,
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'idrole' => '3',
					'uallow' => $allow
					);
		$r = $this->Mlogin->addacc($fdata);
		if ($r){
		//======= set notif to org===========
		$idnotif = $this->Msetting->getset('notifnewsignup');
		$this->notifications->pushNotifToOrg(array('idnotif'=>$idnotif,'uuser'=>$mem,'nlink'=>base_url('Organizer/PDS')));

		//======= set notif to member ========
		$idnotifmem = $this->Msetting->getset('notifregistsuccess');
		$this->notifications->pushnotif(array('idnotif'=>$idnotifmem,'uuser'=>$this->session->userdata('user'),'use_uuser'=>$mem,'nlink'=>null));
		$this->session->set_flashdata('v','Add Member Account Success');
		} else {		
		$this->session->set_flashdata('x','Add Member Account Failed');
		}
		redirect(base_url('Organizer/Memberaccount'));
	
	}

	public function deleteaccount(){
		$id = $this->input->get('id');
		$r = $this->Mlogin->deleteacc($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Member Account Success');
		} else{
		$this->session->set_flashdata('x','Delete Member Account Failed');
		} 
		redirect(base_url('Organizer/Memberaccount'));
	}

	public function printaccount(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['ucreated','uuser','uname','uemail','uhp','ulastip','ulastlog','uallow']; 
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
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Member Account Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/akun/printacc', $data);
		
	}
	
	public function checkemail(){
		$em = $this->input->post('email');
		echo $this->Mlogin->checkmail($em);
	}
	
	public function checkuser(){
		$us = $this->input->post('user');
		echo $this->Mlogin->checkuser($us);
	}
	
	public function savesetting(){
		if(null!= $this->input->post('period')){
		$dtset=array(
				'period'=>$this->input->post('period'));
		$this->Msetting->savesetting($dtset);
		$this->session->set_flashdata('v',"Update Setting Period Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Period Failed.");
		}
		redirect(base_url('Organizer/Memberaccount'));
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
