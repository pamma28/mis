
<?php echo form_open(base_url('Organizer/Level/savelevel'),array('name'=>'addcat', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-plus"></i> Add Level</h4>
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
<script>
$('#flow,#fhigh').numeric();
$('#flow,#fhigh').keyup(function(){
	if (($('#flow').val() > $('#fhigh').val()) && ($('#fhigh').val() != '')) {
		alert('Highest Mark should bigger');
		$('#fhigh').focus();
	}
});
</script>