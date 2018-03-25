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
        <p class="login-box-msg">Please enter your email</p>
            <?php
      if (($this->session->flashdata("xreset")!=null)){?>
      <div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php echo $this->session->flashdata('xreset')."</div>";} ?>
	
    <?php echo form_open('Login/reset',array('name'=>'reset', 'method'=>'POST'));?>
          <div class="form-group has-feedback">
            <?php echo $inemail;?>
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-4 col-xs-offset-8">

              <button type="submit" class="btn btn-primary btn-block btn-flat">Reset</button>
            </div><!-- /.col -->
          </div>
        <?php form_close(); ?>

        
        

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
