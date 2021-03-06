<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-check fa-lg"></i> My<small>Schedule</small></h1>
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
		<div class="box-body table-responsive">
			<div class="panel panel-primary">
				<div class="panel-heading panel-heading-sm">
					<h5 class="panel-title text-center"><b>My Schedule</b></h5>
				</div>
				<div class="panel-body">
				<?=$mysche;?>
				
				</div>
			</div>
			<hr class="clearfix" />
			<div class="panel panel-default">
				<div class="panel-heading panel-heading-sm">
					<h5 class="panel-title text-center"><b>Previous Schedule</b></h5>
				</div>
				<div class="panel-body">
				<?=$myprevsche;?>
				
				</div>
			</div>
			<div class="box box-info <?php (!$ustatus) ? print('hidden'):null;?>">
					<div class="box-body">
						<h4><b>Setting Your Reminder</b></h4>
						<?php echo form_open(base_url('Member/Scheduletest/savereminder'),array('class'=>'form-inline','method'=>'POST','id'=>'formreminder'));?>
						<div class="form-group">	
							<label class="form-label">Your Reminder :</label>
							<?=$btnreminder;?> <small><span class="bg-info"><i>*Please "Turn ON" if you want to have SMS reminder</i></span></small>
						</div><h5></h5>
						<div class="form-group">
						<label class="form-label">
							Test Reminder Date:
						</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							<?=$reminder;?>
						</div>
						</div>
						<div class="form-group text-right">
						<input type="submit" value="Update Reminder" class="btn btn-primary">
						<?php echo form_close();?>
						</div>

						<h5><span class="bg-info text-primary"><i class="fa fa-info-circle"></i> <i>The reminder will send you SMS about the Test.</i></span></h5>
					</div>
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
	
	<script>
	$(document).ready(function() {
	    $("#reminderonoff").bootstrapToggle({
			size: "medium",
			onstyle: "success",
			offstyle: "danger",
			on: "<b>Turn ON</b>",
			off: "<b>Turn OFF</b>",
			width: 150,
			height: 30
		});
  	});
		//delete modal confirmation
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});

	//date reminder
	$(function() {
	    $('#inputreminder').daterangepicker({
	    	singleDatePicker: true,
	    	timePicker: true,
	    	autoUpdateInput: false,
	    	timePickerIncrement: 5,
	    	locale: {format: 'DD-MM-YYYY HH:mm:ss',
	    			cancelLabel: 'Clear'}
	    });
	});
	
	$('#inputreminder').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY HH:mm:ss'));
	});

	</script>

</section>