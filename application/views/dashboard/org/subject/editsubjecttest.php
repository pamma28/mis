<!-- Content Header (Page header) -->
<section class="content-header">
	<h1><i class="fa fa-list"></i> Edit<small>Subject</small></h1>
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
	<?php echo form_open(base_url('Organizer/Subject/updatesubjecttest'),array('name'=>'fsubject', 'method'=>'POST','class'=>'form-horizontal'));?>
	<div class="modal-body">
	   <h4 class="text-center"><b>Detail Test Choosen</b></h4>
		
		<?=$rdata;?>
		
	</div>
	<div class="modal-footer">
		<?=$inid;?>
		<a class="btn btn-default btn-ok btn" href="<?=base_url('Organizer/Subject');?>">Back</a>
		<?=$inbtn;?>
	</div>
	<?php echo form_close();?>
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
	

<script>
	$('.selectpicker').selectpicker({
	size: 3
	});
	$('input[name="fqtot[]"],input[name="fqper[]"]').numeric();
	
	//delete modal confirmation
	$('#confirm-delete').on('show.bs.modal', function(e) {
		$(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
		});
	
	//details data	
		$('#DetailModal').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
		});
		
		$('#DetailModal').on("show.bs.modal", function (e) {
		 var dtot = $(e.relatedTarget).data('tot');
			$('#DetailModal').on("loaded.bs.modal", function (e) {
			$(e.target).find('#infoTot').html(dtot);
			});
		});
		
	
	// populate 100%
	$('input[name="fqper[]"]').on('change',function(d){
		var tot = 0;
		$("input[name='fqper[]']").each(function () {
			tot = tot + parseInt($(this).val());
		});
		$('#btnAdd').attr('data-tot',tot);
		if (tot>100){
		var res = 100-(tot-parseInt($(this).val()));
		$(this).val(parseFloat(res,2));
		determinetot();
		alert('Please make sure total percentage is 100');
		}
	});	
	
	//determine button attr
	$(document).ready(function(){
	determinetot();
	});
	
	function determinetot(){
	var tot = 0;
	$("input[name='fqper[]']").each(function () {
	tot = tot + parseInt($(this).val());
	});
	$('#btnAdd').attr('data-tot',tot);
	}
</script>
</section>