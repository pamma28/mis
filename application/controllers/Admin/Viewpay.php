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
		$column=['idtrans','tdate','tnotrans','a.uname as mem','a.unim','transname','tpaid','b.uname'];
		$header = $this->returncolomn($column);
		unset($header[0]);
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
			unset($filter['id']);
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
			$rows = $this->Mpay->countpay($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mpay->countpay();	
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
						'tdate' => 'Date Issued',
						'tnotrans' => 'Invoice Number',
						'tnomi' => 'Cash Given',
						'tpaid' => 'Nominal Paid',
						'uname' => 'Name',
						'unim' => 'NIM',
						'pic' => 'PIC',
						'valid_to' => 'Valid Date'
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
		$adv['Date Issued '] = form_input(
						array('name'=>'tdate',
						'id'=>'sissuedon',
						'placeholder'=>'Date Issued (YYYY-MM-DD)',
						'value'=>isset($tempfilter['tdate']) ? $tempfilter['tdate'] : null,
						'class'=>'form-control'));
		
		$adv['Invoice Number'] = form_input(
						array('name'=>'tnotrans',
						'id'=>'snotrans',
						'placeholder'=>'Invoice Number',
						'value'=>isset($tempfilter['tnotrans']) ? $tempfilter['tnotrans'] : null,
						'class'=>'form-control'));
		
		$adv['Full Name'] = form_input(
						array('name'=>'uname',
						'id'=>'sfullname',
						'placeholder'=>'Full Name',
						'value'=>isset($tempfilter['uname']) ? $tempfilter['uname'] : null,
						'class'=>'form-control'));
		
		$adv['NIM'] = form_input(
						array('name'=>'unim',
						'id'=>'sNIM',
						'placeholder'=>'NIM',
						'value'=>isset($tempfilter['unim']) ? $tempfilter['unim'] : null,
						'class'=>'form-control'));
		$adv['Nominal Paid'] = form_input(
						array('name'=>'tpaid',
						'id'=>'spaid',
						'placeholder'=>'Nominal Paid',
						'value'=>isset($tempfilter['tpaid']) ? $tempfilter['tpaid'] : null,
						'class'=>'form-control'));
		
		$adv['Cash Given'] = form_input(
						array('name'=>'tnomi',
						'id'=>'scash',
						'placeholder'=>'Cash Given',
						'value'=>isset($tempfilter['tnomi']) ? $tempfilter['tnomi'] : null,
						'class'=>'form-control'));
		
		$adv['Nominal Change'] = form_input(
						array('name'=>'tchange',
						'id'=>'schange',
						'placeholder'=>'Nominal Change',
						'value'=>isset($tempfilter['tchange']) ? $tempfilter['tchange'] : null,
						'class'=>'form-control'));
						
			$opttrans = $this->Mpay->optjtrans();
		$adv['Transaction Type'] = form_dropdown(
						array('name'=>'idjnstrans',
						'id'=>'sjnstrans',
						'placeholder'=>'Transaction Type',
						'value'=>'',
						'class'=>'form-control'),$opttrans,isset($tempfilter['idjnstrans']) ? $tempfilter['idjnstrans'] : null);
		
		$adv['Fully Paid/Not'] = form_dropdown(array(
							'name'=>'ulunas',
							'id'=>'slunas',
							'class'=>'form-control'),
							array(''=>'No filter','1'=>'Fully Paid',
							'0'=>'Not Yet'),isset($tempfilter['ulunas']) ? $tempfilter['ulunas'] : null);
		$adv['PIC Name'] = form_input(
						array('name'=>'pic',
						'id'=>'spicname',
						'placeholder'=>'PIC Name',
						'value'=>isset($tempfilter['pic']) ? $tempfilter['pic'] : null,
						'class'=>'form-control'));
		$adv['Invoice Valid Until'] = form_input(
						array('name'=>'valid_to',
						'id'=>'svalid',
						'placeholder'=>'Date Valid (YYYY-MM-DD)',
						'value'=>isset($tempfilter['valid_to']) ? $tempfilter['valid_to'] : null,
						'class'=>'form-control'));
		
		$dtfilter = '';
		
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
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
		$temp = $this->Mpay->datapay($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				$temp[$key]['tdate']=date('d-M-Y H:i:s',strtotime($temp[$key]['tdate']));
				$temp[$key]['tpaid']=$this->convertmoney->convert($temp[$key]['tpaid']);
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
		$col = ['tnotrans','tdate','valid_to','transname','a.uname as mem','a.unim','tnomi','tpaid','tchange','b.uname'];
		$id = $this->input->get('id');
		$dbres = $this->Mpay->detailpay($col,$id);
		
		//set row title
		$row = $this->returncolomn($col);
		$col[4]='mem';
		$col[5]='unim';
		$col[9]='uname';
		
		
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
	$find=['tdate','valid_to','b.uname','a.uname as mem','a.unim','tnotrans','tnomi','tpaid','tchange','transname'];
	$replace = ['Payment Date','Valid To','PIC Payment','Full Name','NIM','No Transaction','Nominal Given','Nominal Paid','Nominal Change','Transaction Type'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
