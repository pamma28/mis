<!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url('upload/foto').'/'.$this->session->userdata('photo');?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->session->userdata('name');?></p>

              <a href="<?=base_url('Organizer/Setting/account');?>"><i class="fa fa-gears text-info"> <?=lang('account');?></i></a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">
              <?php echo lang('MenuNav');?></li>
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
                <li><a href="<?=base_url('Organizer/PDS/addpds');?>"><i class="fa fa-plus fa-fw"></i>  <?php echo lang("add").lang('registdata');?></a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-money"></i>
                <span><?php echo lang('payment');?></span>
				<i class="fa fa-angle-left pull-right"></i><?=labeltottransfer();?>
              </a>
			   <ul class="treeview-menu">
                <li><?=labeltottransfer();?><a href="<?=base_url('Organizer/Transfer');?>"><i class="fa fa-credit-card fa-fw"></i> <?php echo lang('tfvalid');?></a></li>
                <li><a href="<?=base_url('Organizer/Payment');?>"><i class="fa fa-calculator fa-fw"></i> <?php echo lang('paydata');?></a></li>
                <li><a href="<?=base_url('Organizer/Payment/addpay');?>"><i class="fa fa-plus fa-fw"></i> <?php echo lang('paycashier');?></a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i>
                <span><?php echo lang('managetest');?></span>
				<i class="fa fa-angle-left pull-right"></i><?=labeltotresulttest();?><?=labeltotalactivetest();?>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Organizer/Test");?>"><i class="fa fa-edit fa-fw"></i> <?php echo lang('tname');?></a></li>
                <li>
                  <a href="#"><i class="fa fa-list fa-fw"></i> <?php echo lang('tsubj');?><i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Subject");?>"><i class="fa fa-circle-o"></i> <?php echo lang('tsubjlist');?></a></li>
                    <li><a href="<?=base_url("Organizer/Subject/editsubjecttest");?>"><i class="fa fa-circle-o"></i> <?php echo lang('tsubjedit');?></a></li>
                    <li><a href="<?=base_url("Organizer/Subject/allsubject");?>"><i class="fa fa-circle-o"></i> <?php echo lang('tsubjall');?></a></li>
                  </ul>
                </li>
				<li>
                  <a href="#"><i class="fa fa-question fa-fw"></i> <?php echo lang('tquest');?><i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Question");?>"><i class="fa fa-circle-o"></i> <?php echo lang('tsubjquestlist');?></a></li>
					<li><a href="<?=base_url("Organizer/Question/editquestsubject");?>"><i class="fa fa-circle-o"></i> <?php echo lang('tsubjquestedit');?></a></li>
                    <li><a href="<?=base_url("Organizer/Question/questiontype");?>"><i class="fa fa-circle-o"></i> <?php echo lang('qtype');?></a></li>
                    <li><a href="<?=base_url("Organizer/Question/allquestion");?>"><i class="fa fa-circle-o"></i> <?php echo lang('qall');?></a></li>
                  </ul>
                </li>
                <li><a href="<?=base_url("Organizer/Schedule");?>"><i class="fa fa-calendar fa-fw"></i> <?php echo lang('tsche');?></a></li>
                <li><?=labeltotalactivetest();?><a href="<?=base_url("Organizer/Schedule/active");?>"><i class="fa fa-check-square-o fa-fw"></i> <?php echo lang('tactive');?></a></li>
                <li>
                  <a href="#"><i class="fa fa-legal fa-fw"></i> <?php echo lang('tresult');?><i class="fa fa-angle-left pull-right"></i><?=labeltotresulttest();?></a>
                  <ul class="treeview-menu">
                    <li><?=labeltotresulttest();?><a href="<?=base_url("Organizer/Result");?>"><i class="fa fa-circle-o fa-fw"></i> <?php echo lang('tresult');?></a></li>
                    <li><a href="<?=base_url("Organizer/Result/assessresult");?>"><i class="fa fa-circle-o fa-fw"></i> <?php echo lang('tassess');?></a></li>
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
                <li><a href="<?=base_url("Organizer/Certificate");?>"><i class="fa fa-certificate fa-fw"></i> <?php echo lang('certidata');?></a></li>
                <li><a href="<?=base_url("Organizer/Design");?>"><i class="fa fa-image fa-fw"></i> <?php echo lang('certidesign');?></a></li>
                <li><a href="<?=base_url("Organizer/Certificate/preview");?>"><i class="fa fa-file-image-o fa-fw"></i> <?php echo lang('certiprev');?></a></li>
               </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-bullhorn"></i>
                <span><?php echo lang('bc');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-envelope-o fa-fw"></i> <?php echo lang('bcmail');?> <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Mailbroadcast");?>"><i class="fa fa-circle-o"></i> <?php echo lang('bcmaillist');?></a></li>
					<li><a href="<?=base_url("Organizer/Mailbroadcast/composemail");?>"><i class="fa fa-circle-o"></i> <?php echo lang('bcmailcom');?></a></li>
                </ul>
				</li>
                <li><a href="#"><i class="fa fa-envelope-square fa-fw"></i> <?php echo lang('bcsms');?><i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/SmsBroadcast");?>"><i class="fa fa-circle-o"></i> <?php echo lang('bcsmslist');?></a></li>
					<li><a href="<?=base_url("Organizer/SmsBroadcast/composesms");?>"><i class="fa fa-circle-o"></i> <?php echo lang('bcsmscom');?></a></li>
                    <li><a href="<?=base_url("Organizer/SmsBroadcast/template");?>"><i class="fa fa-circle-o"></i> <?php echo lang('bcsmstmp');?></a></li>
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
                <li><a href="<?=base_url("Organizer/Template");?>"><i class="fa fa-pencil-square fa-fw"></i> <?php echo lang('contenttmp');?></a></li>
                <li><a href="#"><i class="fa fa-newspaper-o fa-fw"></i> <?php echo lang('contentatcl');?><i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
                    <li><a href="<?=base_url("Organizer/Article");?>"><i class="fa fa-circle-o"></i> <?php echo lang('contentatcllist');?></a></li>
					<li><a href="<?=base_url("Organizer/Article/categorylist");?>"><i class="fa fa-circle-o"></i> <?php echo lang('contentatclcat');?></a></li>
                  </ul>
				</li>
                <li><a href="<?=base_url("Organizer/Agenda");?>"><i class="fa fa-calendar-check-o fa-fw"></i> <?php echo lang('contentagn');?></a></li>
                <li><a href="<?=base_url("Organizer/Notification");?>"><i class="fa fa-bell fa-fw"></i> <?php echo lang('contentnotif');?></a></li>
               </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-folder"></i>
                <span><?php echo lang('master');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Organizer/Faculty");?>"><i class="fa fa-building-o fa-fw"></i> <?php echo lang('masterfac');?></a></li>
                <li><a href="<?=base_url("Organizer/Level");?>"><i class="fa fa-line-chart fa-fw"></i> <?php echo lang('masterlvl');?></a></li>
                <li><a href="<?=base_url("Organizer/Gender");?>"><i class="fa fa-intersex fa-fw"></i> <?php echo lang('mastergen');?></a></li>
                <li><a href="<?=base_url("Organizer/Role");?>"><i class="fa fa-sitemap fa-fw"></i> <?php echo lang('masterrole');?></a></li>
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