<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Org_Controller {

	function __construct()
    {
        parent::__construct();
 
        $this->load->database();
 
		$this->load->library(array('table','pagination','form_validation','Converttime'));
		$this->load->helper(array('form','url'));
		
		$this->load->model(array('Mpds','Msetting','Mtmp','Mnotif'));
    }

	public function index(){
		//======================================================================
		//============================== Save Handler ==========================
		//======================================================================
		if ($this->input->post('finputs')!=''){
			$arrinputs = explode(',', $this->input->post('finputs'));
			$s = 0; $x=0;$go = '';

			//========== upload logo handler =========
				if ($this->input->post('fflash')=='weblist1'){
					$tmplogo = $this->Msetting->getset('weblogo');
					$config['upload_path'] = FCPATH.'upload/system/';
					$config['allowed_types'] = 'jpeg|jpg|png';
					$config['max_size']     = '500';
					//$config['max_width'] = '500';
					//$config['max_height'] = '500';
					$config['overwrite'] = true;

					$this->load->library('upload', $config);

					if ( (! $this->upload->do_upload('weblogo')))
		                {
		                	$go = $this->upload->display_errors();
		                	$x=1;
		                } else {
		                	$go = 'Upload New Logo Success';
		                	$newfile = str_replace(' ','_',$_FILES['weblogo']['name']);
		                	$this->Msetting->updatesetting('weblogo',$newfile);
		                	if ($tmplogo <> $newfile) { unlink(FCPATH.'upload/system/'.$tmplogo);}
		                	$s=1;
		                }
				} else if($this->input->post('fflash')=='fontcerti'){
					$tmpfont = $this->Msetting->getset('fontcerti');
					$config['upload_path'] = FCPATH.'assets/fonts/';
					$config['allowed_types'] = '*';
					$config['max_size']     = '1000';
					//$config['max_width'] = '500';
					//$config['max_height'] = '500';
					$config['overwrite'] = true;
					$this->load->library('upload', $config);
					$arrfontname =explode('.', $_FILES['fontcerti']['name']);
					$extfontname = end($arrfontname);
					
					if ($extfontname == 'ttf'){
						if ( (! $this->upload->do_upload('fontcerti')))
			                {
			                	$go = $this->upload->display_errors();
			                	$x=1;
			                } else {
			                	$go = 'Upload New Design Font Success';
			                	$newfile = str_replace(' ','_',$_FILES['fontcerti']['name']);
			                	$this->Msetting->updatesetting('fontcerti',$newfile);
			                	if ($tmpfont <> $newfile) { unlink(FCPATH.'assets/fonts/'.$tmpfont);}
			                	$s=1;
			                }
		            } else {
		            	$go = "Incorrect File Type, Please Use .ttf File.";
		            	$x=1;
		            }
				} else if($this->input->post('fflash')=='txtcerti'){
					$arrtxtresult = array(
						'pretextcerti'=>str_replace("\n", "//", $this->input->post('pretextcerti')),
						'leveltextcerti'=>$this->input->post('leveltextcerti'),
						'titletextcerti'=>implode($this->input->post('titletextcerti'),'--'),
						'namesigntextcerti'=>implode($this->input->post('namesigntextcerti'),'--'),
						'nosigntextcerti'=>implode($this->input->post('nosigntextcerti'),'--')
						);
					foreach ($arrinputs as $k => $v) {
						//============== save text design handler =============
						if (($this->input->post($v)!='') and ($go =='')){
						$hsl = $this->Msetting->updatesetting($v,$arrtxtresult[$v]);
						($hsl) ? $s++ : $x++;
						}
					}

				} else if($this->input->post('fflash')=='design'){
					foreach ($arrinputs as $k => $v) {
						//============== save setting design handler =============
						if (($this->input->post($v)!='') and ($go =='')){
						$txtinput = implode($this->input->post($v), ',');
						$hsl = $this->Msetting->updatesetting($v,$txtinput);
						($hsl) ? $s++ : $x++;
						}
					}
				} else if($this->input->post('fflash')=='mail'){
					foreach ($arrinputs as $k => $v) {
						//============== save setting design handler =============
						if (($this->input->post($v)!='') and ($go =='')){
						$htmlinput = htmlspecialchars($this->input->post($v,false));
						$hsl = $this->Msetting->updatesetting($v,$htmlinput);
						($hsl) ? $s++ : $x++;
						}
					}
				} else {
					foreach ($arrinputs as $k => $v) {
						//============== save data handler =============
						if (($this->input->post($v)!='') and ($go =='')){
						$hsl = $this->Msetting->updatesetting($v,$this->input->post($v));
						($hsl) ? $s++ : $x++;
						}
					}
				}
			($go=='') ? $txt = 'Update '.($s+$x).' Setting, '.$s.' Success and '.$x.' Failed.' : $txt = $go;
			($s>$x) ? $this->session->set_flashdata('v'.$this->input->post('fflash'),$txt) : $this->session->set_flashdata('x'.$this->input->post('fflash'),$txt);
			header("Location: ".$_SERVER['REQUEST_URI']);
		}
	




		
		//======================================================================
		//============================== FORM SETTING ==========================
		//======================================================================

		//===================== table handler =============
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		

		//========== setting website title =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnweb=['webtitle','webtagline','webtag','webdescription'];
		$labelweb = ['Site Title','Site Tagline','Site Tag','Site Description'];
		foreach ($columnweb as $k => $v) {		
		$temp[$k] =array($labelweb[$k], 
					form_input(array(
						'id'=>'f'.$columnweb[$k],
						'name' => $columnweb[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>'text',
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['websetting']=array('table' => $this->table->generate($temp),
									'title' => 'Setting Website',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnweb, ',')).form_hidden('fflash','weblist')
									); 
		
		//========== setting website logo =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnlg=['weblogo'];
		$labellg = ['Current Logo'];
		$lg = $this->Msetting->getset($columnlg[0]);	
		$templg[] =array($labellg[0],'<img src="'.base_url('upload/system/'.$lg).'" class="img-thumbnail" width=100><br/>'
					);
		
		$templg[] =array('Upload Logo', 
					form_upload(array(
						'id'=>'f'.$columnlg[0],
						'name' => $columnlg[0],
						'class'=>'btn btn-default',
						'required'=>'required'))
					
					);
		
		$data['logosetting']=array('table' => $this->table->generate($templg),
									'title' => 'Setting Logo',
									'fbtn' => form_submit(array('value'=>'Update Logo',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnlg, ',')).form_hidden('fflash','weblist1')
									); 
		

		//========================= setting system =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$column=['period','price','quota','cp','email','bbm'];
		$label = ['Period','Price','Quota','Contact Person','Email','Social Media'];
		$arrinput = ['text','number','number','text','email','text'];
		foreach ($column as $k => $v) {		
		$temp[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$column[$k],
						'name' => $column[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>$arrinput[$k],
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp),
									'title' => 'Setting System',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($column, ',')).form_hidden('fflash','system0')
									); 
		

		//=============== setting phase ============
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnphase=['registphase','paymentphase','schedulephase','certiphase'];
		$label = ['Registration<br/>Phase','Payment<br/>Phase','Schedule Confirmatin<br/>Phase','Certificate<br/>Phase'];
		foreach ($columnphase as $k => $v) {		
		$temp2[$k] =array($label[$k], 
					form_input(array(
						'id'=>'f'.$columnphase[$k],
						'name' => $columnphase[$k],
						'class'=>'form-control',
						'required'=>'required',
						'type'=>'text',
						'value'=>$this->Msetting->getset($v)))
					
					);
		}
		
		$data['settinglist'][]=array('table' => $this->table->generate($temp2),
									'title' => 'Setting Phase',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnphase, ',')).form_hidden('fflash','system1')
									); 
		

		//========== setting payment  =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnpay=['no_atm','an_atm','jns_bank'];
		$labelpay = ['Bank Account Number','Account Owner','Bank Name'];
		foreach ($columnpay as $k => $v) {		
			$temppay[$k] =array($labelpay[$k], 
						form_input(array(
							'id'=>'f'.$columnpay[$k],
							'name' => $columnpay[$k],
							'class'=>'form-control',
							'value'=> $this->Msetting->getset($v),
							'required'=>'required')));
		}

		$data['payment']=array('table' => $this->table->generate($temppay),
									'title' => 'Payment System',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnpay, ',')).form_hidden('fflash','pay')
									); 


		//========== setting registration form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnregist=['formregistterm','formregistsuccess','mailregistsuccess'];
		$label = ['Term & Condition','Template Success Registration ','Email Content'];
		$opttmp = $this->Mtmp->getopttmp();
		$temp3 =array(
					array($label[0],
					form_input(array('id'=>'f'.$columnregist[0],
						'name'=>$columnregist[0],
						'required'=>'required',
						'value'=>$this->Msetting->getset('formregistterm'),
						'class'=>'form-control'))),

					array($label[1],
					form_dropdown(array('id'=>'fregistsuccess',
						'name'=>'formregistsuccess',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker'),$opttmp,$this->Msetting->getset('formregistsuccess'))),

					array($label[2],
					form_dropdown(array('id'=>'fmailregistsuccess',
						'name'=>'mailregistsuccess',
						'required'=>'required',
						'data-live-search'=>'true',
						'class'=>'form-control selectpicker'),$opttmp,$this->Msetting->getset('mailregistsuccess'))),
				);
		$data['registform']=array('table' => $this->table->generate($temp3),
									'title' => 'Registration Form',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnregist, ',')).form_hidden('fflash','regist')
									); 
		


		//========== setting notification member form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotifmem=['notifbcmail','notifbcsms','notifcertiavailable','notifcertitaken','notifemailvalidated','notifpayconfirmfailed','notifpayconfirmsuccess','notifpaymentphase','notifpayproofuploaded','notifpayment','notifpdscomplete','notifpdsincomplete','notifregistsuccess','notifschedulechosen','notifschedulephase','notiftestactive','notiftestsubmitted','notiftestresult','notifwelcomemem'];
		$labelmem = ['New Broadcast Mail','New Broadcast SMS','Certificate Available','Certificate Phase','Certificate Has Taken','Payment Rejected','Payment Success','Payment Phase','Payment Uploaded','Payment Made','PDS Completed','PDS Incomplete','Registration Success','Schedule Chosen','Schedule Phase','Test Is Active','Test Is Submitted','Test Result Is Available','Welcome Message (Member)'];
		$optnotif = $this->Mnotif->getoptnotif();
		foreach ($columnnotifmem as $k => $v) {		
			$temp4[$k] =array($labelmem[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotifmem[$k],
							'name' => $columnnotifmem[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notifmemform']=array('table' => $this->table->generate($temp4),
									'title' => 'Notification Member',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnnotifmem, ',')).form_hidden('fflash','notif1')
									); 
		
		//========== setting notification org form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotiforg=['notifbcby','notifnewpayproof','notifnewsignup','notifnewtestresult','notifresetpassword','notiftestactivatedby','notifwelcomeorg'];
		$labelorg = ['Broadcast Message','New Payment Confirmation Requested','New Member Registration','New Test Result Submitted','Reset Password','Test Activated','Welcome Message (Org)'];
		foreach ($columnnotiforg as $k => $v) {		
			$temp5[$k] =array($labelorg[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotiforg[$k],
							'name' => $columnnotiforg[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notiforgform']=array('table' => $this->table->generate($temp5),
									'title' => 'Notification Organizer',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnnotiforg, ',')).form_hidden('fflash','notif2')
									); 
		

		//========== setting notification org Admin =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnnotifadm=['notifnewloginorg','notifwelcomeadm'];
		$labeladm = ['New Login Organizer','Welcome Message Admin'];
		foreach ($columnnotifadm as $k => $v) {		
			$temp6[$k] =array($labeladm[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnnotifadm[$k],
							'name' => $columnnotifadm[$k],
							'class'=>'form-control selectpicker changenotifclass',
							'data-live-search'=>'true',
							'required'=>'required'),$optnotif,$this->Msetting->getset($v)));
		}

		$data['notifadmform']=array('table' => $this->table->generate($temp6),
									'title' => 'Notification Administrator',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnnotifadm, ',')).form_hidden('fflash','notif3')
									); 
		

		//========== setting Mail header footer =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnmail=['mailheader','mailfooter'];
		$labelmail = ['Header Email','Footer Email'];
		foreach ($columnmail as $k => $v) {
			$txtmail = htmlspecialchars_decode($this->Msetting->getset($v));		
			$tempmail[$k] =array($labelmail[$k], 
						form_hidden($columnmail[$k],
							$txtmail).'<div id="txt'.$columnmail[$k].'" class="txtmail">'.$txtmail.'</div>');
		}

		$data['mail']=array('table' => $this->table->generate($tempmail),
									'title' => 'Mail Template',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdatetxtmail')),
									'finputs' => form_hidden('finputs',implode($columnmail, ',')).form_hidden('fflash','mail')
									); 


		//========== setting Mail token =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnmtoken=['sendermail','sendmailcode','sendmailrefreshtoken','sendmailtoken'];
		$labelmtoken = ['Mail Sender','Mail Code','Mail Refresh Token','Mail Token'];
		foreach ($columnmtoken as $k => $v) {		
			$tempmtoken[$k] =array($labelmtoken[$k], 
						form_input(array(
							'id'=>'f'.$columnmtoken[$k],
							'name' => $columnmtoken[$k],
							'class'=>'form-control',
							'value'=>$this->Msetting->getset($v),
							'required'=>'required')));
		}

		$data['mailtoken']=array('table' => $this->table->generate($tempmtoken),
									'title' => 'Gmail API',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnmtoken, ',')).form_hidden('fflash','mtoken')
									); 


		//========== setting certificate =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columncerti=['certiformat','sizedesign'];
		$labelcerti = ['Numbering Format','Maximum Design Size (KB)'];
		foreach ($columncerti as $k => $v) {		
			$tempcerti[$k] =array($labelcerti[$k], 
						form_input(array(
							'id'=>'f'.$columncerti[$k],
							'name' => $columncerti[$k],
							'class'=>'form-control',
							'value'=>$this->Msetting->getset($v),
							'required'=>'required')));
		}

		$data['certi']=array('table' => $this->table->generate($tempcerti),
									'title' => 'Certificate Setting',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columncerti, ',')).form_hidden('fflash','certi')
									); 
		

		//========== setting design =========
		$this->table->set_template($tmpl);
		$columndesign=['sizecerti','margincerti','colorcerti','columncerti','centercerti'];
		$coldesign = ['Setting','Font Size','Margin','Font Color','Total Column','Justify Alignment'];
		$labeldesign = array('Full Name','Certificate Number','Preface','Assessment Score','Level','Title','Signature Name','Signature No');
		$this->table->set_heading($coldesign);
				$optcolor = array(
							''=>'Please Select',
							'black'=>'Black',
							'green'=>'Green',
							'red'=>'Red',
							'blue'=>'Blue'
							);
				$opttotcolumn = array(
							''=>'Please Select',
							'1'=>'1',
							'2'=>'2'
							);
				$optjustalign = array(
							''=>'Please Select',
							'0' => 'No',
							'1' => 'Yes'
							);
				$arrdessize = explode(',', $this->Msetting->getset('sizecerti'));
				$arrdesmargin = explode(',', $this->Msetting->getset('margincerti'));
				$arrdescolor = explode(',', $this->Msetting->getset('colorcerti'));
				$arrdescolumn = explode(',', $this->Msetting->getset('columncerti'));
				$arrdescenter = explode(',', $this->Msetting->getset('centercerti'));
		foreach ($labeldesign as $k => $v) {		
			$tempdesign[$k] =array($labeldesign[$k], 
						form_input(array(
							'id'=>'fsetsize'.$k,
							'name' => $columndesign[0].'[]',
							'class'=>'form-control',
							'value'=>$arrdessize[$k],
							'required'=>'required')),
						form_input(array(
							'id'=>'fsetmargin'.$k,
							'name' => $columndesign[1].'[]',
							'class'=>'form-control',
							'value'=>$arrdesmargin[$k],
							'required'=>'required')),
						form_dropdown($columndesign[2].'[]',
							$optcolor,$arrdescolor[$k],
							'required="required" class="form-control"'),
						form_dropdown($columndesign[3].'[]',
							$opttotcolumn,$arrdescolumn[$k],
							'required="required" class="form-control"'),
						form_dropdown($columndesign[4].'[]',
							$optjustalign,$arrdescenter[$k],
							'required="required" class="form-control"')
					);
		}

		$data['design']=array('table' => $this->table->generate($tempdesign),
									'title' => 'Design Setting',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columndesign, ',')).form_hidden('fflash','design')
									); 
		

		//========== setting font design =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnfontcerti=['fontcerti'];
		$labelfontcerti = ['Current Font'];
		$fontcerti = $this->Msetting->getset($columnfontcerti[0]);	
		$tempfontcerti[] =array($labelfontcerti[0],'<b><i>'.$fontcerti.'</i></b>'
					);
		
		$tempfontcerti[] =array('Upload New Font', 
					form_upload(array(
						'id'=>'f'.$columnfontcerti[0],
						'name' => $columnfontcerti[0],
						'class'=>'btn btn-default',
						'required'=>'required'))
					
					);
		
		$data['fontcerti']=array('table' => $this->table->generate($tempfontcerti),
									'title' => 'Certificate Font',
									'fbtn' => form_submit(array('value'=>'Update Font',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnfontcerti, ',')).form_hidden('fflash','fontcerti')
									); 
		

		//========== setting predefined text certificate =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columntxtcerti=['pretextcerti','leveltextcerti','titletextcerti','namesigntextcerti','nosigntextcerti'];
		$labeltxtcerti = ['Preface Text','Level','Name Title','Fullname of Signature','ID Number of Signature'];
			$temptxtcerti[] =array($labeltxtcerti[0], 
						form_textarea(array(
							'id'=>'f'.$columntxtcerti[0],
							'name' => $columntxtcerti[0],
							'class'=>'form-control',
							'rows'=>'3',
							'value'=>str_replace('//',"\n",$this->Msetting->getset($columntxtcerti[0])),
							'required'=>'required')));
			$temptxtcerti[] =array($labeltxtcerti[1], 
						form_textarea(array(
							'id'=>'f'.$columntxtcerti[1],
							'name' => $columntxtcerti[1],
							'class'=>'form-control',
							'rows'=>'3',
							'value'=>$this->Msetting->getset($columntxtcerti[1]),
							'required'=>'required')));
				$arrtxttitle = explode('--',$this->Msetting->getset($columntxtcerti[2]));
				$arrtxtsignname = explode('--',$this->Msetting->getset($columntxtcerti[3]));
				$arrtxtnosign = explode('--',$this->Msetting->getset($columntxtcerti[4]));
				$arrtxtleftright = array(' (Left)',' (Right)');
				$arrtxtvalue = array($arrtxttitle,$arrtxtsignname,$arrtxtnosign);
				$itxt = 2; 
			foreach ($arrtxtvalue as $k => $v) {
				foreach ($arrtxtleftright as $l=>$r) {		
					$temptxtcerti[] =array($labeltxtcerti[$itxt].$r, 
								form_input(array(
									'id'=>'f'.$columntxtcerti[$itxt].$r,
									'name' => $columntxtcerti[$itxt].'[]',
									'class'=>'form-control',
									'value'=>$arrtxtvalue[$k][$l],
									'required'=>'required')));
					}
				$itxt ++;	
			}
		$data['txtcerti']=array('table' => $this->table->generate($temptxtcerti),
									'title' => 'Predefined Text Certificate',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columntxtcerti, ',')).form_hidden('fflash','txtcerti')
									); 
		







		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','summernote/summernote');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote');  
		// =============== view handler ============
		$data['title']="Setting System";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/setting/settinglist', $data, TRUE);
		$this->load->view ('template/main', $data);
	}
	
	 
	public function previewTemplate($idtmp)
	{
		if ($idtmp!=''){
			$content = $this->Mtmp->detailtmp(array('tmpcontent'),$idtmp)[0];
			echo htmlspecialchars_decode($content['tmpcontent']);
		} else{
			echo 'error';
		}
	}
	
	public function previewNotification($idnotif)
	{
		if ($idnotif!=''){
			$content = $this->Mnotif->detailnotif(array('ncontent'),$idnotif)[0];
			echo ($content['ncontent']);
		} else{
			echo 'error';
		}
	}
	
	
	public function updatepds(){
		if ($this->input->post('fuser')!=null){
		$us = $this->input->post('fuser');
		$fdata = array (
					'uname' => $this->input->post('ffullname'),					
					'uupdate' => date("Y-m-d H:i:s"),
					'idjk' => $this->input->post('fjk'),
					'unim' => $this->input->post('fnim'),
					'idfac' => $this->input->post('ffaculty'),
					'ubplace' => $this->input->post('fbplace'),
					'ubdate' => $this->input->post('fbdate'),
					'uemail' => $this->input->post('femail'),
					'uhp' => $this->input->post('fhp'),
					'ubbm' => $this->input->post('fsocmed'),
					'uaddrnow' => $this->input->post('faddrnow'),
					'uaddhome' => $this->input->post('faddrhome'),
					'ustatus' => $this->input->post('fstats')
					);
		$r = $this->Mpds->updatepds($fdata,$us);
		}
		if ($r){
		$this->session->set_flashdata('v','Update Registration Data Success');
		} else {		
		$this->session->set_flashdata('x','Update Registration Data Failed');
		}
		redirect(base_url('Organizer/PDS'));
	}
	
	
	
	public function getdetailuser(){
		$id = $this->input->post('user');
		echo json_encode($this->Mpds->detailuser($id));
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
		redirect(base_url('Organizer/PDS'));
	}
	
	public function returncolomn($header) {
	$find=['period','price','quota','cp','email','bbm','registphase','paymentphase','schedulephase','certiphase'];
	$replace = ['Period','Price','Quota','Contact Person','Email','Social Media','Registration<br/>Phase','Payment<br/>Phase','Schedule Confirmatin<br/>Phase','Certificate<br/>Phase'];
		foreach ($header as $key => $value){
		$header[$key]  = str_replace($find, $replace, $value);
		}
	return $header;
	}
	
}
