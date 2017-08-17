<div id="containeradd">
<?php echo form_open(base_url('Organizer/Agenda/saveagn'),array('name'=>'addtmp', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-calendar"></i> Add Agenda</h4>
</div>
<div class="modal-body">
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<?=$inid;?>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
</div>
<script>
$(document).ready(function() {
	$('.selectpicker').selectpicker({
		  size: 4
		});
	$(document).ready(function(){
			$("#agdate").inputmask('date');}
			);
});

</script>
