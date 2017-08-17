<!-- Top Navbar -->
          
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="dropdown message-menu">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
					<i class="fa fa-language"></i> <?php echo lang('Language');?> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
				</ul>
			  </li>
              <li class="dropdown user user-menu">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
					<i class="fa fa-language"></i> <?php echo lang('Language');?> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
				<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
				</ul>
			  </li>
              <!-- User Account: style can be found in dropdown.less -->
			  <li class="dropdown user user-menu">
               <a href="<?php echo base_url('Accesscontrol'); ?>"><span class="fa fa-laptop">  <?php echo lang('Dash');?></span></a>
              </li>
			  <li class="dropdown user user-menu">
               <a href="<?php echo base_url('Login/logout'); ?>"><span class="fa fa-power-off"> <?php echo lang('Logout');?></span></a>
              </li>
            </ul>
          </div>