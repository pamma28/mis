<!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url('upload/foto').'/'.$this->session->userdata('photo');?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->session->userdata('name');?></p>

              <a href="#"><i class="fa fa-gears text-info"> <?=lang('account');?></i></a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"><span class="pull-right"><a class="sidebar-toggle" role="button" data-toggle="offcanvas" href="#"><i><sup><?=lang('close');?></sup></i><i class="fa fa-close pull-right"></i></a></span><?php echo lang('MenuNav');?></li>
            <li class="treeview">
              <a href="<?=base_url('Organizer/Dashboard');?>">
                <i class="fa fa-laptop"></i> <span><?php echo lang('Dash');?></span></i>
              </a>
            </li>
            <li class="treeview">
              <a href="<?=base_url('Organizer/Memberaccount');?>">
                <i class="fa fa-users"></i>
                <span><?php echo lang('memberacc');?></span>
              </a>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-file-text"></i>
                <span><?php echo lang('regist');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url('Organizer/PDS/');?>"><i class="fa fa-file-text fa-fw"></i> <?php echo lang('registdata');?></a></li>
                <li><a href="<?=base_url('Organizer/PDS/addpds');?>"><i class="fa fa-plus fa-fw"></i> Add <?php echo lang('registdata');?></a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-money"></i>
                <span><?php echo lang('payment');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			   <ul class="treeview-menu">
                <li><span class="label label-warning pull-right">4</span><a href="<?=base_url('Organizer/Transfer');?>"><i class="fa fa-credit-card fa-fw"></i> Transfer Validation</a></li>
                <li><span class="label label-info pull-right">10</span><a href="<?=base_url('Organizer/Payment');?>"><i class="fa fa-calculator fa-fw"></i> Payment Data</a></li>
                <li><a href="<?=base_url('Organizer/Payment/addpay');?>"><i class="fa fa-plus fa-fw"></i> Payment Cashier</a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i>
                <span><?php echo lang('managetest');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Organizer/Test");?>"><i class="fa fa-edit fa-fw"></i> Test Name</a></li>
                <li>
                  <a href="#"><i class="fa fa-list fa-fw"></i> Test Subject <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Subject");?>"><i class="fa fa-circle-o"></i> Subject-Test List</a></li>
                    <li><a href="<?=base_url("Organizer/Subject/editsubjecttest");?>"><i class="fa fa-circle-o"></i> Edit Subject Test</a></li>
                    <li><a href="<?=base_url("Organizer/Subject/allsubject");?>"><i class="fa fa-circle-o"></i> All Subject</a></li>
                  </ul>
                </li>
				<li>
                  <a href="#"><i class="fa fa-question fa-fw"></i> Test Questions <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Question");?>"><i class="fa fa-circle-o"></i> Subject-Question List</a></li>
					<li><a href="<?=base_url("Organizer/Question/editquestsubject");?>"><i class="fa fa-circle-o"></i> Edit Question List</a></li>
                    <li><a href="<?=base_url("Organizer/Question/questiontype");?>"><i class="fa fa-circle-o"></i> Question Type</a></li>
                    <li><a href="<?=base_url("Organizer/Question/allquestion");?>"><i class="fa fa-circle-o"></i> All Question</a></li>
                  </ul>
                </li>
                <li><a href="<?=base_url("Organizer/Schedule");?>"><i class="fa fa-calendar fa-fw"></i> Test Schedule</a></li>
                <li><span class="label label-info pull-right">12</span><a href="<?=base_url("Organizer/Schedule/active");?>"><i class="fa fa-check-square-o fa-fw"></i> Activate Test</a></li>
                <li>
                  <a href="#"><i class="fa fa-legal fa-fw"></i> Test Result <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><span class="label label-info pull-right">80</span><a href="<?=base_url("Organizer/Result");?>"><i class="fa fa-circle-o fa-fw"></i> Test Result</a></li>
                    <li><a href="<?=base_url("Organizer/Result/assessresult");?>"><i class="fa fa-circle-o fa-fw"></i> Assess Test Result</a></li>
                  </ul>
                </li>
                
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-graduation-cap"></i>
                <span><?php echo lang('certi');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><span class="label label-primary pull-right">4</span><a href="<?=base_url("Organizer/Certificate");?>"><i class="fa fa-certificate fa-fw"></i> Certificate Data</a></li>
                <li><a href="<?=base_url("Organizer/Design");?>"><i class="fa fa-image fa-fw"></i> Certificate Design</a></li>
                <li><a href="<?=base_url("Organizer/Certificate/preview");?>"><i class="fa fa-file-image-o fa-fw"></i> Preview Certificate</a></li>
               </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-bullhorn"></i>
                <span><?php echo lang('bc');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-envelope-o fa-fw"></i> Mail Broadcast <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Mailbroadcast");?>"><i class="fa fa-circle-o"></i> Mail Broadcast List</a></li>
					<li><a href="<?=base_url("Organizer/Mailbroadcast/composemail");?>"><i class="fa fa-circle-o"></i> Compose Mail</a></li>
                </ul>
				</li>
                <li><a href="#"><i class="fa fa-envelope-square fa-fw"></i> SMS Broadcast <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/SmsBroadcast");?>"><i class="fa fa-circle-o"></i> SMS Broadcast List</a></li>
					<li><a href="<?=base_url("Organizer/SmsBroadcast/composesms");?>"><i class="fa fa-circle-o"></i> Send SMS</a></li>
                    <li><a href="<?=base_url("Organizer/SmsBroadcast/template");?>"><i class="fa fa-circle-o"></i> SMS Template</a></li>
                  </ul>
				</li>
               </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-pencil"></i>
                <span><?php echo lang('content');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Organizer/Template");?>"><i class="fa fa-pencil-square fa-fw"></i> Template Content</a></li>
                <li><a href="#"><i class="fa fa-newspaper-o fa-fw"></i> Article <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Article");?>"><i class="fa fa-circle-o"></i> Article List</a></li>
					<li><a href="<?=base_url("Organizer/Article/categorylist");?>"><i class="fa fa-circle-o"></i> Article Category</a></li>
                  </ul>
				</li>
                <li><a href="<?=base_url("Organizer/Agenda");?>"><i class="fa fa-calendar-check-o fa-fw"></i> Agenda</a></li>
                <li><a href="<?=base_url("Organizer/Notification");?>"><i class="fa fa-flag fa-fw"></i> Notifications</a></li>
               </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i>
                <span><?php echo lang('master');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Organizer/Faculty");?>"><i class="fa fa-building-o fa-fw"></i> Faculty Data</a></li>
                <li><a href="<?=base_url("Organizer/Level");?>"><i class="fa fa-line-chart fa-fw"></i> Class Level</a></li>
                <li><a href="<?=base_url("Organizer/Gender");?>"><i class="fa fa-intersex fa-fw"></i> Gender</a></li>
                <li><a href="<?=base_url("Organizer/Role");?>"><i class="fa fa-sitemap fa-fw"></i> Role</a></li>
               </ul>
            </li>
			<li class="treeview">
              <a href="<?=base_url('Organizer/Setting');?>">
                <i class="fa fa-wrench"></i>
                <span><?php echo lang('set_rc');?></span>
              </a>
            </li>
          </ul>
<div id="histats_counter"></div>
<?php /*<!-- Histats.com  START  (aync)-->
<script type="text/javascript">var _Hasync= _Hasync|| [];
_Hasync.push(['Histats.start', '1,3237160,4,336,112,62,00010011']);
_Hasync.push(['Histats.fasi', '1']);
_Hasync.push(['Histats.track_hits', '']);
(function() {
var hs = document.createElement('script'); hs.type = 'text/javascript'; hs.async = true;
hs.src = ('http://s10.histats.com/js15_as.js');
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
})();</script>
<noscript><a href="http://www.histats.com" target="_blank"><img  src="http://sstatic1.histats.com/0.gif?3237160&101" alt="cool hit counter" border="0"></a></noscript>
<!-- Histats.com  END  --> !> */ ?>
		  
<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?=current_url();?>";
        $('.treeview a[href="'+ url +'"]').parent().addClass('active');
        $('.treeview-menu li a').filter(function() {
             return this.href == url;
        }).parent().parent().parent().addClass('active');
		$('.treeview-menu li a').filter(function() {
             return this.href == url;
        }).parent().parent().parent().parent().parent().addClass('active');
        $('.treeview-menu li a').filter(function() {
             return this.href == url;
        }).parent().addClass('active');
    });


</script> 