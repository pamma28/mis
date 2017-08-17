<?php echo form_open(base_url('Organizer/Certificate/savecerti'),array('name'=>'addcerti', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-plus"></i> Add Certificate</h4>
</div>
<div class="modal-body">
	
	<?=$rdata;?>
	
</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn" data-dismiss="modal">Close</a>
	<?=$inbtn;?>
</div>
<?php echo form_close();?>
<script>
	$('.selectpicker').selectpicker({
		  size: 8
		});
	
	$('#NoCerti').bind('keyup change', function() {
	var no= $('#NoCerti').val();
    $.post('<?php echo base_url('Organizer/Certificate/checknocerti'); ?>', {nocerti: no}, function(d) {
                        if (d >= 1)
                        {
                            $('#usuccess').css('display', 'none');
                            $('#ufailed').css('display', 'block');
							$('#submit').attr('disabled', 'disabled');
                        }
                        else
                        {
                            $('#ufailed').css('display', 'none');
                            $('#usuccess').css('display', 'block');
							$('#submit').removeAttr('disabled');
                        }
                    });
	});
</script>