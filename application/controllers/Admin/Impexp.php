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
		
		// ================= data automated backup =============
		
		$setactive = ($this->Msetting->getset('dbbackupset')=='') ? false : true;
		$data['btnset']=form_checkbox(array(
							'name'=>'fsetbackup',
							'id'=>'setdbbackup',
							'checked'=>$setactive,
							'value'=>'1')
							);
		$data['datestart'] = form_input(
							array(
								'id'=>'inputbackupdate',
								'name'=>'dbbackupdate',
								'class'=>'form-control',
								'type' => 'text',
								'required'=>'required',
								'value' => date("d-m-Y H:i:s",strtotime($this->Msetting->getset('dbbackupstart')))
							));
		$data['inputemailbackup'] = form_input(
							array(
								'id'=>'inputbackupemail',
								'name'=>'backupemail',
								'class'=>'form-control',
								'type' => 'email',
								'required'=>'required',
								'value' => $this->Msetting->getset('dbbackupemail')
							));
		$data['period'] = form_input(
							array(
								'id'=>'inputdbperiod',
								'name'=>'dbperiod',
								'class'=>'form-control',
								'maxlength'=>3,
								'type' => 'text',
								'style' => 'width:50px;',
								'required'=>'required',
								'value' => $this->Msetting->getset('dbbackupperiod')
							));

		//=============== Template ============
		$data['jsFiles'] = array(
							'moment/moment.min','daterange/daterangepicker','toggle/bootstrap2-toggle.min','numeric/numeric.min');
		$data['cssFiles'] = array(
							'daterange/daterangepicker','toggle/bootstrap2-toggle.min');  
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
		$dtcol = ['agenda','answer','article','broadcast','cat_artcle','certidesign','certificate','fac','jdwl_mem','jdwl_tes','jk','jns_trans','level','logstatus','notif','nread','question','ques_attach','qtype','quo_sbjct','resultqa','resulttest','role','setting','subject','template','test','transaksi','ttransfer','user'];
		} else if ($this->input->post('fidcol')!=null){
		$tempcol = $this->input->post('fidcol'); 
		$dtcol = explode(',',$tempcol); 
		} else{
			$this->session->set_flashdata('x','Backup failed, you need to select table(s).');
			redirect(base_url('Admin/Impexp/database'));
		}
		
		// Load the DB utility class
		$this->load->dbutil();
		$this->load->model('Msetting');
		$appname = $this->Msetting->getset('webtitle');
		// Backup your entire database and assign it to a variable
			$prefs = array(
					'tables'        => $dtcol,   												// Array of tables to backup.
					'ignore'        => array(),                     							// List of tables to omit from the backup
					'format'        => 'zip',                       							// gzip, zip, txt
					'filename'      => $appname.'.sql',     // File name - NEEDED ONLY WITH ZIP FILES
					'add_drop'      => TRUE,
					'add_insert'    => TRUE,
					'newline'       => "\n",
					'foreign_key_checks' => false 	
				);
		$backup = $this->dbutil->backup($prefs);

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		//write_file(FCPATH.'temp_upload/backupdb.zip', $backup);

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($appname.' Database Backup ('.date("d-m-Y H:i:s").').zip',$backup);
				
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

	public function saveschedulebackup(){
		$datestart = $this->input->post("dbbackupdate");
		if(isset($datestart)){

			$set = $this->input->post("fsetbackup");
			$setactive = ($set!="") ? "1" : "0";
			$startdate = date("Y-m-d H:i:s",strtotime($datestart));
			$maildb = $this->input->post('backupemail');
			$timeperiod = $this->input->post('dbperiod');
			$dt = array(
				"dbbackupstart"=>$startdate,
				"dbbackupset"=> $setactive,
				"dbbackupemail"=> $maildb,
				"dbbackupperiod"=> $timeperiod
				);
			$this->Msetting->savesetting($dt,$this->session->userdata('user'));

			//------------- create cronjob sms sender -------------
			$this->load->library('cronjob');
			$tags = array(
				'type'=>'backupdb'
			);

			if($setactive=='1'){

				$postdata = array(
						'do'=>'backupdb',
						'user'=>$this->session->userdata('user'),
						'maildb' => $maildb
						);
				$this->cronjob->deletecron($this->session->userdata('user'),$tags);
				$res = json_decode($this->cronjob->createcron($this->session->userdata('user'),$tags,$startdate,$postdata,'-1',$timeperiod.'day'));
				$msg = ($res->status=='1') ? 'Turn ON Automated Backup Database Success' : "Turn ON Automated Backup Database Failed";
				$flash = ($res->status=='1') ? 'v' : 'x';
			} else {
				$res = json_decode($this->cronjob->deletecron($this->session->userdata('user'),$tags));
				$msg = ($res->status=='1') ? 'Turn OFF Automated Backup Database Success' : "Turn OFF Automated Backup Database Failed";
				$flash = ($res->status=='1') ? 'v' : 'x';
			}
				$this->session->set_flashdata($flash,$msg);
			redirect("Admin/Impexp");
		}
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
	$find=['agenda','answer','article','jdwl_mem','broadcast','cat_artcle','certidesign','certificate','fac','jdwl_tes','jk','jns_trans','level','logstatus','notif','nread','question','ques_attach','quo_sbjct','resultqa','resulttest','role','setting','subject','template','test','transaksi','ttransfer','user','qtype'];
	$replace = ['Agenda Data','Answer Key Question','Articles','Member Schedule','Broadcast Data','Category Article','Certificate Design','Certificate Data','Faculty Data','Test Schedule Data','Gender','Payment Type','Class Level','Log Login','Notifications Data','Notification Delivered','Question Data','Question Attachment','Quota Subject Test','Result Test','Final Result Test','Role Access','Setting Data','Subject Test Data','Template Page','Test Data','Payment Data','Transfer Payment Confirmation','Account Data','Question Type'];
		foreach ($row as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
