<?php echo form_open_multipart(base_url('Organizer/Question/updateattach'),array('name'=>'editattach', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-pencil"></i> Edit Question Attachment</h4>
</div>
<div class="modal-body table-responsive">
	
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$insub.$inid.$inbtn;?>
</div>
<?php echo form_close();?>
<script>
$('.selectpicker').selectpicker({
	size: 3
	});
</script>

</script>
