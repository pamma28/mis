<!-- Top Navbar -->
          
	 <ul class="nav navbar-nav">
		
		  <li class="dropdown language-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-language fa-fw fa-lg"></i></a>
				<ul class="dropdown-menu">
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
				</ul>
		  </li>
		  <li class="dropdown notifications-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false" id="mybuttonnotiflist"><i class="fa fa-globe fa-fw fa-lg"></i> <span class="label label-danger labeltotmynotif"><?php (getmynotif()!='0') ? print(getmynotif()) : null ;?></span></a>
				<ul class="dropdown-menu">
	              <li>
	                <!-- inner menu: contains the actual data -->
	                <ul class="menu" id="myrecentnotiflist">
	                 
	                </ul>
	              </li>
	              <li class="footer"><a href="<?=linkAllNotif();?>"><b><?=lang('viewall');?></b></a></li>
				</ul>
		  </li>
	 </ul>
	
	  <div class="mynavbar-wrapper">
		<ul class="nav nav-tabs mynavbar-list" id="myTab">
		  <li><a href="<?=base_url('Accesscontrol');?>"><i class="fa fa-laptop"></i> <span><?php echo lang('Dash');?></span></a></li>
		  <li><a href="<?=base_url('Home/articles');?>"><i class="fa fa-newspaper-o"></i><span> <?php echo lang('Articles');?></span></a></li>
		  <li><a href="<?=base_url('Home/agendas');?>"><i class="fa fa-calendar"></i><span> <?php echo lang('Agendas');?></span></a></li>
		  <li><a href="<?=base_url('Home/about');?>"><i class="fa fa-info"></i><span> <?php echo lang('About');?></span></a></li>
		  <li><a href="#" data-toggle="modal" data-target="#LogoutModal" ><i class="fa fa-power-off"></i> <?php echo lang('Logout');?></a></li>
	  </ul>
	  </div>

	 	<!-- Modal Details Data-->
		<div class="modal fade" id="LoggedModal" tabindex="-1" role="dialog" aria-labelledby="myLoggedLabel" aria-hidden="true">
	    	<div class="modal-dialog">
	        <div class="modal-content">
	        	<div class="modal-header text-center text-yellow"><h3><b> <span class="fa fa-exclamation-triangle"></span>  Warning</b></h3></div>
	        	<div class="modal-body text-center">
				<h4 class="text-danger"><b>Your login session has expired, please login again.</b></h4>
				</div>
	        </div>
	    	</div>
		</div>
	

		<!-- Modal Logout-->
		<div class="modal fade" id="LogoutModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog modal-sm">
	        <div class="modal-content">
				<div class="modal-header text-center text-aqua"><h4><b> <span class="fa fa-info-circle"></span>  Do you really want to log out?</b></h4></div>
				<div class="modal-footer text-right">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
					<a href="<?php echo base_url('Login/logout'); ?>" class="btn btn-sm btn-danger"><i class="fa fa-power-off"></i> <?php echo lang('Logout');?></a></div>
	        </div>
	    </div>
		</div>
	  <script>
	  $(document).ready(function () {
	  	
		  $(".mynavbar-wrapper").animate({scrollLeft: 350}, 700,'linear',function(){
			  $(".mynavbar-wrapper").animate({scrollLeft: -350}, 400);
			});
		  
		  $('#mybuttonnotiflist').click(function(e){
		  $.post('<?php echo base_url('Accesscontrol/getmynotif'); ?>', {}, function(d) {
								if (d != '' )
								{
									$('#myrecentnotiflist').empty().html(d);
									$('.labeltotmynotif').empty().html();
								}
							});
		  });
	  		$(window).on('focus', function() {
	  			var urlrdr = "<?=base_url('Login?rdr=');?>"+encodeURIComponent(window.location.pathname);
	  			$.post('<?php echo base_url('Login/checkloggedin'); ?>',{},function(d){
	  				if(d=='0'){
	  					$("#LoggedModal").modal("show");
	  					$("#LoggedModal").on("hide.bs.modal",function(){
	  						window.location.href=urlrdr;
	  					});
	  				}
	  			});
	  		});
	  });


	  </script>
	  
	