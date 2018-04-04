<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-edit fa-lg"></i> Edit<small>PDS</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-primary">
	<div class="box-body">
		<div class="panel panel-default">
		<div class="panel-body">
		<h3 class="text-center text-primary">Edit Personal Data Sheet</h3> <hr class="divider"/>
		<?php echo form_open(base_url('Member/Managepds/updatepds'),array('name'=>'uppds', 'method'=>'POST','class'=>'form-horizontal'));?>
		<?=$rdata;?>
        </div>
        <div class="panel-footer text-right">
        <?=$inbtn;?>
        </div>
        <?php echo form_close();?>
    </div>
	</div>

	<script type="text/javascript">
	$(document).ready(function(){
		$('.selectpicker').selectpicker({
		      size: 6
		    });
		$("#bdate").inputmask('date');
		    });
		 $("#nohp").inputmask('08[99999999999]');
		  //check email availabelity
		  $('#Email').bind('keyup change', function() {
			var email = $('#Email').val();
		    $.post('<?php echo base_url('Register/checkemail'); ?>', {email: email}, function(d) {
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

		  $('#nohp').bind('keyup change', function(e) {
			var hp = $(this).val().replace('_','');
			var eid = $(this).attr('id');
			$.post('<?php echo base_url('Register/checkphone'); ?>', {nohp: hp}, function(d) {
								if (d == 1)
								{
									$('#'+eid).parent().find('.text-danger').removeClass('hidden');
									$('#submit').attr('disabled', 'disabled');
								}
								else
								{
									$('#'+eid).parent().find('.text-danger').addClass('hidden');
									$('#submit').removeAttr('disabled');
								}
							});
			});
	</script>

</section>