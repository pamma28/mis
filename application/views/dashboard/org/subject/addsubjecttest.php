<?php echo form_open(base_url('Organizer/Subject/savesubjecttest'),array('name'=>'addsubjecttest', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-plus"></i> Add Subject Test</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	<label class="label label-info">Total Assessment Percentage being Used is: <span id="infoTot">0</span>%</label>
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inid.$intot.$inbtn;?>
</div>
<?php echo form_close();?>
<script>
 $('.selectpicker').selectpicker({
		  size: 3
		});
 $('#fqtot,#fqper').numeric();
 
 $(document).ready(function(){
	$('input[name="fqper"]').on('change',function(d){
	var ttot = 100 - parseInt($('#infoTot').html());
	if ($(this).val() > ttot){
	alert('Total Assessment Percentage is more than 100%');
	$(this).val(ttot);
	}
	});
 });
 
</script>
