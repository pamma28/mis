<section class="content">
	
  <?php if ($this->session->flashdata('rdr')!=null){ ?>
      <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <div class="text-center"><b><?=$this->session->flashdata('rdr');?></div></h5>
      </div>
      <?php } ?>

	<div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><b>Membership</b> System</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Please sign in</p>
	
    <?php echo form_open('Login/auth',array('name'=>'login', 'method'=>'POST'));?>
          <div class="form-group has-feedback">
            <?php echo $inuser;?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <?php echo $inpass;?>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
			<?php
			if ((validation_errors()) or ($this->session->flashdata("error")!=null)){?>
			<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo "Login failed".validation_errors()."</div>";} ?>
			
          <div class="row">
            <div class="col-xs-8">    
             
                  <input type="checkbox" name="remember" class="checkbox icheck">   Remember Me
			
            </div><!-- /.col -->
            <div class="col-xs-4">
              <?=$rdr;?>
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div><!-- /.col -->
          </div>
        <?php form_close(); ?>

      

        <a href="#">I forgot my password</a><br>
        

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
	
	<script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_flat-blue',
          radioClass: 'iradio_flat-blue',
          increaseArea: '0%' // optional
        });
      });
    </script>

</section>
