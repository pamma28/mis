<section class="content">	
 	<div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><b>Membership</b> System</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
         <?php if ($this->session->flashdata('v')!=null){ ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="text-center"><b><?=$this->session->flashdata('v');?></div></h5>
          </div>
          <?php } ?>
        
        <div class="col-md-12 bg-info">
        <p class="text-primary"><b>For security reason, please set new password for your account:</b></p>
          <form class="form" action="<?=base_url('Login/savereset');?>" name="resetpassword" method="POST">
            <div class="form-group">
              <div class="input-group">
              <input name="fnewpass" type="password" class="form-control" id="newpass" placeholder="New Password" required="required"><span class="input-group-addon"><a class="togglepassword" type="button"><i class="fa fa-eye text-primary"></i></a></span>
            </div>
            </div>
            <div class="form-group">
              <input name="frepass" type="password" class="form-control" id="retypepass" placeholder="Re-type Password" required="required">
            </div>
            <div class="form-group text-right">
              <input type="hidden" name="fnim" value="<?=$nim;?>">
              <input type="hidden" name="frstcode" value="<?=$rstcode;?>">
              <input type="submit" class="btn btn-primary" value="Update Password">
            </div>
          </form>
        </div>
       
        <br class="clearfix" />
        <p class="login-box-msg bg-warning"><i>if you skip this, your password is your NIM.</i></p>
      </div><!-- /.login-box-body -->
  </div><!-- /.login-box -->
	
	<script>
    $('#newpass').on('change', function(e) {
        var newpass = $("input[name='fnewpass']").val(), oldpass = $("input[name='fnim']").val();
        if (newpass==oldpass){
          alert("Your new password is the same as old password");
          $(this).val('');
          }
      });
      $('#retypepass').on('change', function(e) {
        var newpass2 = $("input[name='frepass']").val(), newpass = $("input[name='fnewpass']").val();
        if (newpass2!=newpass){
          alert("Your confirmation of new password is not match");
          $(this).val('');
          }
      });
    $(document).ready(function(){
      $('.togglepassword').on('click', function(){
        $(this).find("i.fa").toggleClass('fa-eye fa-eye-slash');
        var type    = ($(this).find("i.fa").hasClass('fa-eye-slash') ? 'text' : 'password');
        var input   = '#'+$(this).parent().parent().find(".form-control").attr("id");
        var replace = $(input).clone().attr('type', type);
        $(input).replaceWith(replace);
      });
      
    });
      
  </script>

</section>
