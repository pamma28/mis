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
        <h4 class="text-success text-center"><b><?=$this->session->flashdata('title');?></b></h4>
        <p class="login-box-msg text-info"><i><?=$this->session->flashdata('info');?></i></p>
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
