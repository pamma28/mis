<!-- Top Navbar -->
            
            <ul class="nav navbar-nav">
            	<li class="dropdown language-menu"><a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-language fa-fw fa-lg"></i></a>
					<ul class="dropdown-menu">
					<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/english?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/en.png');?>" alt="English" height="20px"> En/USA</a> </li>
					<li><a href='<?php echo base_url(); ?>LangSwitch/switchLanguage/indonesian?url=<?php echo current_url();?>'><img src="<?php echo base_url('assets/images/id.png');?>" alt="Indonesia" height="20px"> ID/Indonesia</a></li>
					</ul>
		  		</li>
             
             </ul>
             <div class="mynavbar-wrapper">
				<ul class="nav nav-tabs mynavbar-list" id="myTab">
				  <li class="dropdown user user-menu"><a href="<?php echo base_url('register'); ?>"><span class="fa fa-edit"> <?php echo lang('Register');?></span></a></li>
	              <li>
	                
	                  <?php if (null!==($this->session->userdata('role'))) {?>
					  <a href="<?php echo base_url('Accesscontrol'); ?>"><span class="fa fa-laptop"> <?php echo lang('Dash');?></span>
					  <?php } else {?>
					  <a href="<?php echo base_url('login'); ?>"><span class="fa fa-key"> <?php echo lang('Login');?></span>
					  <?php } ?>
					</a>
	              </li>
	            </ul>
	         </div>