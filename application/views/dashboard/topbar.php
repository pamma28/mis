<!-- Top Navbar -->
          
	 <ul class="nav nav-tabs pretopmenu">
		<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-globe fa-2x"></i></a>
				<ul class="dropdown-menu">
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
				</ul>
		  </li>
		  <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-language fa-2x"></i></a>
				<ul class="dropdown-menu">
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
				</ul>
		  </li>
	 </ul>
	  
	  <div class="mynavbar-wrapper">
		<ul class="nav nav-tabs mynavbar-list" id="myTab">
		  <li><a href="#"><i class="fa fa-laptop"></i> Dashboard</a></li>
		  <li><a href="#"><i class="fa fa-newspaper-o"></i> Articles</a></li>
		  <li><a href="#"><i class="fa fa-calendar-o"></i> Agendas</a></li>
		  <li><a href="#"><i class="fa fa-info"></i>  About</a></li>
		  <li><a href="<?php echo base_url('Login/logout'); ?>"><i class="fa fa-power-off"></i> <?php echo lang('Logout');?></a></li>
	  </ul>
	  </div>
	  <script>
	  $(document).ready(function () {
	  $(".mynavbar-wrapper").animate({scrollLeft: 350}, 700,'linear',function(){
	  $(".mynavbar-wrapper").animate({scrollLeft: -350}, 400);
	  });
	  });
	  </script>
	  
	