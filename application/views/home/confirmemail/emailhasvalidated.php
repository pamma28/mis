<section class="content">
	
  <?php if ($this->session->flashdata('x')!=null){ ?>
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <div class="text-center"><b><?=$this->session->flashdata('x');?></div></h5>
      </div>
      <?php } ?>

	<div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><b>Membership</b> System</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <div class="bg-danger">
        <div class="text-danger text-center"><b>Your email has been validated, <br/>please login to this <a href="<?=base_url('Login');?>" class="text-primary" alt="Register">link </a></b></div>
        </div>
      <div class="col-md-12">
        <small><i>If you forget your login credentials, please do reset your account on this <a href="<?=base_url("Login/Reset")?>" alt="reset" class="text-primary">link </a>.</i></small>
      </div>
      <br class="clearfix"/>
      </div><!-- /.login-box-body -->
  </div><!-- /.login-box -->
	
	 <script>
     
    </script>

</section>
