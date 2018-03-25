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
        <p class="text-danger text-center"><b>Your email can not be validated, <br/>please register again to this <a href="<?=base_url('Register');?>" class="text-primary" alt="Register">link </a></b></p>
      </div>
        <div class="col-md-12">
          
        </div>
        <small class=" text-info"><i>Please confirm your email immediately after you register again.</i></small>
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
