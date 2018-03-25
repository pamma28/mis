<!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo base_url('upload/foto').'/'.$this->session->userdata('photo');?>" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $this->session->userdata('name');?></p>

              <a href="<?=base_url('Member/Setting');?>"><i class="fa fa-gears text-info"> <?=lang('account');?></i></a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header"><span class="pull-right"><a class="sidebar-toggle" role="button" data-toggle="offcanvas" href="#"><i><sup><?=lang('close');?></sup></i><i class="fa fa-close pull-right"></i></a></span><?php echo lang('MenuNav');?></li>
            <li class="treeview">
              <a href="<?=base_url('Member/Dashboard');?>">
                <i class="fa fa-laptop"></i> <span><?php echo lang('Dash');?></span></i>
              </a>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-file-text"></i>
                <span><?php echo lang('mempds');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url('Member/Managepds/');?>"><i class="fa fa-file-text fa-fw"></i> <?php echo lang('mypds');?></a></li>
                <li><a href="<?=base_url('Member/Managepds/editpds');?>"><i class="fa fa-edit fa-fw"></i> <?php echo lang('editmypds');?></a></li>
              </ul>
      </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-money"></i>
                <span><?php echo lang('mempay');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			   <ul class="treeview-menu">
                <li><a href="<?=base_url('Member/Confirmpay');?>"><i class="fa fa-list-alt fa-fw"></i> <?=lang('mempayproc');?></a></li>
                <li><a href="<?=base_url('Member/Confirmpay/requestvalidation');?>"><i class="fa fa-send fa-fw"></i> <?=lang('memsendpay');?></a></li>
                <li><a href="<?=base_url('Member/Confirmpay/validationresult');?>"><i class="fa fa-info-circle fa-fw"></i> <?=lang('mempayvalid');?></a></li>
                <li><a href="<?=base_url('Member/Confirmpay/payment');?>"><i class="fa fa-money fa-fw"></i> <?=lang('memmypay');?></a></li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-book"></i>
                <span><?php echo lang('memschetest');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li>
                  <a href="#"><i class="fa fa-calendar-o fa-fw"></i>  <?=lang('memsche');?><i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Member/Scheduletest");?>"><i class="fa fa-calendar-plus-o fa-fw"></i> <?=lang('memchosesche');?></a></li>
                    <li><a href="<?=base_url("Member/Scheduletest/myschedule");?>"><i class="fa fa-calendar-check-o fa-fw"></i> <?=lang('memmysche');?></a></li>
                  </ul>
                </li>
			         <li>
                  <a href="#"><i class="fa fa-book fa-fw"></i> <?=lang('memtest');?><i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="<?=base_url("Member/Test");?>"><i class="fa fa-book"></i> <?=lang('memmytest');?></a></li>
                    <li><a href="<?=base_url("Member/Test/dotest");?>"><i class="fa fa-pencil"></i> <?=lang('memdotest');?></a></li>
                   <li><a href="<?=base_url("Member/Test/testresult");?>"><i class="fa fa-wpforms"></i> <?=lang('memtestresult');?></a></li>
                    
                  </ul>
                </li>
              </ul>
            </li>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-graduation-cap"></i>
                <span><?php echo lang('memcerti');?></span>
				<i class="fa fa-angle-left pull-right"></i>
              </a>
			  <ul class="treeview-menu">
                <li><a href="<?=base_url("Member/Certificate");?>"><i class="fa fa-certificate fa-fw"></i> <?=lang('mycerti');?></a></li>
                <li><a href="<?=base_url("Member/Certificate/preview");?>"><i class="fa fa-image fa-fw"></i> <?=lang('prevcerti');?></a></li>
               </ul>
      </li>
			
			<li class="treeview">
              <a href="<?=base_url('Member/Setting');?>">
                <i class="fa fa-wrench fa-fw"></i>
                <span><?php echo lang('memsetacc');?></span>
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