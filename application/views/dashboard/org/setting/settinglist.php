<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-wrench fa-lg"></i> Setting<small>System</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
		<div class="box-header">
			<?php if ($this->session->flashdata('v')!=null){ ?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('v');?>
			</div>
			<?php } else if ($this->session->flashdata('x')!=null){ ?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('x');?>
			</div>		
			<?php } ?>
		
				
	</div>
	<div class="box-body">
		<div class="nav-tabs-custom">
		  	<!-- Tabs within a box -->
            <ul class="nav nav-tabs nav-primary">
                <li class="active"><a data-toggle="tab" href="#weblist"><span class="fa fa-home"></span>  Website Setting</a></li>
                <li><a data-toggle="tab" href="#systemlist"><span class="fa fa-wrench"></span> System Parameter</a></li>
                <li><a data-toggle="tab" href="#registform"><span class="fa fa-file-text"></span>  Registration Form</a></li>
                <li><a data-toggle="tab" href="#paymentform"><span class="fa fa-money"></span>  Payment Setting</a></li>
                <li><a data-toggle="tab" href="#notiflist"><span class="fa fa-bell"></span>  Notification Setting</a></li>
                <li><a data-toggle="tab" href="#pagelist"><span class="fa fa-pencil-square"></span>  Page Setting</a></li>
                <li><a data-toggle="tab" href="#emaillist"><span class="fa fa-envelope"></span>  Email Setting</a></li>
                <li><a data-toggle="tab" href="#certilist"><span class="fa fa-certificate"></span>  Certificate Setting</a></li>
            </ul>
            <div class="tab-content">
            	<div class="tab-pane table-responsive active" id="weblist">
					<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#weblist'),array('name'=>'fwebsetting','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-home"></span> <b><?=$websetting['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vweblist')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vweblist');?>
									</div>
									<?php } else if ($this->session->flashdata('xweblist')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xweblist');?>
									</div>		
									<?php } ?>
								 	<?=$websetting['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$websetting['finputs'];?>
									<?=$websetting['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
					</div>
					<div class="col-md-6">
						<?php echo form_open_multipart(base_url('Organizer/setting#weblist'),array('name'=>'flogoform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-home"></span> <b><?=$logosetting['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vweblist1')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vweblist1');?>
									</div>
									<?php } else if ($this->session->flashdata('xweblist1')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xweblist1');?>
									</div>		
									<?php } ?>
								 	<?=$logosetting['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$logosetting['finputs'];?>
									<?=$logosetting['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
					</div>
				</div>
            	<div class="tab-pane table-responsive" id="systemlist">
                    
						<?php foreach ($settinglist as $k => $v) { ?>
						<?php echo form_open(base_url('Organizer/setting#systemlist'),array('name'=>'fsetting'.$k,'class'=>'form-horizontal','method'=>'POST'));?>
						<div class="col-md-6">
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-wrench"></span> <b><?=$v['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vsystem'.$k)!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vsystem'.$k);?>
									</div>
									<?php } else if ($this->session->flashdata('xsystem'.$k)!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xsystem'.$k);?>
									</div>		
									<?php } ?>
								 	<?=$v['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$v['finputs'];?>
									<?=$v['fbtn'];?>
								</div>
							</div>
						</div>

						<?php  
						echo form_close();
						}
						?>
					
				</div>
				  
				<div class="tab-pane table-responsive" id="registform">
						<div class="col-md-6">
							<?php echo form_open(base_url('Organizer/setting#registform'),array('name'=>'fregistform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-file-text"></span> <b><?=$registform['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vregist')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vregist');?>
									</div>
									<?php } else if ($this->session->flashdata('xregist')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xregist');?>
									</div>		
									<?php } ?>
								 	<?=$registform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$registform['finputs'];?>
									<?=$registform['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
						
				</div>

				<div class="tab-pane table-responsive" id="paymentform">
                   
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#paymentform'),array('name'=>'fpaymentform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-money"></span> <b><?=$payment['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vpay')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vpay');?>
									</div>
									<?php } else if ($this->session->flashdata('xpay')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xpay');?>
									</div>		
									<?php } ?>
								 	<?=$payment['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$payment['finputs'];?>
									<?=$payment['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
					
				</div>

				<div class="tab-pane table-responsive" id="notiflist">
						<div class="col-md-4">
							<?php echo form_open(base_url('Organizer/setting#notiflist'),array('name'=>'fnotifform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-bell"></span> <b><?=$notifmemform['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vnotif1')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vnotif1');?>
									</div>
									<?php } else if ($this->session->flashdata('xnotif1')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xnotif1');?>
									</div>		
									<?php } ?>
								 	<?=$notifmemform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notifmemform['finputs'];?>
									<?=$notifmemform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
						<div class="col-md-4"> 
							<?php echo form_open(base_url('Organizer/setting#notiflist'),array('name'=>'fnotifform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-bell"></span> <b><?=$notiforgform['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vnotif2')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vnotif2');?>
									</div>
									<?php } else if ($this->session->flashdata('xnotif2')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xnotif2');?>
									</div>		
									<?php } ?>
								 	<?=$notiforgform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notiforgform['finputs'];?>
									<?=$notiforgform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
						<div class="col-md-4"> 
							<?php echo form_open(base_url('Organizer/setting#notiflist'),array('name'=>'fnotifform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-bell"></span> <b><?=$notifadmform['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vnotif3')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vnotif3');?>
									</div>
									<?php } else if ($this->session->flashdata('xnotif3')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xnotif3');?>
									</div>		
									<?php } ?>
								 	<?=$notifadmform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notifadmform['finputs'];?>
									<?=$notifadmform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
				</div>
				<div class="tab-pane table-responsive" id="pagelist">
                    
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#pagelist'),array('name'=>'fpageform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-pencil-square"></span> <b><?=$pagesetting['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vpageset')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vpageset');?>
									</div>
									<?php } else if ($this->session->flashdata('xpageset')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xpageset');?>
									</div>		
									<?php } ?>
								 	<?=$pagesetting['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$pagesetting['finputs'];?>
									<?=$pagesetting['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
					
				</div>
				<div class="tab-pane table-responsive" id="emaillist">
                    
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#emaillist'),array('name'=>'fmailform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-envelope"></span> <b><?=$mail['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vmail')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vmail');?>
									</div>
									<?php } else if ($this->session->flashdata('xmail')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xmail');?>
									</div>		
									<?php } ?>
								 	<?=$mail['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$mail['finputs'];?>
									<?=$mail['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?>  
						</div>
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#emaillist'),array('name'=>'fmailtoken','class'=>'form-horizontal','method'=>'POST','id'=>'fmailtoken'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-envelope"></span> <b><?=$mailtoken['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vmtoken')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vmtoken');?>
									</div>
									<?php } else if ($this->session->flashdata('xmtoken')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xmtoken');?>
									</div>		
									<?php } ?>
								 	<?=$mailtoken['table'];?>
								 	<div class="bg-info">
								 		<blockquote>
										<p>In order to fully remove the configuration, please remove our App (SEF Membership) from Security Account through this <a href="https://security.google.com/settings/security/permissions" target="_blank" alt="remove apps">link</a></p>
											<div class="text-center">
											<img class="img-rounded" src="<?=base_url('upload/system/remove gapps.JPG')?>" width="50%"><br/>
											<small>Please remove our App, by clicking remove as shown in picture.</small>
											</div>
										</blockquote>
									</div>
								</div>
								<div class="panel-footer text-right">
									<?=$mailtoken['finputs'];?>
									<?=$mailtoken['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?>  
						</div>
					
				</div>
				<div class="tab-pane table-responsive" id="certilist">
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#certilist'),array('name'=>'fcertiform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-certificate"></span> <b><?=$certi['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vcerti')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vcerti');?>
									</div>
									<?php } else if ($this->session->flashdata('xcerti')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xcerti');?>
									</div>		
									<?php } ?>
								 	<?=$certi['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$certi['finputs'];?>
									<?=$certi['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
						<div class="col-md-6"> 
							<?php echo form_open_multipart(base_url('Organizer/setting#certilist'),array('name'=>'fdesignform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-font"></span> <b><?=$fontcerti['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vfontcerti')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vfontcerti');?>
									</div>
									<?php } else if ($this->session->flashdata('xfontcerti')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xfontcerti');?>
									</div>		
									<?php } ?>
								 	<?=$fontcerti['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$fontcerti['finputs'];?>
									<?=$fontcerti['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
						<br class="clearfix">
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#certilist'),array('name'=>'fdesignform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-certificate"></span> <b><?=$design['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vdesign')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vdesign');?>
									</div>
									<?php } else if ($this->session->flashdata('xdesign')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xdesign');?>
									</div>		
									<?php } ?>
								 	<?=$design['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$design['finputs'];?>
									<?=$design['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
						<div class="col-md-6"> 
							<?php echo form_open(base_url('Organizer/setting#certilist'),array('name'=>'ftxtcertiform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-font"></span> <b><?=$txtcerti['title'];?></b></h3>
								</div>
								<div class="panel-body">
									<?php if ($this->session->flashdata('vtxtcerti')!=null){ ?>
									<div class="alert alert-success alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('vtxtcerti');?>
									</div>
									<?php } else if ($this->session->flashdata('xtxtcerti')!=null){ ?>
									<div class="alert alert-danger alert-dismissible" role="alert">
										<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
										<?=$this->session->flashdata('xtxtcerti');?>
									</div>		
									<?php } ?>
								 	<?=$txtcerti['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$txtcerti['finputs'];?>
									<?=$txtcerti['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
				</div>
				
					
			</div>
        </div><!-- /.nav-tabs-custom -->
		
	</div>
		
		
	</div>
	
	
	<!-- Modal Details Data-->
	<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content">
        	<div class="modal-header">
			   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			   <h4><i class="fa fa-info"></i> <span id="titlepreview"></span></h4>
			</div>
			<div class="modal-body">
			</div>	
        </div>
    </div>
	</div>
	
	
	<script type="text/javascript">
	
	//details data	
		$('#DetailModal').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
		});
			
		
	//range date registration
	$(function() {
    $('#fregistphase,#fpaymentphase,#fschedulephase,#fcertiphase').daterangepicker({
    	locale: {format: 'DD/MM/YYYY'}
    });
	});
		
$('#DetailModal').on('show.bs.modal',function(e){
});

$(document).ready(function(){
	
	$('#fregistsuccess, #fmailregistsuccess, .changepage').on('changed.bs.select',function(e){
		var idtmp = $(this).selectpicker('val');
			$('#DetailModal').modal({
				show: true,
				remote: "<?=base_url('Organizer/Setting/previewTemplate/')?>/"+idtmp
			});
	});

	$('.changenotifclass').on('changed.bs.select',function(e){
		var idnotif = $(this).selectpicker('val');
			$('#DetailModal').modal({
				show: true,
				remote: "<?=base_url('Organizer/Setting/previewNotification/')?>/"+idnotif
			});
	});

	var url = window.location.href;
	var activeTab = url.substring(url.indexOf("#") + 1);
	if (activeTab!=''){
		$('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
	}
	

	$(".txtmail").summernote({
		height: 200,
		toolbar: [
			    ['style', ['bold', 'italic', 'underline', 'clear']],
			    ['font', ['bold','italic','underline','strikethrough', 'superscript', 'subscript','clear']],
			    ['fontsize', ['fontname','fontsize']],
			    ['color', ['color']],
			    ['para', ['style','ul', 'ol', 'paragraph']],
			    ['height', ['height']],
			    ['misc',['codeview','undo','redo']]
			  ]
	});

	$("#btnupdatetxtmail").click(function(){
		$('input[name="mailheader"]').val($("#txtmailheader").summernote('code'));
		$('input[name="mailfooter"]').val($("#txtmailfooter").summernote('code'));
	});

	$("#btnremoveemail").click(function(){
		$("#fmailtoken").submit();
	});

});
	</script>
</section>