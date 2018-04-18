<!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url('upload/foto').'/'.$this->session->userdata('photo');?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->session->userdata('name');?></p>

              <a href="<?=base_url('Admin/Setting');?>"><i class="fa fa-gears text-info"> <?=lang('account');?></i></a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"><span class="pull-right"><a class="sidebar-toggle" role="button" data-toggle="offcanvas" href="#"><i><sup><?=lang('close');?></sup></i><i class="fa fa-close pull-right"></i></a></span><?php echo lang('MenuNav');?></li>
            <li class="treeview">
              <a href="<?=base_url('Admin/Dashboard');?>">
                <i class="fa fa-laptop"></i> <span><?php echo lang('Dash');?></span></i>
              </a>
            </li>
            <li class="treeview">

              <a href="#">
                <i class="fa fa-users"></i>
                <span><?php echo lang('manageacc');?></span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
               <ul class="treeview-menu">
                <li>
                  <a href="<?=base_url('Admin/Managelogin');?>">
                    <i class="fa fa-users"></i> <span><?php echo lang('manageacc');?></span></i>
                  </a>
                </li>
                <li>
                 <li><a href="<?=base_url("Admin/Role");?>"><i class="fa fa-sitemap fa-fw"></i> <?php echo lang('masterrole');?></a></li>
                </li>
              </ul>
            </li>
			<li class="treeview">
              <a href="<?=base_url('Admin/Viewpds');?>">
                <i class="fa fa-file-text"></i>
                <span><?php echo lang('registdata');?></span>
              </a>
            </li>
            <li class="treeview">
              <a href="<?=base_url('Admin/Viewpay');?>">
                <i class="fa fa-money"></i>
                <span><?php echo lang('payment');?></span>
              </a>
            </li>
			<li class="treeview">
              <a href="<?=base_url('Admin/Impexp');?>">
                <i class="fa fa-database"></i>
                <span><?php echo lang('database');?></span>
              </a>
      </li>
      <li class="treeview">
              <a href="<?=base_url('Admin/Setting');?>">
                <i class="fa fa-database"></i>
                <span><?php echo lang('setting');?></span>
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
        var url = window.location;
        $('.treeview a[href="'+ url +'"]').parent().addClass('active');
        $('.treeview-menu li a').filter(function() {
             return this.href == url;
        }).parent().parent().parent().addClass('active');
        $('.treeview-menu li a').filter(function() {
             return this.href == url;
        }).parent().addClass('active');
    });


</script> 