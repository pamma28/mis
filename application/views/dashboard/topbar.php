<!-- Top Navbar -->
          
	 <ul class="nav navbar-nav">
		<li class="dropdown notifications-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-globe fa-fw fa-lg"></i> <span class="label label-danger"><?php (getmynotif()!='0') ? print(getmynotif()) : null ;?></span></a>
				<ul class="dropdown-menu">
	              <li class="header">You have <b><?=getmynotif();?></b> new notifications</li>
	              <li>
	                <!-- inner menu: contains the actual data -->
	                <ul class="menu">
	                  <li>
	                    <a href="#" class="notread">
	                      <p> 5 new members joined today</p>
	                      <sup class="text-left"><span class="fa fa-users text-aqua"></span> <i><b>two hours ago</b></i></sup>
	                      
	                    </a>
	                  </li>
	                  <li>
	                    <a href="#">
	                      <i class="fa fa-warning text-yellow"></i> Very long description here that may not fit into the
	                      page and may cause design problems
	                    </a>
	                  </li>
	                  <li>
	                    <a href="#">
	                      <i class="fa fa-users text-red"></i> 5 new members joined
	                    </a>
	                  </li>
	                  <li>
	                    <a href="#">
	                      <i class="fa fa-shopping-cart text-green"></i> 25 sales made
	                    </a>
	                  </li>
	                  <li>
	                    <a href="#">
	                      <i class="fa fa-user text-red"></i> You changed your username
	                    </a>
	                  </li>
	                </ul>
	              </li>
	              <li class="footer"><a href="#">View all</a></li>
				</ul>
		  </li>
		  <li class="dropdown language-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-language fa-fw fa-lg"></i></a>
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
	  
	