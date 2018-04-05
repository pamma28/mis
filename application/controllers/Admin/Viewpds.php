<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Viewpds extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation'));
		$this->load->helper(array('form','url'));
		
		$this->load->model('Mpds');
    }

	public function index(){
		$this->pdsdata();				
	}
	
	public function pdsdata() {		
		//===================== table handler =============
		$column=['ucreated','uuser','uname','fname','unim','uhp','ustatus','ulunas'];
		$header = $this->returncolomn($column);
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
		$data["urlperpage"] = base_url().'Admin/Viewpds/pdsdata?view=';
		$config = array(
				'base_url' => $data['urlperpage'].$offset.$addrpage,
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
		$data["perpage"] = ['10','25','50','100','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
		$temp = $this->Mpds->datapds($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
					$temp[$key]['ucreated'] = date('d-M-Y H:i:s',strtotime($value['ucreated']));
				//manipulation allow data
				if($value['ulunas']){
					$temp[$key]['ulunas']='<span class="label label-success">Fully Paid</span>';
				} else{
					$temp[$key]['ulunas']='<span class="label label-warning">Not yet</span>';
				}
				$temp[$key]['ustatus']= str_replace(',', '<br/>', $value['ustatus']);
				//manipulation menu
				$enc = $value['uuser'];
				$temp[$key]['menu']='<small><a href="'.base_url('Admin/Viewpds/pdsdetail?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a></small>';
				
				}
		$data['datalist'] = $this->table->generate($temp);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							);
		$data['cssFiles'] = array(
							);  
		// =============== view handler ============
		$data['title']="Registration Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/pds/pdslist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function pdsdetail(){
		//fecth data from db
		$col = ['ufoto','ucreated','uupdate','uuser','uname','ubplace','ubdate','jkname','unim','fname','uemail','uhp','ubbm','uaddrnow','uaddhome','umin','ustatus','ulunas'];
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
		$this->load->view('dashboard/admin/pds/pdsdetail', $data);
		
		
	}
	
	
	public function returncolomn($header) {
	$find=['ucreated','uupdate','uuser','uname','ubplace','ubdate','jkname','unim','uemail','uhp','ufoto','ubbm','uaddrnow','uaddhome','umin','upaycode','ustatus','ulunas','fac.idfac','idfac','fname'];
	$replace = ['Date Registered','Last Updated','Username','Full Name','Birth Place','Birth Date','Gender','NIM','Email','Phone Number','Photo','BBM/Social Media','Current Address','Home Address','Member Index Number','Payment','Status','Full Payment','Faculty','Faculty','Faculty'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
