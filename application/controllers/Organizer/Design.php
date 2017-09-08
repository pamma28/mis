<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Design extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Convertmoney'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mcerti','Msetting'));
    }

	public function index(){
		//===================== table handler =============
		$data['thisperiod']=$this->Msetting->getset('period');
		$column=['iddes','desdateup','desname','desfile','cerdefault','desnote','a.uname as uploader','b.uname as pic'];
		$header=array();
		// checkbox checkalldata
				$data['checkall'] = form_checkbox(array(
							'name'=>'checkall',
							'class'=>'',
							'value'=>'all',
							'id'=>'c_all'
							));
		
		//================== catch all value ================
		$durl= $_SERVER['QUERY_STRING'];
		parse_str($durl, $filter);
		$tempfilter=$filter;
		$addrpage = '';
		$offset= isset($tempfilter['view']) ? $tempfilter['view'] : 6;
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
			$rows = $this->Mcerti->countdesign($filter);
			
		} else {
			//count rows of data (no filter/search)
			$rows = $this->Mcerti->countdesign();	
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
						'desdateup' => 'Date Uploaded',
						'desfile' => 'Design Name',
						'desnote' => 'Design Note',
						'a.uname' => 'Uploader',
						'cerdefault' => 'Default (1/0)'
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
						'id'=>'dperiod',
						'placeholder'=>'Period',
						'value'=>isset($tempfilter['period']) ? $tempfilter['period'] : null,
						'class'=>'form-control'));
		$adv['Design Uploaded '] = form_input(
						array('name'=>'desdateup',
						'id'=>'ddateup',
						'placeholder'=>'Date Uploaded (YYYY-MM-DD)',
						'value'=>isset($tempfilter['desdateup']) ? $tempfilter['desdateup'] : null,
						'class'=>'form-control'));
		
		$adv['Design Name'] = form_input(
						array('name'=>'desfile',
						'id'=>'dfile',
						'placeholder'=>'Design Name',
						'value'=>isset($tempfilter['desfile']) ? $tempfilter['desfile'] : null,
						'class'=>'form-control'));
		
		$adv['Design Note'] = form_textarea(
						array('name'=>'desnote',
						'id'=>'dnote',
						'rows'=>'2',
						'cols'=>'8',
						'placeholder'=>'Desgin Note',
						'value'=>isset($tempfilter['desnote']) ? $tempfilter['desnote'] : null,
						'class'=>'form-control'));
		
		$adv['Uploader'] = form_input(
						array('name'=>'a.uname',
						'id'=>'duploader',
						'placeholder'=>'Uploader',
						'value'=>isset($tempfilter['a.uname']) ? $tempfilter['a.uname'] : null,
						'class'=>'form-control'));
		
		$adv['Default/Not'] = form_dropdown(array(
							'name'=>'cerdefault',
							'id'=>'ddefault',
							'class'=>'form-control'),
							array(''=>'Not','1'=>'Default'));
		
		$dtfilter = '';
		
		foreach($adv as $a=>$v){
			$dtfilter = $dtfilter.'<div class="input-group"><label>'.$a.': </label>'.$v.'</div>  ';
		}
		$data['advance'] = $dtfilter;
		
		
		//=============== paging handler ==========
		$config = array(
				'base_url' => base_url().'/Organizer/Design?'.$addrpage.'view='.$offset,
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
		$data["urlperpage"] = base_url().'Organizer/Design?'.$addrpage.'view=';
		$data["perpage"] = ['1','6','36','96','all'];
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$data["links"] = explode('&nbsp;',$str_links );

		//========== data manipulation =========
		$result='';
		$temp = $this->Mcerti->datadesign($column,$config['per_page'],$perpage,$filter);	
				foreach($temp as $key=>$value){
				//manipulation allow data
				$temp[$key]['desdateup']= date('d-M-Y H:i:s',strtotime($temp[$key]['desdateup']));
				//manipulation checkbox
				$ctable = form_checkbox(array(
							'name'=>'check[]',
							'class'=>'ciduser design-check',
							'value'=>$temp[$key]['iddes']
							));
				$tempfile=$temp[$key]['desfile'];
				$temp[$key]['desname']='<span class="idname">'.$temp[$key]['desname'].'</span>';
				//manipulation menu
				$enc = $value['iddes'];
				unset($temp[$key]['iddes']);
				//set row
				if (($key%2)==0){
				$result=$result.'';
				}
				//set default certificate
				($temp[$key]['cerdefault']==1) ? $cerdef='<div class="design-default">
							<p class="text-success text-center"><span class="fa fa-check"></span> <b>Default</b></p>
							</div>': $cerdef='';
				($temp[$key]['cerdefault']==0) ? $btndefault='<a data-target=".bs-selecteddata" data-toggle="modal" data-title="Make Default" data-icon="fa fa-check" data-btn="btn btn-sm btn-success" data-finput="2" data-fconfirm="'.$enc.'" class="btn btn-sm text-success" href="#"><i class="fa fa-check"></i> Make Default</a><br/>':$btndefault='';
				//set template gallery
				$result=$result.'<div class="col-sm-6 col-md-4 col-xs-12">
						<!-- normal -->
		
							<div class="pull-left">
							'.$ctable.$cerdef.'
							<div class="hidden">'.$temp[$key]['desname'].'</div>
							</div>

							<div class="pull-left design-toogle">
							<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-expanded="false">
							<span class="fa fa-gear"></span>
							<span class="sr-only">Toggle Dropdown</span>
							</button>
							<div class="dropdown-menu design-setting text-right" role="menu">
								'.$btndefault.'
								<a href="'.base_url('Organizer/Design/preview?id=').$enc.'" alt="Print Preview Data" class="btn btn-sm text-info" role="button" data-toggle="modal" data-target="#DetailModal"><i class="fa fa-print"></i> Print</a><br/>
								<a href="'.base_url('Organizer/Design/editdesign?id=').$enc.'" alt="Edit Data" class="btn btn-sm text-primary" role="button" data-toggle="modal" data-target="#DetailModal"><i class="fa fa-edit"></i> Edit</a><br/>
								<a href="#" data-href="'.base_url('Organizer/Design/deletedesign?id=').$enc.'" alt="Delete Data" data-toggle="modal" data-target="#confirm-delete" class="btn btn-sm text-danger"><i class="fa fa-trash-o"></i> Delete</a>
							</div>	
							</div>

						<div class="design-menu">
							<div class="col-md-12 col-sm-12 col-xs-12">
							<div class="ih-item square effect6 from_top_and_bottom">
							<a href="#">
								<div class="img"><img src="'.base_url('upload/design/'.$tempfile).'" alt="img">
								</div>
								<div class="info">
									<h3>'.$temp[$key]['desname'].'</h3>
									<h4><small>Uploaded by '.$temp[$key]['uploader'].'<br/> on '.$temp[$key]['desdateup'].'</small></h4>
									<p>'.$temp[$key]['desnote'].'</p>
								</div>
							</a>
						</div>

						</div>
					</div>
					
					
					<!-- end normal -->
				</div>';
				
				$temp[$key]['menu']='<small><a href="'.base_url('Organizer/Payment/detailpay?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Full Data" class="btn-primary btn-sm"><i class="fa fa-list-alt"></i> Details</a> | '.
				'<a href="'.base_url('Organizer/Payment/editpay?id=').$enc.'" data-target="#DetailModal" data-toggle="modal" role="button" alt="Edit Data" class="btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a> | </small>';
				
				//set close row
				if (($key%2)==0){
				$result=$result;
				}
				}
		$data['listdata'] = $result;
		
		// ======== default design ==============
			$data['idac']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedid',
								'name'=>'fid'
								));
			$data['idtype']= form_input(
								array('type'=>'hidden',
								'id'=>'selectedtype',
								'name'=>'ftype'
								));
			$data['factselected'] = site_url('Organizer/Design/updateselected');
		
		
		//=============== print handler ============
			$data['fbtnprint']= form_submit(array('value'=>'Print',
								'class'=>'btn btn-primary',							
								'id'=>'subb'));
			$data['factprint'] = site_url('Organizer/Design/printdesign');
		
		//=============== setting registration phase ============
			$price = $this->Msetting->getset('price');
			$data['fprice']= form_input(array('id'=>'price',
								'class'=>'form-control',						
								'name'=>'fprice',							
								'placeholder'=>'Registration Price',							
								'value'=>$price,							
								'required'=>'required'));
			$data['fbtnperiod']= form_submit(array('value'=>'Update Setting',
								'class'=>'btn btn-primary',							
								'id'=>'btnupdateset'));
			$data['fsendper'] = site_url('Organizer/Payment/savesetting');
				
		//=============== Template ============
		$data['jsFiles'] = array(
							'toggle/bootstrap2-toggle.min','print/printThis');
		$data['cssFiles'] = array(
							'toggle/bootstrap2-toggle.min','ihover/ihover.min');  
		// =============== view handler ============
		$data['title']="Certificate Design";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/design/designlist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	public function preview(){
		//fecth data from db
		$col = ['desname','desfile'];
		$id = $this->input->get('id');
		$dbres = $this->Mcerti->detaildesign($col,$id);
		
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
				"dtcol"=>'<div class="text-center"><h3>'.$dbres[0][$col[$a]].'</h3></div>'
				);
				
			if ($key=='Design File'){
				$dtable[$a] = array(
				"dtcol"=>'<div class="text-center"><img src="'.base_url('upload/design/'.$dbres[0][$col[$a]]).'" width=850px class="img-round"></div>'
				);
			
			}
			$a++;
		}
		$data['rdata']=$this->table->generate($dtable);
		
		// =============== view handler ============
		$this->load->view('dashboard/org/design/printpreview', $data);
		
		
	}
	
	public function adddesign(){
		//============ form add certificate design ===========
		$fdesign = array('name'=>'fdesfile',
						'id'=>'DesFile',
						'required'=>'required',
						'placeholder'=>'Design File',
						'value'=>'',
						'class'=>'btn btn-default');
		$r[] = form_upload($fdesign);
		
		$r[]= form_input(array(
						'name'=>'fdesname',
						'id'=>'DesName',
						'required'=>'required',
						'placeholder'=>'Design Name',
						'value'=>'',
						'class'=>'form-control'
						));
		
		$r[] = form_checkbox(array(
							'name'=>'fdef',
							'data-toggle'=>'toggle',
							'data-on'=>'<b>Default</b>',
							'data-off'=>'<b>Not Default</b>',
							'data-size'=>'small',
							'id'=>'iddefault',
							'checked'=>'',
							'value'=>'1')
							);
		
		
			$fdesc = array('name'=>'fdesc',
						'id'=>'Descript',
						'required'=>'required',
						'placeholder'=>'Description',
						'rows'=>'4',
						'cols'=>'10',
						'value'=>set_value('fdesc'),
						'class'=>'form-control');
		$r[] = form_textarea($fdesc);
		
		
		$fsend = array(	'id'=>'redi',
						'value'=>'Save',
						'class'=>'btn btn-primary',
						'type'=>'submit');
		$data['inbtn'] = form_submit($fsend);
		
		//set row title
		$col = ['desfile','desname','Default','Description'];
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
		
		$this->load->view('dashboard/org/design/adddesign', $data);
	}
	
	public function editdesign(){					
		// ============== Fetch data ============
		$col = ['desfile','desname','desnote'];
		$id = $this->input->get('id');
		$g = $this->Mcerti->detaildesign($col,$id);
		// ========= form edit ================ 
		$r[] = '<div style="max-height:200px" disabled><img src="'.base_url('upload/design/'.$g[0]['desfile']).'" height="200px" class="img-rounded"/></div>';
		
		$r[]= form_input(array(
						'name'=>'fdesname',
						'id'=>'DesName',
						'required'=>'required',
						'placeholder'=>'Design Name',
						'value'=>$g[0]['desname'],
						'class'=>'form-control'
						));
		
			$fdesnote = array('name'=>'desnote',
						'id'=>'designnote',
						'required'=>'required',
						'rows'=>5,
						'cols'=>5,
						'placeholder'=>'Design Note',
						'value'=>$g[0]['desnote'],
						'class'=>'form-control');
		$r[] = form_textarea($fdesnote);
		
		$data['inid'] = form_hidden('fiddes',$id);
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
		
		$this->load->view('dashboard/org/design/editdes', $data);
	
	
	}
	
	public function updatedesign(){
		if ($this->input->post('fiddes')!=null){
		$id = $this->input->post('fiddes');
		$fdtdes = array(
					'desname'=>$this->input->post('fdesname'),
					'use_uuser'=>$this->session->userdata('user'),
					'desnote'=>$this->input->post('desnote')
					);
		$r = $this->Mcerti->updatedes($fdtdes,$id);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Certificate Design Success');
		} else {		
		$this->session->set_flashdata('x','Update Certificate Design Failed');
		}
		redirect(base_url('Organizer/Design'));
	}
	

	public function updateselected(){
		if($this->input->post('fid')!=''){
				$id = $this->input->post('fid');
				$type = $this->input->post('ftype');
				$r = $this->Mcerti->updatedefault($id);
					$this->session->set_flashdata('v','Make Default Success');
				
		} else{
		$this->session->set_flashdata('x','No data selected, update Selected Member Account Failed.');
		}
		redirect(base_url('Organizer/Design'));
	}
		
	public function savedesign(){
		if($_FILES['fdesfile']['name']!=null){		
            $file_name = $_FILES['fdesfile']['tmp_name'];
			list($width,$height,$type,$attr) = getimagesize($file_name);
			
			// check image landscape and resize it if too big
			if($height<$width){

					// config upload
            		$maxDim = 1000;
					$fhashfile = md5(date('Y-m-d h:i:sa').$_FILES['fdesfile']['name']);
		            $config['upload_path'] = FCPATH.'upload/design/';
		            $config['allowed_types'] = 'jpeg|jpg|png';
		            $config['file_name'] = $fhashfile;
		            $config['max_size'] = 0;
		            $this->load->library('upload', $config);

		            //manipulate height n width if too big
						if (($height > $maxDim) or ($width>$maxDim)){
								$target_filename = $file_name;
					            $ratio = $width/$height;
					            if( $ratio > 1) {
					                $new_width = $maxDim;
					                $new_height = $maxDim/$ratio;
					            } else {
					                $new_width = $maxDim*$ratio;
					                $new_height = $maxDim;
					            }
					            $src = imagecreatefromstring( file_get_contents( $file_name ) );
					            $dst = imagecreatetruecolor( $new_width, $new_height );
					            imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
					            imagedestroy( $src );
					            // adjust format as needed
					            if (($type=='2') or ($type=='3')){
					            	imagejpeg($dst, $target_filename);
					            } else {
					            imagepng( $dst, $target_filename );
					            } 
								imagedestroy( $dst );
					            
						}

					// save file and add to database
					if ($this->upload->do_upload('fdesfile')){
						// set new design variable
						$fdtdes = array(
							'desfile'=>$this->upload->data()['file_name'],
							'desdateup'=>date("Y-m-d H:i:s"),
							'uuser'=>$this->session->userdata('user'),
							'desname'=>$this->input->post('fdesname'),
							'desnote'=>$this->input->post('fdesc'),
							'cerdefault'=>$this->input->post('fdef')
							);
						$t[]='Upload Design Success';
						//add to database
						$insertid = $this->Mcerti->savedesign($fdtdes);
						($insertid <> null) ? $t[]='Add Certificate Design Success' : $t[]='Add Certificate Design Failed'; 
						if ($fdtdes['cerdefault']){
							$this->Mcerti->updatedefault($insertid);
						}
				
					$this->session->set_flashdata('v',implode(' and ',$t));	
					} else {
					$t[]='Upload Certificate Design Failed';
					$this->session->set_flashdata('x',implode(' and ',$t));
					}
				
			} else {
				$this->session->set_flashdata('x','The Design Files Should in Lanscape.');	
			} 
		} else {
			$this->session->set_flashdata('x','Directly Access is not Allowed.');
		}
		redirect(base_url('Organizer/Design'));
	}

	public function deletedesign(){
		$id = $this->input->get('id');
		$filename = $this->Mcerti->getnamedes($id);
		$default = $this->Mcerti->getDefault($id);
		if(!$default){
				//delete file
				$path = FCPATH.'upload/design/' . $filename;
				unlink($path);
			$r = $this->Mcerti->deletedes($id);
	        unlink($path);		
			if ($r){
				$this->session->set_flashdata('v','Delete Success.');
				} else{
				$this->session->set_flashdata('x','Delete Failed.');
				}
		} else {
			$this->session->set_flashdata('x','Delete Failed, Default Certificate Design Can Not be Deleted.');	
		} 
		redirect(base_url('Organizer/Design'));
	}

	
	public function printdesign(){
		//catch column value
		$sumpaid ="(select sum(tpaid) from transaksi where uuser=a.uuser) as 'totpaid'";
		if ($this->input->post('fcolomn')!=null){
			foreach($this->input->post('fcolomn') as $selected)
			{ 
				if ($selected != 'totpaid'){
				$dtcol[] = $selected;
				} else {
				$dtcol[] = $sumpaid;
				}
			}
		} else {
		$dtcol = ['tnotrans','tdate','transname','a.uname as mname','a.unim','tpaid','tnomi','tchange','b.uname as rname','valid_to',$sumpaid,'a.ulunas'];
		}
		
		//check use date range
		if (null!=$this->input->post('fusedate')){
			$dtrange = $this->input->post('fdtrange');
			$dtstart = mb_substr($dtrange,0,10,'utf-8');
			$dtend = substr($dtrange,13);
			$dexp = $this->Mpay->exportpay($dtstart,$dtend,$dtcol);
			$title=$dtrange;
		}else {
			$dexp = $this->Mpay->exportpay(null,null,$dtcol);
			$title = Date('d-m-Y');
		}
		
		// config table
		$dtcol[3]='Member Name';
		$dtcol[4]='Member NIM';
		$dtcol[8]='PIC';
		$dtcol[10]='Total Paid';
		$dtcol[11]='Full Paid Status';
		$header = $this->returncolomn($dtcol);
		
		$tmpl = array ( 'table_open'  => '<table class="table table-bordered">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		//fetch data	
				foreach($dexp as $key=>$val){
					//manipulation allow data
					if(array_key_exists('ulunas',$val)){
						if ($val['ulunas']==1){
						$dexp[$key]['ulunas']='Fully Paid';
						}else{
						$dexp[$key]['ulunas']='Not Yet';
						}
					}
				}
		$data['printlistlogin'] = $this->table->generate($dexp);
		$this->session->set_flashdata('v',"Print success");
		$this->index();
		$this->session->set_flashdata('v',null);
		
		//create title
		$period = $this->Msetting->getset('period');
		$data['title']="Payment Data ".$period." Period<br/><small>".$title."</small>";
		$this->load->view('dashboard/org/pay/printpay', $data);
		
	}
	
	public function savesetting(){
		if(null!= $this->input->post('fprice')){
			$price = $this->input->post('fprice');
		$dtset=array(
				'price'=>$price
				);
		$this->Msetting->savesetting($dtset);
		$this->checklunas();
		$this->session->set_flashdata('v',"Update Setting Registration Price and Full Paid Status Success.");
		} else{
		$this->session->set_flashdata('x',"Update Setting Registration Price Failed.");
		}
		redirect(base_url('Organizer/Payment'));
	}
	
	public function returncolomn($header) {
	$find=['iddes','desdateup','desfile','desname','cerdefault','desnote','a.uname as uploader','b.uname as pic'];
	$replace = ['ID Design','Design Uploaded','Design File','Design Name','Default','Design Note','Uploader','PIC'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
