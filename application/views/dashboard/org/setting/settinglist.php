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
		<div class="row">
			<div class="col-md-6"> 
				<a href="<?=base_url('Organizer/PDS/addpds');?>" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> Add Setting</a> 
			</div>
		</div>
				
	</div>
	<div class="box-body">
		<div class="row">
			
			<?php foreach ($settinglist as $k => $v) { ?>
			
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading panel-heading-sm">
						<h3 class="panel-title text-center"><span class="fa fa-gear"></span> <b><?=$v['title'];?></b></h3>
					</div>
					<div class="panel-body">
					 	<?=$v['table'];?>
					</div>
					<div class="panel-footer text-right">
						<?=$v['fbtn'];?>
					</div>
				</div>
			</div>

			<?php } ?>
		</div>
	</div>
		
		<div class="box-footer clearfix">
		
			
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting Registration Phase</a></legend>
			<div id="collapseSetting" class="collapse">
			<form name="fsetperiod" class="form-inline" action="<?=$fsendper;?>" method="POST" >
			<div class="form-group">
				<label class="input-label" for="startTime">Registration Phase (Range Date) :</label>
				<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span><?=$fregist;?>
				</div>
			</div>
			<?=$fbtnperiod;?>
			</form>
			</div>
			</fieldset>
			
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
	
	
	
	</script>
</section>