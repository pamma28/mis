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
	echo form_open('Register/save',array('name'=>'formPDS', 'method'=>'POST','class'=>'form-horizontal'));
	?>
    
        <div class="form-group">

            <label for="fullName" class="control-label col-xs-2">Full Name</label>

            <div class="col-xs-10">

				<?php echo $innama;?>
            </div>

        </div>

        <div class="form-group">

            <label for="NIM" class="control-label col-xs-2">NIM</label>

            <div class="col-xs-10">

                <?php echo $innim;?>
				
            </div>

        </div>

        <div class="form-group">

            <label for="gender" class="control-label col-xs-2">Gender</label>

            <div class="col-xs-10">

                <?php echo $ingen;?>

            </div>

        </div>

        <div class="form-group">

            <label for="faculty" class="control-label col-xs-2">Faculty</label>

            <div class="col-xs-10">

                <?php echo $infac;?>

            </div>

        </div>

        <div class="form-group">

            <label for="birthplace" class="control-label col-xs-2">Birth Place</label>

            <div class="col-xs-10">

                <?php echo $inbplc;?>
				
            </div>

        </div>

        <div class="form-group">

            <label for="birthdate" class="control-label col-xs-2">Birth Date</label>

            <div class="col-xs-10">

                <div class="input-group">
				<?php echo $inbdt;?>
				
				</div>

            </div>

        </div>

        <div class="form-group">

            <label for="Email" class="control-label col-xs-2">Email</label>

            <div class="col-xs-10">

                <?php echo $inemail;?>
				
            </div>

        </div>

        
        <div class="form-group">

            <label for="socmed" class="control-label col-xs-2">Social Media</label>

            <div class="col-xs-10">

                <?php echo $insoc;?>
				
            </div>

        </div>

		
		<div class="form-group">

            <label for="socmed" class="control-label col-xs-2">BBM PIN</label>

            <div class="col-xs-10">

                <?php echo $inbbm;?>
				
            </div>

        </div>

        <div class="form-group">

            <label for="hp" class="control-label col-xs-2">Phone</label>

            <div class="col-xs-10">

                <?php echo $inhp;?>
				
            </div>

        </div>

        <div class="form-group">

            <label for="homenow" class="control-label col-xs-2">Current Address</label>

            <div class="col-xs-10">

                <?php echo $inaddn;?>
				
            </div>

        </div>

        <div class="form-group">

            <label for="hometown" class="control-label col-xs-2">Home Address</label>

            <div class="col-xs-10">

				<?php echo $inaddh;?>

            </div>

        </div>

        <div class="form-group">

            <div class="col-xs-offset-2 col-xs-10">

                <div class="checkbox">

                    <label><input type="checkbox" required="required"> Agree on Term on Policy</label>
					<?php echo $inkode;?>

                </div>

            </div>

        </div>

        <div class="form-group">

            <div class="col-xs-offset-2 col-xs-10 text-right">

					<?php echo $inres;?>
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