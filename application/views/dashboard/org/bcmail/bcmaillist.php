<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-envelope-o fa-lg"></i> Email<small>Broadcast</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
			<?php if ($this->session->flashdata('v')!=null){ ?>
			<div class="box-header">
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('v');?>
			</div>
			<?php } else if ($this->session->flashdata('x')!=null){ ?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('x');?>
			</div>		
			</div>
			<?php } ?>
		
		<div class="box-body table-responsive">
            <div class="col-md-2">
              <a class="btn btn-primary btn-block margin-bottom" href="<?=base_url('Organizer/Mailbroadcast/composemail')?>"><i class="fa fa-pencil"></i> Compose</a>
              <div class="box box-solid">
                <div class="box-body no-padding">
                  <ul class="nav nav-pills nav-stacked">
                    <li><a href="#"><i class="fa fa-envelope-o"></i> Sent <span class="label label-primary pull-right"><?=$totmail;?></span></a></li>
                    <li><a href="#" data-toggle="modal" data-target=".bs-import-data"><i class="fa fa-cloud-upload"></i> Import</a></li>
                    <li><a href="#" data-toggle="modal" data-target=".bs-export-data"><i class="fa fa-cloud-download"></i> Export</a></li>
                    <li><a href="#" data-toggle="modal" data-target=".bs-print-data"><i class="fa fa-print"></i> Print </a></li>
                  </ul>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
              <div class="box box-solid">
                <div class="box-header with-border">
                  <h3 class="box-title">Labels</h3>
                </div>
                <div class="box-body no-padding">
                  <ul class="nav nav-pills nav-stacked">
                    <li><a href="#"><i class="fa fa-users text-primary"></i> Broadcast Mail</a></li>
                    <li><a href="#"><i class="fa fa-user text-yellow"></i> Single Mail</a></li>
                  </ul>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
            <div class="col-md-10">
              <div class="box box-primary">
                <div class="box-header with-border">
					<div class="mailbox-controls">
                    <div class="row">
					<!-- Check all button -->
						
                   <div class="btn-group">
					<div class="btn btn-default btn-sm">
					<?=$btncheckall;?>
					</div>
					</div>
					<div class="btn-group">
                      <a href="#" class="btn btn-sm btn-default" data-finput="0" data-btn="btn btn-sm btn-danger" data-icon="fa fa-trash-o" data-title="Delete All" data-toggle="modal" data-target=".bs-selecteddata"><i class="fa fa-trash-o"></i></a>
                    </div><!-- /.btn-group -->
                    <a href="<?=base_url('Organizer/Mailbroadcast');?>" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></a>
                    <div class="pull-right">
						<?php 
						echo form_open(current_full_url(),array('name'=>'fq', 'method'=>'GET','class'=>'form-inline'));
						?>		
						<div class="input-group">
							<span class="input-group-addon input-xs">Filter</span>
							<?php
								echo $inc.'</div><div class="input-group input-xs">'.$inq.
								'<span class="input-group-btn input-xs">';
								echo $bq.$inv.'</span></div>';
								echo form_close();
							?>
					<div class="text-right"><a href="<?=current_url();?>" class="label label-danger">Clear Search</a> <a class="label label-info" role="button" data-toggle="collapse" href="#collapseAdvanced" aria-expanded="<?php echo(empty($d)? 'false': 'true');?>" aria-controls="collapseExample">Advanced search</a></div>
					</div>
					</div>
                    <div class="row">
						<div class="<?php echo(empty($d)? 'collapse': 'collapse in');?> col-md-12" id="collapseAdvanced" aria-expanded="<?php echo(empty($d)? 'false': 'true');?>">
						  <div class="well">
							<?php 
									echo form_open(current_full_url(),array('name'=>'fadvq', 'method'=>'GET','class'=>'form-inline'));
							?>	
								
									<?=$advance;?>
								<div class="text-right">
								<?=$bq;?>
								</div>
							<?php 
							echo form_close();
							?>
						  </div>
						</div>
                    
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">
                  
                  <div class="table-responsive mailbox-messages">
                    <?=$listlogin;?>
				  </div><!-- /.mail-box-messages -->
                </div><!-- /.box-body -->
                <div class="box-footer">
					<!-- Show perpage -->
						<div class="pull-left">
							<div class="btn-group">
										  <button class="btn btn-default" type="button">Per Page</button>
										  <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">
											<span class="caret"></span>
											<span class="sr-only">Toggle Dropdown</span>
										  </button>
										  <ul role="menu" class="dropdown-menu">
											<?php
											foreach($perpage as $p){
											echo '<li><a href="'.$urlperpage.$p.'">'.$p.'</a></li>';
											}?>
										  </ul>
							</div>
						</div>
						<div class="pull-right">
						<!-- Show pagination links -->
						<ul class="pagination pagination-sm no-margin pull-right">
						<?php foreach ($links as $link) {
						echo "<li>". $link."</li>";
						} ?>
						</ul>
						</div>
                </div>
              </div><!-- /. box -->
            </div><!-- /.col -->
          
	
	
	
		
	</div>
		
		<div class="box-footer clearfix">
			
			<br/>
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting Mail Footer</a></legend>
			<div id="collapseSetting" class="collapse">	
			<h5 class="text-info"><span class="fa fa-info-circle"></span> Current Mail Footer</h5>
			<form name="fsetperiod" class="form-inline" action="<?=$fsendper;?>" method="POST" >	
				
				<div class="text-center">
				<div class="form-group text-left">
				<?=$editfooter;?>
				</div>
				</div>
			<div class="text-right"><?=$fcode.$fbtnupdate;?></div>
			</form>		
			</div>
			</fieldset>
			
		</div>
	</div>
	
	
	
	<!-- Modal Import Data-->
	<div class="modal fade bs-import-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open_multipart($factimp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-upload"></i> Import Data</h4>
			</div>
			<div class="modal-body">
					<div class="form-group">
					<label>Download Pre-defined .xls file </label>
					</br><a href="<?=base_url('Organizer/Mailbroadcast/predefinedimport');?>" type="button" class="btn btn-warning btn-sm">Download sample format import .xls</a>
					<p class="help-block">Please only use this format to import data.</p>
					</div>
					<div class="form-group">
					<label>Choose File </label>
					<?=$finfile;?>
					<p class="help-block">Please choose Microsoft Excel 2003 version (.xls)</p>
					</div>				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?php echo $fbtnimport;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	<!-- Modal Export Data-->
	<div class="modal fade bs-export-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($factexp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-download"></i> Export Data</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Choose Column(s)</label>
					<div class="input-group">
                    <?=$fcol;?>
					</div>
					<br/>
					<div class="input-group">
						<?=$fcheckcol;?>
						<?=$funcheckcol;?>
					</div>
					<p class="help-block">*Hold ctrl to choose multiple columns.</p>
							
				</div>
				<hr class="divider"/>
				<div class="form-group">
					<label>Determine Interval Time</label>
					<div class="input-group">
						<span class="input-group-addon"><?=$fusedate;?></span>
						<?=$fdtrange;?>
						<span class="input-group-addon"><i class="fa fa-calendar"></i> Use Date</span>
					</div>
					<p class="help-block">*Leave uncheck to export all data.</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?=$fbtnexport;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	<!-- Modal Print Data-->
	<div class="modal fade bs-print-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($factprint,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-print"></i> Print Data</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Choose Column(s)</label>
					<div class="input-group">
                    <?=$fcol;?>
					</div>
					<br/>
					<div class="input-group">
						<?=$fcheckcol;?>
						<?=$funcheckcol;?>
					</div>
					<p class="help-block">*Hold ctrl to choose multiple columns.</p>
							
				</div>
				<hr class="divider"/>
				<div class="form-group">
					<label>Determine Interval Time</label>
					<div class="input-group">
						<span class="input-group-addon"><?=$fusedate;?></span>
						<?=$fdtrange;?>
						<span class="input-group-addon"><i class="fa fa-calendar"></i> Use Date</span>
					</div>
					<p class="help-block">*Leave uncheck to print all data.</p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<?=$fbtnprint;?>
			</div>
				<?=form_close();?>
		</div>
	  </div>
	</div>
	
	<!-- Modal Details Data-->
	<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
	
	// populate checked data and show in modal
	$('#SelectedModal').on('show.bs.modal', function(e) {
		$(this).find('#selectedicon').attr('class',$(e.relatedTarget).data('icon'));
		$(this).find('#selectedtitle').text($(e.relatedTarget).data('title'));
		$(this).find('#selectedcontent').text($(e.relatedTarget).data('title'));
		$(this).find('#selectedbutton').attr('class',$(e.relatedTarget).data('btn'));
		$(this).find('#selectedtype').val($(e.relatedTarget).data('finput'));
		var tables = new Array();
		var values = new Array();
		$.each($("input[name='check[]']:checked"), function() {
		tables.push('- '+$(this).parent().parent().find('.uname').text()+'<br/>');
		values.push($(this).val());
		$('#selecteduser').html(tables);
		$('#selectedid').val(values);
		});
		});
	
	//details data	
		$('#DetailModal').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
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
	
	$('.selectall').click(function() {
        $('.selectcol option').prop('selected', true);
    });
	$('.unselectall').click(function() {
        $('.selectcol option').prop('selected', false);
    });
	$('.close').click(function() {
        $('.selectcol option').prop('selected', false);
    });
	
	// function check/uncheck all at once
	$(function () {
    $("#c_all").click(function () {
        if ($("#c_all").is(':checked')) {
            $("input[name='check[]']").each(function () {
                $(this).prop("checked", true);
            });

        } else {
            $("input[name='check[]']").each(function () {
                $(this).prop("checked", false);
            });
        }
    });
	});
	
	
	$("#editfoo").summernote({
			 minHeight: 200,
			 width: 500
		});
	$("#editfoo").summernote('code',$('input[name="fcode"]').val());
	$("#btnupdateset").click(function(){
		$('input[name="fcode"]').val($("#editfoo").summernote('code'));
	});
	</script>
</section>