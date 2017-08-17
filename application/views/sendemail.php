<section class="content-header">
          <h1>
            Registration
            
          </h1>
</section>

<section class="content">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="panel panel-primary">
								<div class="panel-heading">
							
									<h3 class="panel-title" align="center">Personal Data Sheet</h3>
								</div>
									<div class="panel-body">
									
									
									
	
	
	<div class="well well-lg">
	<p>Hello Member,</p>
	<p>In order to make the recapitulation of Member data, we need you to fulfil the form below. The information will be used for the Membership purpose.</p>
	<p></p>
	</div>
	<?php if (validation_errors()){ ?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert">
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Close</span>
		</button>
	<?php echo validation_errors().'</div>';} ?>
	


	<?php
	echo form_open('Home/save',array('name'=>'formPDS', 'method'=>'POST','class'=>'form-horizontal'));
	?>
    
        <div class="form-group">

            <label for="fullName" class="control-label col-xs-2">Full Name</label>

            <div class="col-xs-10">

				<?php echo $incode;?>
            </div>

        </div>


        <div class="form-group">

            <div class="col-xs-offset-2 col-xs-10 text-right">

					
					<?php echo $insend;?>
            </div>

        </div>

    <?php form_close(); ?>


							</div>
						</div>

</div>


	<script>
	$(document).ready(function(){
    $("#birthdate").inputmask('date');}
	);
	</script>					
</section>