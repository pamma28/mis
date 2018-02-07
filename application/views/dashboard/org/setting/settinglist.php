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
                <li class="active"><a data-toggle="tab" href="#generallist"><span class="fa fa-wrench"></span> System Parameter</a></li>
                <li><a data-toggle="tab" href="#registform"><span class="fa fa-file-text"></span>  Registration Form</a></li>
                <li><a data-toggle="tab" href="#notiflist"><span class="fa fa-bell"></span>  Notification Setting</a></li>
                <li><a data-toggle="tab" href="#pagelsit"><span class="fa fa-pencil-square"></span>  Page Setting</a></li>
                <li><a data-toggle="tab" href="#emaillist"><span class="fa fa-envelope"></span>  Email Setting</a></li>
                <li><a data-toggle="tab" href="#dashlist"><span class="fa fa-home"></span>  Dashboard Setting</a></li>
                <li><a data-toggle="tab" href="#registform"><span class="fa fa-certificate"></span>  Certificate Setting</a></li>
                <li><a data-toggle="tab" href="#registform"><span class="fa fa-file-text"></span>  Registration Form</a></li>
            </ul>
            <div class="tab-content">
            	<div class="tab-pane table-responsive active" id="generallist">
                    
						<?php foreach ($settinglist as $k => $v) { ?>
						<?php echo form_open(base_url('Organizer/setting/saveparameter/'.$k),array('name'=>'fsetting'.$k,'class'=>'form-horizontal','method'=>'POST'));?>
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
							<?php echo form_open(base_url('Organizer/setting/saveformregist/'),array('name'=>'fregistform','class'=>'form-horizontal','method'=>'POST'));?>
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
									<?=$registform['fbtn'];?>
								</div>
							</div>

						<?php  
						echo form_close();
						?> 
						</div>
						<div class="col-md-6">
							<div class="box box-primary">
							 	<div class="box-body" id="prevsettingregistform"> <p class="text-center"><i>Preview Template</i></p>
							 	</div>
							</div>
						</div>
				</div>
				<div class="tab-pane table-responsive" id="notiflist">
						<div class="col-md-12">
							<div class="box box-primary">
								<div class="box-header text-center">
									<h5 class="box-title">Notification Preview</h5>
								</div>
								<div class="box-body">
									<div id="notiftextpreview">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<?php echo form_open(base_url('Organizer/setting/saveformregist/4'),array('name'=>'fregistform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-file-text"></span> <b><?=$notifmemform['title'];?></b></h3>
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
								 	<?=$notifmemform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notifmemform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
						<div class="col-md-4"> 
							<?php echo form_open(base_url('Organizer/setting/saveformregist/5'),array('name'=>'fregistform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-file-text"></span> <b><?=$notiforgform['title'];?></b></h3>
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
								 	<?=$notiforgform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notiforgform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
						<div class="col-md-4"> 
							<?php echo form_open(base_url('Organizer/setting/saveformregist/6'),array('name'=>'fregistform','class'=>'form-horizontal','method'=>'POST'));?>
							<div class="panel panel-primary">
								<div class="panel-heading panel-heading-sm">
									<h3 class="panel-title text-center"><span class="fa fa-file-text"></span> <b><?=$notifadmform['title'];?></b></h3>
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
								 	<?=$notifadmform['table'];?>
								</div>
								<div class="panel-footer text-right">
									<?=$notifadmform['fbtn'];?>
								</div>
							</div>

							<?php  
							echo form_close();
							?> 
						</div>
				</div>
				<div class="tab-pane table-responsive" id="pagelsit">
                    
						<div class="col-md-6"> 
							<a href="<?=base_url('Organizer/PDS/addpds');?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Setting</a> 
						</div>
					
				</div>
				<div class="tab-pane table-responsive" id="emaillist">
                    
						<div class="col-md-6"> 
							<a href="<?=base_url('Organizer/PDS/addpds');?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Setting</a> 
						</div>
					
				</div>
				<div class="tab-pane table-responsive" id="dashlist">
                   
						<div class="col-md-6"> 
							<a href="<?=base_url('Organizer/PDS/addpds');?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Setting</a> 
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
			
        </div>
    </div>
	</div>
	
	<!-- Modal Delete Data-->
	<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="fa fa-trash"></i> Are you sure want to delete selected data?</h4>
            </div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok btn-sm">Delete</a>
            </div>
        </div>
    </div>
	</div>
	
	<!-- Modal Selected Data-->
	<div class="modal fade bs-selecteddata" id="SelectedModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<?php echo form_open($factselected,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" ><i id="selectedicon"></i> <span id="selectedtitle"></span></h4>
			</div>
			<div class="modal-body">
					Are you sure want to <span id="selectedcontent"></span> selected data?
					<div class="bg-info" id="selecteduser" style="max-height:100px;overflow-y: auto;width:250px;"></div>
			</div>
			<div class="modal-footer">
				<?=$idac.$idtype;?>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" id="selectedbutton" value="Confirm">
			</div>
				<?=form_close();?>
        </div>
    </div>
	</div>
	
	
	<script type="text/javascript">
	//delete modal confirmation
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});
	
	
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
		
	$(function() {
		//Date range 
		cb(moment().subtract(29, 'days'), moment());

		$('.frange').daterangepicker({
			locale: {format: 'YYYY-MM-DD'},
			"startDate": new Date(),
			"endDate": new Date(),
			ranges: {
			   'Today': [moment(), moment()],
			   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			   'This Month': [moment().startOf('month'), moment().endOf('month')],
			   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);
		
		function cb(start, end) {
			$('#rangedate').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		}
	});
	
$(document).ready(function(){
	$('#registsuccess, #mailregistsuccess').on('changed.bs.select',function(e){
		$.post('<?php echo base_url('Organizer/Setting/previewTemplate'); ?>', {idtmp: $(this).selectpicker('val')}, function(d) {
			$('#prevsettingregistform').empty().html(d);
		});
	});

	$('.changenotifclass').on('changed.bs.select',function(e){
		$.post('<?php echo base_url('Organizer/Setting/previewNotification'); ?>', {idnotif: $(this).selectpicker('val')}, function(d) {
			$('#notiftextpreview').empty().html(d);
		});
	});
});
	</script>
</section>