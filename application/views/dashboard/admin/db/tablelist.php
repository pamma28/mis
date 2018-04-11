<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-database fa-lg"></i> Manage<small>Database</small></h1>
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
		<div class="row">
			<div class="col-md-6"> 
				<a href="#" class="btn btn-primary" data-toggle="modal" data-target=".bs-import-data"><i class="fa fa-cloud-upload"></i> Restore Database</a>
				<a href="#" class="btn btn-primary" data-toggle="modal" data-target=".bs-export-data"><i class="fa fa-cloud-download"></i> Backup Database</a>
				
			</div>
			<div class="col-md-6">
					
			</div>
		</div>	
	</div>
	<div class="box-body table-responsive">
	<?=$listlogin;?>
	</div>
		
		<div class="box-footer clearfix">
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
			<br class="clearfix"/>
			<hr/>

			<div class="box box-info">
					<div class="box-body">
						<h4><b>Setting Database Backup (Automated)</b></h4>
						<?php echo form_open(base_url('Admin/Impexp/saveschedulebackup'),array('class'=>'form-inline','method'=>'POST','id'=>'formreminder'));?>
						<div class="form-group">	
							<label class="form-label">Backup Setting :</label>
							<?=$btnset;?> <small><span class="bg-info"><i>*Please "Turn ON" if you want to get new backup database via Email</i></span></small>
						</div><h5></h5>
						<div class="form-group">	
							<label class="form-label">Backup Period :</label>
							<div class="input-group">
							<?=$period;?>
							<span class="input-group-addon">Day(s)</span>
							</div>
							<small><span class="bg-info"><i>*on certain day(s), newest backup will send to you</i></span></small>
						</div><h5></h5>
						<div class="form-group">	
							<label class="form-label">Email Backup :</label>
							<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
							<?=$inputemailbackup;?>
							</div>
							<small><span class="bg-info"><i>*Target email of backup database</i></span></small>
						</div><h5></h5>
						<div class="form-group">
						<label class="form-label">
							Start Backup (Date):
						</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<?=$datestart;?>
						</div>
						</div>
						<div class="form-group text-right">
						<input type="submit" value="Update Reminder" class="btn btn-primary">
						<?php echo form_close();?>
						</div>

						<h5><span class="bg-info text-primary"><i class="fa fa-info-circle"></i> <i>This will send email with newest database backup attachment.</i></span></h5>
					</div>
				</div>
		</div>
	</div>
	
	<!-- Modal Import Data-->
	<div class="modal fade bs-import-data" tabindex="-1" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open_multipart($factimp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-upload"></i> Restore Database</h4>
					
			</div>
			<div class="modal-body">
					<div class="form-group">
					<label>Choose File </label>
					<?=$finfile;?>
					<p class="help-block">Please choose only (.sql) file. (Max 10Mb)</p>
					</div>
				<span class="label label-info"><i class="fa fa-info-circle"></i> Please unzip first, then select .sql file to restore database</span>
				<span class="label label-warning"><i class="fa fa-exclamation-triangle"></i> Be careful import data can ruin database, if it is not well-synchronized</span>
				
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
	<div class="modal fade bs-export-data" tabindex="-1" role="dialog" id="mdlexport">
	  <div class="modal-dialog">
		<div class="modal-content">
			<?php echo form_open($factexp,array('method'=>'POST'));?>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-cloud-download"></i> Backup Database</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Selected Column(s)</label>
					<div class="input-group">
                    <div class="bg-info" id="selectedcol" style="max-height:200px;overflow-y: auto;width:250px;"></div>
					<?=$fselectedidcol;?>
					</div>
					<br/>
					<div class="input-group">
					<span class="input-group-addon"><?=$falldb;?> </span><span class="input-group-addon"><i class="fa fa-check"></i> Checked it, if you want select all tables.</span> 
					</div>	
					<p class="label label-warning"><i class="fa fa-info-circle"></i> To preserve the syncronization of database, please backup all tables.</p>
							
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
                <h4><i class="fa fa-trash"></i> Are you sure want to Drop (Empty) selected table?</h4>
            </div>
            <div class="modal-content" align="center">
				<br/><p class="label label-danger" style="font-size:16px;"><i class="fa fa-exclamation-triangle fa-lg"></i> IMPORTANT: the action cannot reversed once done.</p>
				<br/><br/><p class="label label-info" style="font-size:16px;margin-top:30px;"><i class="fa fa-info-circle fa-lg"></i> PS: it is better save the backup data, before do this action.</p>
				<br/>
				<br/>
			</div>			
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok btn-sm">Delete</a>
            </div>
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
	
	//export data
		$('#mdlexport').on('show.bs.modal', function(e) {
		var tables = new Array();
		var values = new Array();
		$.each($("input[name='check[]']:checked"), function() {
		tables.push('- '+$(this).parent().parent().find('.tblname').text()+'<br/>');
		values.push($(this).val());
		$('#selectedcol').html(tables);
		$('#selectedidcol').val(values);
		});
		});
			
	
	
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
	
	$(function () {
    $("#checkalldb").click(function () {
        if ($("#checkalldb").is(':checked')) {
            $("#selectedcol").attr('class','bg-info hidden');

        } else {
             $("#selectedcol").attr('class','bg-info visible');
        }
    });
	});

	$(document).ready(function() {
	    $("#setdbbackup").bootstrapToggle({
			size: "medium",
			onstyle: "success",
			offstyle: "danger",
			on: "<b>Turn ON</b>",
			off: "<b>Turn OFF</b>",
			width: 150,
			height: 30
		});

		$("#inputdbperiod").numeric();
  	});

  	$(function() {
	    $('#inputbackupdate').daterangepicker({
	    	singleDatePicker: true,
	    	timePicker: true,
	    	autoUpdateInput: false,
	    	timePickerIncrement: 5,
	    	locale: {format: 'DD-MM-YYYY HH:mm:ss',
	    			cancelLabel: 'Clear'}
	    });
	});
	$('#inputbackupdate').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY HH:mm:ss'));
	});
	
	
	</script>
</section>