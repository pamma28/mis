<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Matcl','Msetting'));
    }

	public function index(){
		$this->load->library('converttime');
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idarticle','a_date','a_title','a_isi','category','a_aktf','uname'];
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
			$rows = $this->Matcl->countatcl($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Matcl->countatcl();	
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
						'a_date' => 'Article Created',
						'a_title' => 'Article Title',
						'a_isi' => 'Article',
						'category' => 'Category',
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
		$adv['Article Created'] = form_input(
						array('name'=>'a_date',
						'id'=>'createdon',
						'placeholder'=>'Article Created (YYYY-MM-DD)',
						'value'=>isset($tempfilter['a_date']) ? $tempfilter['a_date'] : null,
						'class'=>'form-control'));
		
		$adv['Article Title'] = form_input(
						array('name'=>'a_title',
						'id'=>'atitle',
						'placeholder'=>'Article Title',
						'value'=>isset($tempfilter['a_title']) ? $tempfilter['a_title'] : null,
						'class'=>'form-control'));
							
		$adv['Article'] = form_input(
						array('name'=>'a_isi',
						'id'=>'aisi',
						'placeholder'=>'Article',
						'size'=>'45',
						'value'=>isset($tempfilter['a_isi']) ? $tempfilter['a_isi'] : null,
						'class'=>'form-control'));
			$optcat = $this->Matcl->getoptatclcat();
		$adv['Category'] = form_dropdown(
						array('name'=>'idcat',
						'id'=>'idcat',
						'class'=>'form-control'),
						$optcat,
						isset($tempfilter['idcat']) ? $tempfilter['idcat'] : null);
			
			$optactive = array(''=>'All Status','1'=>'Active','0'=>'Inactive');
		$adv['Status'] = form_dropdown(
						array('name'=>'a_aktf',
						'id'=>'aaktf',
						'class'=>'form-control'),
						$optactive,
						isset($tempfilter['a_aktf']) ? $tempfilter['a_aktf'] : null);
		
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
				'base_url' => base_url().'/Organizer/Article?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Article?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );
				
		//========== data manipulation =========
		
		$temp = $this->Matcl->dataatcl($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'checkbox',
							'value'=>$value['idarticle']
							));
				array_unshift($temp[$key],$ctable);
					//read and modify content text 
					$txt = strip_tags(htmlspecialchars_decode($value['a_isi']));
					(strlen($txt)>30) ? $tmptext = mb_substr($txt,0,30).'...':$tmptext=$txt;
				
				$temp[$key]['a_isi']='<span class="idname hidden">'.$value['a_title'].'</span>'.$tmptext;
				$temp[$key]['a_date']=date('d-M-Y', strtotime($value['a_date'])).'<br/>'.date('H:i:s', strtotime($value['a_date']));
				($value['a_aktf']) ? $txtaktif = '<span class="label label-success">Active</span>' : $txtaktif = '<span class="label label-danger">Inactice</span>'; 
				$temp[$key]['a_aktf'] = $txtaktif;
				//manipulation menu
				$enc = $value['idarticle'];
				unset($temp[$key]['idarticle']);
				$temp[$key]['a_title']='<a href="'.base_url('Organizer/Article/readarticle?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Read Article"><b>'.$value['a_title'].'</b></a>';
				$temp[$key]['menu']='<div class="btn-group" aria-label="Template Menu" role="group"><a href="'.base_url('Organizer/Article/readarticle?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn btn-primary btn-sm" title="Read"><i class="fa fa-list-alt"></i></a>'.
				'<a href="'.base_url('Organizer/Article/editarticle?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></a>'.
				'<a href="#" data-href="'.base_url('Organizer/Article/deletearticle?id=').$enc.'" alt="Delete Data" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete" title="Delete"><i class="fa fa-trash"></i></a></div>';
				
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
			$data['factselected'] = site_url('Organizer/Article/updateselected');
		
		// ============= import form ==============
			$data['finfile']= form_upload(array(	'name'=>'fimport',
							'class'=>'btn btn-info btn-sm',
							'required'=>'required'));
			$data['fbtnimport']= form_submit(array(	'value'=>'Import',
							'class'=>'btn btn-primary',							
							'id'=>'subb'));
			$data['factimp'] = site_url('Organizer/Article/importxls');
			
			
		
		// ============= export form ==============
				$optcol = array(
						'a_date' => 'Article Created',
						'a_title' => 'Article Title',
						'category' => 'Category',
						'a_isi' => 'Article Content',
						'a_aktf' => 'Active',
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
			$data['factexp'] = site_url('Organizer/Article/exportxls');
			
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Article/printtmp');
		
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
			$data['fsendper'] = site_url('Organizer/Article/savesetting');
				
			
			
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','summernote/summernote');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Article";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/article/atcllist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addarticle(){
	$id=$this->input->get('id');
	$colq=['a_title','category','a_isi','a_aktf'];
	//============ form edit quest ===========
		$fatcltitle =  array('name'=>'fatcltitle',
						'id'=>'fatcltitle',
						'required'=>'required',
						'placeholder'=>'Article Title',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($fatcltitle);
		
			$optatclcat = $this->Matcl->getoptatclcat();
		$fatclcat =  array('name'=>'fatclcat',
						'id'=>'fatclcat',
						'required'=>'required',
						'class'=>'form-control');
		$faddcat =  array('name'=>'faddcat',
						'id'=>'faddcat',
						'placeholder'=>'Add Category Article',
						'value'=>'',
						'class'=>'form-control');
		$fbtnaddcat =  array('name'=>'fbtnaddcat',
						'id'=>'fbtnaddcat',
						'role'=>'button',
						'value'=>'Add Category',
						'type'=>'button',
						'class'=>'btn btn-info btn-sm');
		$r[]=form_dropdown($fatclcat,$optatclcat).'
								<div class="input-group">
								<a href="#collapseaddcat" data-toggle="collapse" id="btncat" class="collapsed"> 
								<span class="btn btn-default btn-sm">
								<i class="fa fa-plus text-info"></i>
								<i class="text-info">Add Category</i>
								</span>
								</a>
								</div>
								<div class="col-md-12" id="reportadd"></div>
								<br/><div id="collapseaddcat" class="collapse"><div class="col-md-10">'.form_input($faddcat).'</div><div class="col-md-2">'.form_submit($fbtnaddcat).'</div></div>';
		
		
		$fcont = array('name'=>'fatclcont',
						'id'=>'fatclcont',
						'required'=>'required',
						'placeholder'=>'Article Content',
						'value'=>'',
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
			$optaktif = array(
						'1' => 'Active',
						'0' => 'Inactive');
		$faktif = array('name'=>'fatclactive',
						'id'=>'fatclactive',
						'required'=>'required',
						'class'=>'form-control');
		$r[]=form_dropdown($faktif,$optaktif);
		
		$fsend = array(	'id'=>'addatcl',
						'value'=>'Add Article',
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
		
		$this->load->view('dashboard/org/article/addatcl', $data);
	}
	
	public function editarticle(){
	//fecth data from db
		$col=['a_title','article.idcat','a_isi','a_aktf'];
		$id = $this->input->get('id');
		$dbres = $this->Matcl->detailatcl($col,$id);
		$this->load->library('Converttime');
		$colq = $this->returncolomn($col);
	//============ form edit quest ===========
		$fatcltitle =  array('name'=>'fatcltitle',
						'id'=>'fatcltitle',
						'required'=>'required',
						'placeholder'=>'Article Title',
						'value'=>$dbres[0]['a_title'],
						'class'=>'form-control');
		$r[]=form_input($fatcltitle);
		
		$optatclcat = $this->Matcl->getoptatclcat();
		$fatclcat =  array('name'=>'fatclcat',
						'id'=>'fatclcat',
						'required'=>'required',
						'class'=>'form-control');
		$r[]=form_dropdown($fatclcat,$optatclcat,$dbres[0]['idcat']);
		
		$fcont = array('name'=>'fatclcont',
						'id'=>'fatclcont',
						'required'=>'required',
						'placeholder'=>'Article Content',
						'value'=>htmlspecialchars_decode($dbres[0]['a_isi']),
						'rows'=>3,
						'cols'=>6,
						'class'=>'form-control');
		$r[]=form_textarea($fcont);
		
			$optaktif = array(
						'1' => 'Active',
						'0' => 'Inactive');
		$faktif = array('name'=>'fatclactive',
						'id'=>'fatclactive',
						'required'=>'required',
						'class'=>'form-control');
		$r[]=form_dropdown($faktif,$optaktif,$dbres[0]['a_aktf']);
		
		$fsend = array(	'id'=>'updateatcl',
						'value'=>'Update Article',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inid'] = form_hidden('fid',$id);
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($colq);
		//set table article
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
		
		$this->load->view('dashboard/org/article/editatcl', $data);
	}
	
	public function readarticle(){
		//fecth data from db
		$col = ['a_date','a_isi','category','uname','a_title','a_aktf','a_view'];
		$id = $this->input->get('id');
		$dbres = $this->Matcl->detailatcl($col,$id);
		$this->load->library('Converttime');
		//set row title
		$row = $this->returncolomn($col);
		unset($row[3]);
		unset($row[4]);
		unset($row[5]);
		unset($row[6]);
		//set table template
		$tmpl = array ( 'table_open'  => '<div>',
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
		$dbres[0]['a_date'] = date('d-M-Y H:i:s', strtotime($dbres[0]['a_date']));
		$dbres[0]['a_isi'] = htmlspecialchars_decode($dbres[0]['a_isi']);
		($dbres[0]['a_aktf']) ? $aktf = "Active" : $aktf = "Inactive";
		
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<div class="col-md-12"><span class="text-primary"><b>'.$key.' :</b></span><br/>',
				"dtval"=>$dbres[0][$col[$a]].'<hr/></div>'
				);
			if ($key=='Article Created') {
				$dtable[$a] = array(
				"dtcol"=>'<div class="col-md-12 bg-primary"><h3>'.$dbres[0]['a_title'].' <small><span class="label label-info">('.$aktf.')</span></small></h3>',
				"dtval"=>'<code class="mailbox-read-time"><i class="fa fa-clock-o"></i> '.$dbres[0][$col[$a]].'</code><code class="pull-right"><i class="fa fa-user"></i> PIC: '.$dbres[0]['uname'].'</code><br/><div class="label label-default"><i class="fa fa-eye"></i> Total Views '.$dbres[0]['a_view'].'</div><hr/></div>'
				);
			}
			
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/article/readatcl', $data);
		
		
	}
	
	public function deletearticle(){
		$id = $this->input->get('id');
		$r = $this->Matcl->deleteatcl($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Article Success');
		} else{
		$this->session->set_flashdata('x','Delete Article Failed');
		} 
		redirect(base_url('Organizer/Article'));
	}
	
	public function savearticle(){
		$this->load->library('MY_input');
				// set new data variable
				$fdata = array(
					'a_date'=>date('Y-m-d H:i:s'),
					'a_title'=>$this->input->post('fatcltitle'),
					'idcat'=>$this->input->post('fatclcat'),
					'a_aktf'=>$this->input->post('fatclactive'),
					'a_isi'=>htmlspecialchars($this->input->post('fatclcont',false)),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Matcl->saveatcl($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Article Succes.') : $this->session->set_flashdata('x','Add Article Failed.');
			
		redirect(base_url('Organizer/Article'));
		
	}
	
	public function updatearticle(){
	$this->load->library('MY_input');
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'a_title'=>$this->input->post('fatcltitle'),
					'idcat'=>$this->input->post('fatclcat'),
					'a_aktf'=>$this->input->post('fatclactive'),
					'a_isi'=>htmlspecialchars($this->input->post('fatclcont',false)),
					'uuser'=>$this->session->userdata('user')
					);
			//update to database
			$hsl = $this->Matcl->updateatcl($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Article Succes.') : $this->session->set_flashdata('x','Update Article Failed.');
			
		redirect(base_url('Organizer/Article'));
		
	}
	
	public function importxls(){
            // config upload
            $config['upload_path'] = FCPATH.'temp_upload/';
            $config['allowed_types'] = 'xls';
            $config['max_size'] = '10000';
            $this->load->library('upload', $config);
 
            if ( (! $this->upload->do_upload('fimport')) or ($this->upload->data()['orig_name']!='ImportFormatArticle.xls') ){
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
					$aktf = ($objWorksheet->getCell('D'.($i+1))->getValue()=='Y') ? '1' : '0'; 
				   $dtxl[$i-1]['a_date'] = date("Y-m-d H:i:s");
                   $dtxl[$i-1]['a_title'] = $objWorksheet->getCell('A'.($i+1))->getValue();
                   $dtxl[$i-1]['idcat'] = $objWorksheet->getCell('B'.($i+1))->getValue();
                   $dtxl[$i-1]['a_isi'] = htmlspecialchars($objWorksheet->getCell('C'.($i+1))->getValue());
                   $dtxl[$i-1]['a_aktf'] = $aktf;
                   $dtxl[$i-1]['uuser'] = $this->session->userdata('user');
				 
				 }
              }
			  
			  //save data through model
			  $report = $this->Matcl->tmpimportdata($dtxl);
 
              
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
		redirect(base_url('Organizer/Article'));
        
    }
		
	public function exportxls(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['a_date','a_title','category','a_isi','a_aktf','uname']; 
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Matcl->exportatcl($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Matcl->exportatcl(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		//change header data
		$dtcol = $this->returncolomn($dtcol);
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Article Data');
	
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
						($k == 'a_isi') ?	$objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,strip_tags(htmlspecialchars_decode($v))) : null;
						($k == 'a_aktf') ?	(($v==true) ? $objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,'Active') : $objPHPExcel->getActiveSheet()->setCellValue($Dcol.$Drow,'Inactive') ) : null;
						
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
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'ARTICLE DATA ('.$title.')');
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
		header('Content-Disposition: attachment;filename="Article Data ('.$title.').xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
		
	}
	
	public function predefinedimport(){
		$dtcol = ['Article Title','Category ID','Article Content', 'Active (Y/N)']; 
		
		//Create a new Object
		$this->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('ImportFormatArticle');
		
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
		
		//set colomn header Category
		$objPHPExcel->getActiveSheet()->setCellValue($tempcol.($Hrow),"Category ID");			
		$objPHPExcel->getActiveSheet()->getStyle($tempcol.$Hrow)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($tempcol)+1).($Hrow),"Category");
		$objPHPExcel->getActiveSheet()->getStyle(chr(ord($tempcol)+1).$Hrow)->getFont()->setBold(true);
		
		$dtcat= $this->Matcl->getcat();
		
		$Hrow=$Hrow+1;
		
		foreach ($dtcat as $al=>$val){
			$Dcol=$tempcol;
			
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($Dcol.$Hrow,$val['idcat'],PHPExcel_Cell_DataType::TYPE_STRING);
				$objPHPExcel->getActiveSheet()->setCellValue(chr(ord($Dcol)+1).$Hrow,$val['category']);
			$Hrow++;
		}
		$lastcol = chr(ord($Dcol)+2);
		$Bordercol = chr(ord($lastcol)-2);
		$objPHPExcel->getActiveSheet()->setCellValue($lastcol.($Hrow-1),"Remember: Always Put 'Category ID' instead of 'Category' in Colomn 'Category ID'");			
		
		
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
		header('Content-Disposition: attachment;filename="ImportFormatArticle.xls');
		header('Cache-Control: max-age=0');
		$objWriter->save('php://output');
	}
	
	public function printtmp(){
		//catch column value
		if ($this->input->post('fcolomn')!=null){
		foreach($this->input->post('fcolomn') as $selected)
		{$dtcol[] = $selected;}
		} else {
		$dtcol = ['a_date','a_title','category','a_isi','a_aktf','uname'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Matcl->exportatcl($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Matcl->exportatcl(null,null,$dtcol);
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
					(array_key_exists('a_date',$val)) ? $dexp[$key]['a_date']=date('d-M-Y',strtotime($val['a_date'])).'<br/>'.date('H:i:s',strtotime($val['a_date'])): null;
					(array_key_exists('a_isi',$val)) ? $dexp[$key]['a_isi']=strip_tags(htmlspecialchars_decode($val['a_isi'])) : null;
					(array_key_exists('a_aktf',$val)) ? $dexp[$key]['a_aktf'] = ($val['a_aktf']==true) ? 'Active' : 'Inactive' : null;
					
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Article Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/Article/printtmp', $data);
		
	}
	
	public function categorylist(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['idcat','cat_icon','category'];
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
			$rows = $this->Matcl->countcat($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Matcl->countcat();	
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
						'category' => 'Category'
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
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'Organizer/Article/categorylist?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Article/categorylist?'.$addrpage.'view=';
		$data["perpage"] = ['10','25','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
	
		$temp = $this->Matcl->datacat($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser',
							'value'=>$temp[$key]['idcat']
							));
				array_unshift($temp[$key],$ctable);
				$temp[$key]['cat_icon'] = '<span class="fa '.$temp[$key]['cat_icon'].'"></span>';
				$temp[$key]['category']='<span class="idname">'.$temp[$key]['category'].'</span>';
				//manipulation menu
				$enc = $value['idcat'];
				unset($temp[$key]['idcat']);
				$temp[$key]['menu']='<small><a href="'.base_url('Organizer/Article/editcat?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a> | '.
				'<a href="#" data-href="'.base_url('Organizer/Article/delcat?id=').$enc.'" alt="Delete Data" class="btn-danger btn-sm" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash"></i> Delete</a></small>';
				}
		$data['listdata'] = $this->table->generate($temp);
		
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
			$data['factselected'] = site_url('Organizer/Article/updateselectedcat');
			
		//=============== Template ============
		$data['jsFiles'] = array('selectpicker/select.min'
							);
		$data['cssFiles'] = array('selectpicker/select.min'
							);  
		// =============== view handler ============
		$data['title']="Article Category";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/article/catlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function addcat(){
	$id=$this->input->get('id');
	$colq=['cat_icon','category'];
	//============ form add category ===========
		$opticon = array(
				"fa-newspaper-o",
				"fa-bullhorn",
				"fa-home",
				"fa-key",
				"fa-legal",
				"fa-pencil",
				"fa-money",
				"fa-book",
				"fa-bullhorn",
				"fa-file-text",
				"fa-envelope-square",
				"fa-envelope",
				"fa-certificate",
				"fa-check-square-o",
				"fa-calendar",
				"fa-clock-o",
				"fa-edit",
				"fa-check"
				);
		$selecticon = '<select name="fcaticon" id="fcaticon" class="form-control selectpicker" required="required">';

		foreach ($opticon as $k => $v) {
			$selecticon .= '<option value="'.$v.'" data-icon="'.$v.'"></option>';
		}
		$selecticon .= "</select>";
		$r[] = $selecticon;

		$fatcltitle =  array('name'=>'fcat',
						'id'=>'fcat',
						'required'=>'required',
						'placeholder'=>'Category',
						'value'=>'',
						'class'=>'form-control');
		$r[]=form_input($fatcltitle);
		
		$fsend = array(	'id'=>'addcat',
						'value'=>'Add Category',
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
		
		$this->load->view('dashboard/org/Article/addcat', $data);
	}
	
	public function editcat(){
	//fecth data from db
		$col=['cat_icon','category'];
		$id = $this->input->get('id');
		$dbres = $this->Matcl->detailcat($col,$id);
		$colq = $this->returncolomn($col);
	//============ form edit quest ===========
		$opticon = array(
				"fa-newspaper-o",
				"fa-bullhorn",
				"fa-home",
				"fa-key",
				"fa-legal",
				"fa-pencil",
				"fa-money",
				"fa-book",
				"fa-bullhorn",
				"fa-file-text",
				"fa-envelope-square",
				"fa-envelope",
				"fa-certificate",
				"fa-check-square-o",
				"fa-calendar",
				"fa-clock-o",
				"fa-edit",
				"fa-check"
				);
		$selecticon = '<select name="fcaticon" id="fcaticon" class="form-control selectpicker" required="required">';

		foreach ($opticon as $k => $v) {
			($dbres[0]['cat_icon']==$v) ? $select = 'selected' : $select='';
			$selecticon .= '<option value="'.$v.'" data-icon="'.$v.'" '.$select.'></option>';
		}
		$selecticon .= "</select>";
		$r[] = $selecticon;


		$fatcltitle =  array('name'=>'fcat',
						'id'=>'fcat',
						'required'=>'required',
						'placeholder'=>'Category',
						'value'=>$dbres[0]['category'],
						'class'=>'form-control');
		$r[]=form_input($fatcltitle);
		
		
		$fsend = array(	'id'=>'updateatcl',
						'value'=>'Update Category',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inid'] = form_hidden('fid',$id);
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$row = $this->returncolomn($colq);
		//set table article
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
		
		$this->load->view('dashboard/org/Article/editcat', $data);
	}
	
	public function savecat(){
			// set new data variable
				$fdata = array(
					'cat_icon'=> $this->input->post('fcaticon'),
					'category'=>$this->input->post('fcat')
					);
			//update to database
			$hsl = $this->Matcl->savecat($fdata);
			($hsl) ? $this->session->set_flashdata('v','Add Article Category Succes.') : $this->session->set_flashdata('x','Add Article Category Failed.');
			
		redirect(base_url('Organizer/Article/categorylist'));
		
	}
	
	public function delcat(){
		$id = $this->input->get('id');
		$r = $this->Matcl->deletecat($id);
	if ($r){
		$this->session->set_flashdata('v','Delete Article Category Success');
		} else{
		$this->session->set_flashdata('x','Delete Article Category Failed');
		} 
		redirect(base_url('Organizer/Article/categorylist'));
	}
	
	public function addcategory(){
		if ($this->input->post('category')!=''){
			$fdata = array(
					'cat_icon'=>'fa-newspaper-o',
					'category'=>$this->input->post('category')
					);
			//update to database
			$hsl = $this->Matcl->savecat($fdata);
			($hsl) ? print($hsl) : print($hsl);
		} else {
			redirect(base_url('Organizer/Article/categorylist'));
		}
	}
	
	public function updatecat(){
	$id = $this->input->post('fid');
	// set new data variable
				$fdata = array(
					'cat_icon'=> $this->input->post('fcaticon'),
					'category'=>$this->input->post('fcat')
					);
			//update to database
			$hsl = $this->Matcl->updatecat($fdata,$id);
			($hsl) ? $this->session->set_flashdata('v','Update Category Succes.') : $this->session->set_flashdata('x','Update Category Failed.');
			
		redirect(base_url('Organizer/Article/categorylist'));
		
	}
	
	public function updateselected(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Matcl->deletetmp($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article Success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Failed.');
		}
		redirect(base_url('Organizer/Article'));
	}
	public function updateselectedcat(){
		if($this->input->post('fusers')!=''){
				$users = $this->input->post('fusers');
				$dtuser= explode(',',$users);
				$totuser = count($dtuser);
		foreach($dtuser as $k=>$v){
					$r = $this->Matcl->deletecat($v);
					
				($r) ? $tot++ : $failed[]=$v;
			}
			$this->session->set_flashdata('v','Delete '.$totuser.' Selected Article Category Success.<br/>Details: '.$tot.' success and '.count($failed).' error(s)');
		} else{
		$this->session->set_flashdata('x','No data selected, delete Selected Article Category Failed.');
		}
		redirect(base_url('Organizer/Article'));
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
		redirect(base_url('Organizer/Subject'));
	}
	
	public function returncolomn($header) {
	$find=['a_date','a_title','a_isi','category','article.idcat','cat_icon','a_aktf','a_view','uname'];
	$replace = ['Article Created', 'Article Title','Article Content','Category','Category','Icon','Active','Total View','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	

}
