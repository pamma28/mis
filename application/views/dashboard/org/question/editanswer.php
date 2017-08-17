<?php echo form_open(base_url('Organizer/Question/updateanswer'),array('name'=>'addanswer', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-pencil"></i> Edit Answer</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
</div>
<div class="modal-footer">
	<span id="keyAnswer" class="hidden"></span>
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$insub.$inid.$inbtn;?>
</div>
<?php echo form_close();?>
<script>
$('.selectpicker').selectpicker({
	size: 3
	});
var tempkey = $('#fkey').val();
$('#fkey').on('change',function(){
	if (($('#keyAnswer').html()!='') && (tempkey!=1)){
	alert('Only 1 Key Answer Allowed');
	$('#fkey').val('0');
	$('.selectpicker').selectpicker('refresh');
	}
});

</script>
