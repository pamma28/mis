<?php echo form_open(base_url('Organizer/Certificate/updatecerti'),array('name'=>'upcerti', 'method'=>'POST','class'=>'form-horizontal'));?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-certificate"></i> Edit Certificate</h4>
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
  //check email availabelity
  $('#NoCerti').bind('keyup change', function() {
	var no = $('#NoCerti').val();
    $.post('<?php echo base_url('Organizer/Certificate/checknocerti'); ?>', {nocerti: no}, function(d) {
                        if (d == 1)
                        {
                            $('#valsuccess').css('display', 'none');
                            $('#valfailed').css('display', 'block');
                            $('#submit').attr('disabled', 'disabled');
							
                        } 
						else if(d == 0)
                        {
                            $('#valfailed').css('display', 'none');
                            $('#valsuccess').css('display', 'block');
							$('#submit').removeAttr('disabled');
                        }
                    });
	});
	
</script>