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
		if ($this->input->get('code')!=''){
				$this->session->set_flashdata('vmtoken','Update Gmail Setting Success');
				header("Location: ".base_url("Organizer/Setting#emaillist"));
			}

		if ($this->input->post('finputs')!=''){
			$arrinputs = explode(',', $this->input->post('finputs'));
			$s = 0; $x=0;$go = '';

			//========== payment handler =========
				if ($this->input->post('fflash')=='pay'){
					$arrbanks[] = implode(',', $this->input->post('no_atm'));
					$arrbanks[] = implode(',', $this->input->post('an_atm'));
					$arrbanks[] = implode(',', $this->input->post('jns_bank'));

					foreach ($arrinputs as $k => $v) {
						//============== save data handler =============
						if ($arrbanks[$k]!=''){
						$hsl = $this->Msetting->updatesetting($v,$arrbanks[$k]);
						($hsl) ? $s++ : $x++;
						}
					}
					$go = ($s>$x) ? "Update Payment Channel Success" : "Update Payment Channel Failed";
				
				}else if ($this->input->post('fflash')=='weblist1'){
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
						//============== save setting mail handler =============
						if (($this->input->post($v)!='') and ($go =='')){
						$htmlinput = ($v<>'cssmail') ? htmlspecialchars($this->input->post($v,false)) : $this->input->post($v);
						$hsl = $this->Msetting->updatesetting($v,$htmlinput);
						($hsl) ? $s++ : $x++;
						}
					}
				} else if($this->input->post('fflash')=='mtoken'){
					if (($this->input->post('fmailconfigured')=='') and ($go =='')){
						//============== save setting mail token handler =============
						foreach ($arrinputs as $k => $v) {
							$hsl = $this->Msetting->updatesetting($v,'');
							($hsl) ? $x++ : $s++;
						}
						$go = "Remove Gmail Setting Success";
					} else {
						$go = "New Gmail Setting Saved";
						$s++;
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
		$headerphase = $header;
		$headerphase['heading_cell_start'] = '<th width=30%>'; 
		$this->table->set_template($tmpl);
		$this->table->set_heading($headerphase);
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
		$columnpay=['no_atm','an_atm','jns_bank'];
		$labelpay = ['Bank Account Number','Account Owner','Bank Name'];
		$tblpay = '';
		foreach ($columnpay as $k => $v) {
			$arrpay[$v] = explode(',', $this->Msetting->getset($v));
		}
		foreach ($arrpay['no_atm'] as $i => $val) {	
			foreach ($columnpay as $k => $v) {
				$temppay[$i][$k] =array($labelpay[$k], 
						form_input(array(
							'id'=>'f'.$columnpay[$k],
							'name' => $columnpay[$k].'[]',
							'class'=>'form-control',
							'value'=> $arrpay[$v][$i],
							'required'=>'required')));
			}
				$btnpay = ($i==0) ? '<p class="text-right"><button type="button" class=" btn btn-primary btn-sm" id="btnaddpayform"><span class="fa fa-plus"></span> Add More Bank</button></p>' : '<p class="text-right"><button type="button" class=" btn btn-danger btn-sm btnremovepayform"><span class="fa fa-minus"></span> Delete Bank</button></p>';
				$this->table->set_template($tmpl);
				$this->table->set_heading($header);
				$tblpay .= '<div class="panel panel-default">
								<div class="panel-body">'.$btnpay.
									$this->table->generate($temppay[$i]).
							'</div></div>';		
		}

		$data['payment']=array('table' => $tblpay,
									'title' => 'Payment System',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnpay, ',')).form_hidden('fflash','pay')
									); 


		//========== setting registration form =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);
		$columnregist=['formregistterm','formregistsuccess'];
		$label = ['Term & Condition','Registration Success'];
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
						'class'=>'form-control selectpicker'),$opttmp,$this->Msetting->getset('formregistsuccess')))
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

		$columnnotiforg=['notifbcsmsby','notifbcmailby','notifnewpayproof','notifnewsignup','notifnewtestresult','notifresetpassword','notiftestactivatedby','notifwelcomeorg'];
		$labelorg = ['Broadcast SMS by','Broadcast Mail by','New Payment Confirmation Requested','New Member Registration','New Test Result Submitted','Reset Password','Test Activated','Welcome Message (Org)'];
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
		

		//================= setting page =================
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnpg=['tmpdashhome','tmpdashmem','tmpdashorg','tmpdashadm','tmppayproc','tmpaboutsef','tmpaboutrc'];
		$labelpg = ['Homepage','Dashboard Member','Dashboard Organizer','Dashboard Admin','Payment Procedure (Member)','About SEF','About Program (RC)'];
		foreach ($columnpg as $k => $v) {		
			$temppg[$k] =array($labelpg[$k], 
						form_dropdown(array(
							'id'=>'f'.$columnpg[$k],
							'name' => $columnpg[$k],
							'class'=>'form-control selectpicker changepage',
							'data-live-search'=>'true',
							'required'=>'required'),$opttmp,$this->Msetting->getset($v)));
		}

		$data['pagesetting']=array('table' => $this->table->generate($temppg),
									'title' => 'Page Setting',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnpg, ',')).form_hidden('fflash','pageset')
									); 


		//================= setting mail page =================
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columntmpmail=['mailregistsuccess','mailresetaccount','mailverify'];
		$labeltmpmail = ['Registration Success','Reset Account','Mail Confirmation'];
		foreach ($columntmpmail as $k => $v) {		
			$temptmpmail[$k] =array($labeltmpmail[$k], 
						form_dropdown(array(
							'id'=>'f'.$columntmpmail[$k],
							'name' => $columntmpmail[$k],
							'class'=>'form-control selectpicker changepage',
							'data-live-search'=>'true',
							'required'=>'required'),$opttmp,$this->Msetting->getset($v)));
		}

		$data['template']=array('table' => $this->table->generate($temptmpmail),
									'title' => 'Mail Template',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columntmpmail, ',')).form_hidden('fflash','tmpmail')
									); 


		//================= setting template sms =================
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$opttmpsms = $this->Mtmp->getopttmpsms();
		$columntmpsms=['smsregistsuccess','smspayvalid','smsreminderschedule','smscertiready'];
		$labeltmpsms = ['Registration Success','Payment Approved','Test Reminder','Certificate Ready'];
		foreach ($columntmpsms as $k => $v) {		
			$temptmpsms[$k] =array($labeltmpsms[$k], 
						form_dropdown(array(
							'id'=>'f'.$columntmpsms[$k],
							'name' => $columntmpsms[$k],
							'class'=>'form-control selectpicker changepage',
							'data-live-search'=>'true',
							'required'=>'required'),$opttmpsms,$this->Msetting->getset($v)));
		}

		$data['tmpsms']=array('table' => $this->table->generate($temptmpsms),
									'title' => 'SMS Template',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columntmpsms, ',')).form_hidden('fflash','tmpsms')
									); 





		//========== setting Mail Template =========
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnmail=['cssmail','mailtemplate','mailfooter'];
		$labelmail = ['CSS Email','Email Template','Footer Email'];
		foreach ($columnmail as $k => $v) {
			$txtmail = htmlspecialchars_decode($this->Msetting->getset($v));		
			$tempmail[$k] =array($labelmail[$k], 
						form_hidden($columnmail[$k],
							$txtmail).'<div id="txt'.$columnmail[$k].'" class="txtmail">'.$txtmail.'</div>');
			if($v=='cssmail'){
				$tempmail[$k] =array($labelmail[$k], 
						form_textarea(array(
							'name'=>$columnmail[$k],
							'value' =>htmlspecialchars_decode($this->Msetting->getset($v)),
							'class'=>'form-control',
							'style' => 'width:100%;'
						)));
			}
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
		$columnmtoken=['sendermail'];
		$arrtoken = ['sendermail','sendmailcode','sendmailrefreshtoken','sendmailtoken'];
		$labelmtoken = ['Mail Sender'];
		foreach ($columnmtoken as $k => $v) {
			$mailnow = $this->Msetting->getset($v);
			if ($mailnow!=''){
				$valtoken = true;
				$btntoken = form_button(array(
											'class'=>'btn btn-danger',
											'id'=>'btnremoveemail'),'Remove Curent Email');
			} else {
				$valtoken = false;
				$this->load->library('Gmail');
				$btntoken = '<a href="'.$this->gmail->authorize(base_url('Organizer/Setting')).'" class="btn btn-primary" id="btnnewemail">Authorize New Email</a>';
			}		
			$tempmtoken[$k] =array($labelmtoken[$k], 
						form_checkbox(array(
							'name'=>'fmailconfigured',
							'data-toggle'=>'toggle',
							'disabled'=>'disabled',
							'data-on'=>'Configured as '.$mailnow,
							'data-off'=>'Not Configured Yet',
							'data-width'=>300,
							'data-height'=>35,
							'data-onstyle'=>'primary',
							'data-offstyle'=>'danger',
							'id'=>'senderstat',
							'checked'=>$valtoken,
							'value'=>'1'))
							);
		}

		$data['mailtoken']=array('table' => $this->table->generate($tempmtoken),
									'title' => 'Gmail API',
									'fbtn' => $btntoken,
									'finputs' => form_hidden('finputs',implode($arrtoken, ',')).form_hidden('fflash','mtoken')
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


		$data['period'] = $this->Msetting->getset('period');
		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','print/printThis','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','summernote/summernote','toggle/bootstrap2-toggle.min');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker','summernote/summernote','toggle/bootstrap2-toggle.min');  
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
	
	
	public function account(){
		//============== save handler ========================
		//====================================================
		if($this->input->post('finputs')<>''){
			$arrinputs = explode(',',$this->input->post('finputs'));
			$go =  "";$x=0;$s=0;
			if($this->input->post('fflash')=='pass'){
				$oldpass = md5($this->input->post("upassold"));
				$newpass = $this->input->post("upassnew");
				$confirmpass = $this->input->post("upassnew2");
				$dtpass = $this->Mlogin->detaillogin(array("upass"),$this->session->userdata('user'))[0]['upass']; 
				if ($oldpass == $dtpass){
					if ($confirmpass == $newpass){
						$arrdt = array('upass'=>md5($newpass));
						$hsl = $this->Mlogin->updateacc($arrdt,$this->session->userdata('user'));
						if ($hsl){
							$go= "Update Password Setting Success";
							$s++;
						} else {
							$go= "Update Password Setting Failed";
							$x++;
						}
					} else {
						$go = "Your confirmation password is mismatch with new password";
						$x++;
					} 
				} else {
					$go = "Your old password is mismatch";
					$x++; 
				}
			} else if($this->input->post('fflash')=='pho'){
					$arrfilename =explode('.', $_FILES['ufoto']['name']);
					$extfilename = end($arrfilename);
					$new_name = md5($this->session->userdata('user').$_FILES['ufoto']['name']).'.'.$extfilename;
					$tmpfoto = $this->Mlogin->detaillogin(array('ufoto'),$this->session->userdata('user'));
					$config['upload_path'] = FCPATH.'upload/foto/';
					$config['allowed_types'] = 'png|jpg|jpeg';
					$config['max_size']     = '300';
					$config['overwrite'] = true;
					$config['file_name'] = $new_name;
					//$config['max_width'] = '500';
					//$config['max_height'] = '500';
					$this->load->library('upload', $config);

					if ($this->upload->do_upload('ufoto')){
			            $go = 'Upload New Profile Photo Success';
			            $newfile = $new_name ;
			            $hslfoto = $this->Mlogin->updateacc(array('ufoto'=>$newfile),$this->session->userdata('user'));
			            ($hslfoto) ? $this->session->set_userdata(array('photo'=>$newfile)): null;
			            if (($tmpfoto <> $newfile)) { unlink(FCPATH.'upload/foto/'.$tmpfoto);}
			            $s=1;
			               
		            } else {
		            	$go = $this->upload->display_errors();
		            	$x=1;
		            }
		    } else {
			foreach ($arrinputs as $k => $v) {
						//============== save data handler =============
						if (($this->input->post($v)!='') and ($go =='')){
							$arrdt[$v] = $this->input->post($v);
						}
					}
					$hsl = $this->Mlogin->updateacc($arrdt,$this->session->userdata('user'));
					if ($hsl){
						$go= "Update Account Setting Success";
						$s++;
					} else {
						$go= "Update Account Setting Failed";
						$x++;
					}
		    }
			
			($go<>'') ? $txt = $go : $txt= "Update Success";
			($s>$x) ? $this->session->set_flashdata('v'.$this->input->post('fflash'),$txt) : $this->session->set_flashdata('x'.$this->input->post('fflash'),$txt);
			header("Location: ".$_SERVER['REQUEST_URI']);
		}




		//========== setting account =========
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnacc=['ufoto','uemail','uhp','ubbm'];
		$labelacc = ['Photo','Email','Phone Number','Social Media'];
		$arracc = $this->Mlogin->detaillogin($columnacc,$this->session->userdata('user'))[0];
		foreach ($columnacc as $k => $v) {

			$tempacc[$k] =array($labelacc[$k], 
						form_input(array(
							'id'=>'f'.$columnacc[$k],
							'name' => $columnacc[$k],
							'class'=>'form-control',
							'value'=>$arracc[$v],
							'required'=>'required')));
			if($columnacc[$k]=="uemail"){
				$tempacc[$k][1] .= '<p class="text-danger hidden">Email Is Taken</p><span id="emailnow" class="hidden">'.$arracc[$v].'</span>';
			} 
			else if($columnacc[$k]=="ufoto"){
				($arracc[$v]=='') ? $foto = "avatar.png" : $foto = $arracc[$v];
				$tempacc[$k][1] = '<div class="thumbnail text-center">
							        <img src="'.base_url("upload/foto/".$foto).'" alt="" class="img-responsive imgava" alt="user photo">
							        <div class="caption">
							        	<button type="button" class="btn" data-toggle="modal" data-target="#fotoModal">
							            <i class="fa fa-cloud-upload fa-2x"></i> <br/>
							            <small><b>Upload</b></small>
							            </button>
							        </div>
							    </div>';
			}  
					
		}

		$data['acc']=array('table' => $this->table->generate($tempacc),
									'title' => 'Account Setting',
									'fbtn' => form_submit(array('value'=>'Update Setting',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateacc')),
									'finputs' => form_hidden('finputs',implode($columnacc, ',')).form_hidden('fflash','acc')
									); 
		

		//========== setting photo =========
		
		$tmpl = array ( 'table_open'  => '<table class="table table-hover text-center">',
						'row_open' );
		$this->table->set_template($tmpl);
		
		$columnpho=['ufoto'];
		$arrpho = $this->Mlogin->detaillogin($columnpho,$this->session->userdata('user'))[0];
		foreach ($columnpho as $k => $v) {
			($arrpho[$v]=='') ? $foto = "avatar.png" : $foto = $arrpho[$v];		
			$temppho[$k] =array( 
							'<div class="text-center">
							<div class="well">
								<div id="prevmyphoto" class="img-thumbnail" style="height:200px;width:200px;">
							     </div>
							</div>
							<hr/><p><b>Upload New Photo (Max 300kb)</b></p>'.form_upload(array(
							'id'=>'f'.$columnpho[$k],
							'name' => $columnpho[$k],
							'class'=>'btn btn-default',
							'style'=> 'margin:0px auto',
							'required'=>'required')).'</div>');
		}
		$data['pho']=array('table' => $this->table->generate($temppho),
									'title' => 'Change Photo',
									'fbtn' => form_submit(array('value'=>'Update Photo',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs',implode($columnacc, ',')).form_hidden('fflash','pho')
									); 
		

		//========== setting password =========
		$header = array('Setting','Value');
		$tmpl = array ( 'table_open'  => '<table class="table table-hover">' );
		$this->table->set_template($tmpl);
		$this->table->set_heading($header);

		$columnpass=['upassold','upassnew','upassnew2'];
		$labelpass = ['Old Password',"New Password","Confirm New Password"];
		
		foreach ($columnpass as $k => $v) {		
			$temppass[$k] =array($labelpass[$k], 
						form_input(array(
							'id'=>'f'.$columnpass[$k],
							'name' => $columnpass[$k],
							'class'=>'form-control',
							'value'=>'',
							'type'=>'password',
							'required'=>'required')));
		}

		$data['pass']=array('table' => $this->table->generate($temppass),
									'title' => 'Password Setting',
									'fbtn' => form_submit(array('value'=>'Update Password',
											'class'=>'btn btn-primary',
											'id'=>'btnupdateset')),
									'finputs' => form_hidden('finputs','upass').form_hidden('fflash','pass')
									); 


		//=============== Template ============
		$data['jsFiles'] = array(
							'selectpicker/select.min','moment/moment.min','daterange/daterangepicker','inputmask/inputmask','inputmask/jquery.inputmask','inputmask/inputmask.date.extensions','uploadpreview/uploadPreview');
		$data['cssFiles'] = array(
							'selectpicker/select.min','daterange/daterangepicker');  
		// =============== view handler ============
		$data['title']="Setting Account";
		$data['topbar'] = $this->load->view('dashboard/topbar', NULL, TRUE);
		$data['sidebar'] = $this->load->view('dashboard/org/sidebar', NULL, TRUE);
		$data['content'] = $this->load->view('dashboard/org/setting/account', $data, TRUE);
		$this->load->view ('template/main', $data);
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
