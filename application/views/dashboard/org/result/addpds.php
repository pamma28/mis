<!-- Content Header (Page header) -->
<section class="content-header">
	<h1><i class="fa fa-plus"></i> Add<small>Registration Data</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
			
			<div class="nav-tabs-custom">
				<!-- Tabs within a box -->
				<ul class="nav nav-tabs ">
					<li class="dpane active"><a class="text-info" data-toggle="tab" href="#addnouser">Add with Available Username</a></li>
					<li class="dpane"><a class="text-info" data-toggle="tab" href="#addwithuser">Add with New Username</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="addnouser">
						<?php echo form_open(base_url('Organizer/PDS/savepds'),array('name'=>'addlogin', 'method'=>'POST','class'=>'form-horizontal'));?>
						<?=$rdata;?>
						<div class="text-right">
						<?=$inbtn;?>
						</div>
						<?=form_close();?>
					</div>
					<div class="tab-pane" id="addwithuser">
						<div class="text-center bg-warning h3">
						NOTE: Username will equal to NIM, Password will equal to birthdate (ddmmyyyy).
						</div><br/>
						<?php echo form_open(base_url('Organizer/PDS/savepds'),array('name'=>'addlogin', 'method'=>'POST','class'=>'form-horizontal'));?>
						<?=$r2data;?>
						<div class="text-right">
						<?=$inbtn;?>
						</div>
						<?=form_close();?>
					</div>
				</div>
			</div><!-- /.nav-tabs-custom -->
		
		<?php echo form_close();?>
		<script>
		 $('.selectpicker').selectpicker({
		  size: 6
		});
		  
		  $('#Email,#inpemail').bind('keyup change', function(e) {
			var email = $(this).val();
			var eid = $(this).attr('id');
			$.post('<?php echo base_url('Organizer/Memberaccount/checkemail'); ?>', {email: email}, function(d) {
								if (d == 1)
								{
									$('#'+eid).parent().find('.text-primary').css('display', 'none');
									$('#'+eid).parent().find('.text-danger').css('display', 'block');
									$('#'+eid).parent().parent().parent().find('.btn-primary').attr('disabled', 'disabled');
								}
								else
								{
									$('#'+eid).parent().find('.text-danger').css('display', 'none');
									$('#'+eid).parent().find('.text-primary').css('display', 'block');
									$('#'+eid).parent().parent().parent().find('.btn-primary').removeAttr('disabled');
								}
							});
			});
			
			$('#username2').bind('keyup change', function() {
			var user = $('#username2').val();
			$.post('<?php echo base_url('Organizer/Memberaccount/checkuser'); ?>', {user: user}, function(d) {
								if (d == 1)
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


			$('.selectpicker').on('change',function(e) {
			var selected = $(this).find('option:selected').val();
			$.post('<?php echo base_url('Organizer/PDS/getdetailuser'); ?>',{user: selected}, function(d) {
				d = $.parseJSON(d);
				$('#inpname').val(d.uname);
				$('#inpemail').val(d.uemail);
				$('#inphp').val(d.uhp);       
				});
			});  

			$('.dpane').on('click',function(e) {
				$('#submit').removeAttr('disabled');
			});
			
			$(document).ready(function(){
			$("#bdate,#bdate2").inputmask('date');}
			);
		</script>
</section>