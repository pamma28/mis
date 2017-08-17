<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Viewpay extends Admin_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney'));
		$this->load->helper(array('form','url'));
		
		$this->load->model('Mpay');
    }

	public function index(){
		$this->paydata();				
	}
	
	public function paydata() {		
		//===================== table handler =============
		$column=['idtrans','tdate','tnotrans','uname','unim','transname','tpaid','use_uuser'];
		$header = $this->returncolomn($column);
		unset($header[0]);
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
				$rows = $this->Mpay->countpay($col,$filter);
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
		
		
		//================ filter handler ================
		
		
		$fq = array('name'=>'search',
						'id'=>'search',
						'placeholder'=>'Search Here',
						'value'=>$filter,
						'class'=>'form-control');
		$data['inq'] = form_input($fq);
			$optf = array(
						'ucreated' => 'Date Created',
						'uuser' => 'Username',
						'uname' => 'Name',
						'unim' => 'NIM',
						'uhp' => 'Phone Number'
						);
		$fc = array('name'=>'column',
						'id'=>'col',
						'class'=>'form-control'
					);
		$data['inc'] = form_dropdown($fc,$optf,$col);
		$data['inv'] = form_hidden('view',$offset);
		
		$fbq = array(	'id'=>'bsearch',
						'value'=>'search',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['bq'] = form_submit($fbq);
		
		
		//=============== paging handler ==========
		$data["urlperpage"] = base_url().'Admin/Viewpay/paydata?view=';
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
		$temp = $this->Mpay->datapay($column,$config['per_page'],$perpage,$col,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				if($value['tpaid']!=''){
					$temp[$key]['tpaid']=$this->convertmoney->convert($value['tpaid']);
				} else{
					$temp[$key]['tpaid']=' - ';
				}
				//manipulation menu
				$enc = $value['idtrans'];
				$temp[$key]['menu']='<small><a href="'.base_url('Admin/Viewpay/paydetail?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a></small>';
				unset($temp[$key]['idtrans']);
				}
		
		$data['datalist'] = $this->table->generate($temp);
		
		//=============== Template ============
		$data['jsFiles'] = array(
							);
		$data['cssFiles'] = array(
							);  
		// =============== view handler ============
		$data['title']="Payment Data";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/admin/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/admin/pay/paylist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function paydetail(){
		//fecth data from db
		$col = ['tnotrans','tdate','valid_to','transname','uname','unim','tnomi','tpaid','tchange','use_uuser'];
		$id = $this->input->get('id');
		$dbres = $this->Mpay->detailpay($col,$id);
		
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
		array_unshift($row,'QR Code'); //set qr label 
		array_unshift($col,'QR Code'); //set qr label 
		$notrans = $dbres[0]['tnotrans'];
		$dbres[0]['QR Code']= '<img src="'.base_url('upload/qr/'.$notrans).'.png" class="img-thumbnail" style="height:100px;"/>'; //set qr data 		
		
		$a = 0;
		foreach($row as $key)
		{
			$dtable[$a] = array(
				"dtcol"=>'<b>'.$key.'</b>',
				"dtval"=>' : '.$dbres[0][$col[$a]]
				);
				
			if (($key=='Nominal Given') or ($key=='Nominal Paid') or ($key=='Nominal Change')){
					$dtable[$a] = array(
						"dtcol"=>'<b>'.$key.'</b>',
						"dtval"=>' : '.$this->convertmoney->convert($dbres[0][$col[$a]])
						);
					} 
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/admin/pay/paydetail', $data);
		
	}
	
	public function returncolomn($header) {
	$find=['tdate','valid_to','use_uuser','uname','unim','tnotrans','tnomi','tpaid','tchange','transname'];
	$replace = ['Payment Date','Valid To','PIC Payment','Full Name','NIM','No Transaction','Nominal Given','Nominal Paid','Nominal Change','Transaction Type'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
