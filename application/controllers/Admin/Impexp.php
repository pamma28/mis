<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Impexp extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation'));
		$this->load->helper(array('form','url'));
		
		$this->load->model('Mdb');
    }

	public function index(){
		$this->database();				
	}
	
	public function database() {		
		//===================== table handler =============
		$column=['table_name','table_rows'];
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
		$addrpage = '';
		if ($this->input->get('search')!=null){
			$filter = $this->input->get('search');
			$addrpage= $addrpage.'&search='.$filter;
		} else {
			$filter = '';
		}
		if ($this->input->get('column')!=null){
			$col = $this->input->get('column');
			$addrpage= $addrpage.'&column='.$col;
		} else {
			$col = '';
		}
				//count rows of data (inlcude filter & search)
				$rows = $this->Mdb->countdb($col,$filter);
		if ($this->input->get('view')!=null){
			$offset = $this->input->get('view');
		} else {
			$offset = 10;
		}
		if ($this->input->get('page')!=null){
			$perpage = $this->input->get('page');
		} else {
			$perpage = 1;
		}
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/admin/Impexp/?view='.$offset.$addrpage,
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
		$data["urlperpage"] = base_url().'Admin/Impexp?view=';
		$data["perpage"] = ['10','20','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========			
		$temp2 = $this->Mdb->datadb($column,$config['per_page'],$perpage,$col,$filter);
				//manipulation table name
				$temp = $this->returnrow($temp2);
				foreach($temp as $key=>$value){
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ctable',
							'value'=>$temp2[$key]['table_name']
							));
				array_unshift($temp[$key],$ctable);
				//manipulation menu
				$enc = $temp2[$key]['table_name'];
				$temp[$key]['table_name']='<span class="tblname">'.$temp[$key]['table_name'].'</span>';
				$temp[$key]['menu']='<small><a href="'.base_url('Admin/Impexp/detaildb?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details Data</a> | '.
				'<a href="#" data-href="'.base_url('Admin/Impexp/deletedb?id=').$enc.'" alt="Delete Data" class="btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Delete (Empty Data)</a></small>';
				}
				
		$data['listlogin'] = $this->table->generate($temp);
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Admin/Impexp/importdb');
			
			
		
		// ============= export form ==============
			$data['falldb']= form_checkbox(array('name'=>'falldb',
							'class'=>'form-class',
							'id'=>'checkalldb',
							'value'=>'1'));
			$data['fselectedidcol']= form_input(array('name'=>'fidcol',
							'type'=>'hidden',
							'id'=>'selectedidcol'));
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
			$data['factexp'] = site_url('Admin/Impexp/exportdb');
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'moment/moment.min','daterange/daterangepicker');
		$data['cssFiles'] = array(
							'daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Database Management";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/db/tablelist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 public function importdb(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = '*';
            $config['max_size'] = '10000000';
			$config['log_threshold'] = 4;
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or (($this->upload->data()['file_ext'])!='.sql')) {
                // if file validation failed, send error to view
                $this->session->set_flashdata('x','Upload file failed, please upload (.sql) file type only.');
				redirect(base_url('Admin/Impexp'));
            } else {
              // if upload success, take file data
			  $dt = $this->upload->data();
			  $schema = htmlspecialchars(file_get_contents($dt['file_path'].$dt['file_name']));
			  $query = rtrim(trim($schema), "\n;");
			  $query_list = explode(";", $query);
			  $hsl = $this->Mdb->restoredb($query_list);
              
            //set flashdata
			$this->session->set_flashdata('v','Database is successfully restored.<br/>'.$hsl);
			//redirect ke halaman awal
			redirect(base_url('Admin/Impexp'));
			}
		//delete file
        $upload_data = $this->upload->data();
		$file = $upload_data['file_name'];
        $path = FCPATH.'temp_upload/' . $file;
        unlink($path);
        
    }
	
	public function exportdb(){
		//catch column value
		if ($this->input->post('falldb')!=null){
		$dtcol = ['agenda','answer','article','bcto','broadcast','cat_artcle','certidesign','certificate','fac','jdwl_tes','jk','jns_trans','level','logstatus','notif','nread','question','ques_attach','quo_sbjct','resultqa','resulttest','role','setting','subject','template','test','transaksi','ttransfer','user'];
		} else if ($this->input->post('fidcol')!=null){
		$tempcol = $this->input->post('fidcol'); 
		$dtcol = explode(',',$tempcol); 
		} else{
			$this->session->set_flashdata('x','Backup failed, you need to select table(s).');
			redirect(base_url('Admin/Impexp/database'));
		}
		
		// Load the DB utility class
		$this->load->dbutil();

		// Backup your entire database and assign it to a variable
			$prefs = array(
					'tables'        => $dtcol,   												// Array of tables to backup.
					'ignore'        => array(),                     							// List of tables to omit from the backup
					'format'        => 'zip',                       							// gzip, zip, txt
					'filename'      => 'MemberInformationSytem.sql',     // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'      => TRUE,
					'add_insert'    => TRUE,
					'newline'       => "\n",
					'foreign_key_checks' => false 	
				);
		$backup = $this->dbutil->backup($prefs);

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file(FCPATH.'temp_upload/', $backup);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download('backupdb ('.date("d-m-Y H:i:s").').zip',$backup);
				
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
		$dtrole = $this->Mdb->getallrole();
		
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
		header('Content-Disposition: attachment;filename="ImportFormat.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function detaildb(){
		//fecth data from db
		$col = ['*'];
		$id = $this->input->get('id');
		$dbres = $this->Mdb->detaildb($col,$id);
		
		//set table template
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered table-responsive">',
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
		$data['rdata']=$this->table->generate($dbres);
		
		// =============== view handler ============
		$this->load->view('dashboard/admin/db/detaildb', $data);
		
		
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
		
			$optrole = $this->Mdb->getrole();
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
		$g = $this->Mdb->detailacc($col,$id);
		
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
		
		
		
			$optrole = $this->Mdb->getrole();
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
					'upass' => md5($this->input->post('fpass')),
					'uhp' => $this->input->post('fhp'),
					'idrole' => $this->input->post('frole'),
					'uallow' => $this->input->post('fallow')
					);
		$r = $this->Mdb->updateacc($fdata,$id);
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
		$r = $this->Mdb->addacc($fdata);
		if ($r){
		$this->session->set_flashdata('v','Add Account Data Success');
		} else {		
		$this->session->set_flashdata('x','Add Account Data Failed');
		}
		redirect(base_url('Admin/Managelogin'));
	
	}

	public function deletedb(){
		$id = $this->input->get('id');
		$r = $this->Mdb->deletedb($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Success');
		} else{
		$this->session->set_flashdata('x','Delete Failed');
		} 
		redirect(base_url('Admin/Impexp?view=all'));
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
			$dtend = substr($dtrange,9);
			$dexp = $this->Mdb->exportlogin($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mdb->exportlogin(null,null,$dtcol);
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
	
	public function checkemail(){
		$em = $this->input->post('email');
		echo $this->Mdb->checkmail($em);
	}
	
	public function checkuser(){
		$us = $this->input->post('user');
		echo $this->Mdb->checkuser($us);
	}
	
	public function returncolomn($header) {
	$find=['table_name','table_rows'];
	$replace = ['Table Name','Total Data'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
	public function returnrow($row) {
	$find=['agenda','answer','article','bcto','broadcast','cat_artcle','certidesign','certificate','fac','jdwl_tes','jk','jns_trans','level','logstatus','notif','nread','question','ques_attach','quo_sbjct','resultqa','resulttest','role','setting','subject','template','test','transaksi','ttransfer','user'];
	$replace = ['Agenda Data','Answer Key Question','Articles','Broadcast Events','Broadcast Data','Category Article','Certificate Design','Certificate Data','Faculty Data','Test Schedule Data','Gender','Payment Type','Class Level','Log Login','Notifications Data','Notification Delivered','Question Data','Question Attachment','Quota Subject Test','Result Test','Final Result Test','Role Access','Setting Data','Subject Test Data','Template Page','Test Data','Payment Data','Transfer Payment Confirmation','Account Data'];
		foreach ($row as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
