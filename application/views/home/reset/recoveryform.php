<section class="content"> 
  <div class="login-box">
      <div class="login-logo">
        <a href="<?php echo base_url(); ?>"><b>Membership</b> System</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
         <?php if ($this->session->flashdata('x')!=null){ ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="text-center"><b><?=$this->session->flashdata('x');?></div></h5>
          </div>
          <?php } ?>
        <div class="col-md-12">
        <p class="text-primary"><b>Please type your new password:</b></p>
          <form class="form" action="<?=base_url('Login/savereset');?>" name="resetpassword" method="POST">
            <div class="form-group">
              <div class="input-group">
              <?=$fnewpass;?>
              <span class="input-group-addon"><a class="togglepassword" type="button"><i class="fa fa-eye text-primary"></i></a></span>
            </div>
            </div>
            <div class="form-group">
              <?=$frepass;?>
            </div>
            <div class="form-group text-right">
              <?=$frstcode;?>
              <input type="submit" class="btn btn-primary" value="Update Password">
            </div>
          </form>
        </div>
        <br class="clearfix" />
      </div><!-- /.login-box-body -->
  </div><!-- /.login-box -->
  
  <script>
    $(document).ready(function(){
      $('#repass').on('change', function(e) {
        var newpass2 = $("input[name='frepass']").val(), newpass = $("input[name='fnewpass']").val();
        if (newpass2!=newpass){
          alert("Your confirmation of new password is not match");
          $(this).val('');
          }
      });
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
