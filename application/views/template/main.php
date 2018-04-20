<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="description" content="<?php print(webDescription());?>">
    <meta name="keywords" content="<?php print(webTag());?>">
    <meta name="author" content="Yunas Pamatda">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <title><?php (isset($title)) ? print($title).' - ':null; print(webTitle());?></title>
    <link rel="shortcut icon" href="<?=base_url();?>/assets/images/favicon.ico" type="image/x-icon" />
	<!-- Bootstrap 3.3.2 -->
    <link href="<?php echo base_url('assets'); ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="<?php echo base_url('assets'); ?>/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo base_url('assets'); ?>/css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url('assets'); ?>/css/skin-blue.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	
	<?php 
	if(isset($cssFiles)){
    foreach($cssFiles as $css) {
        echo "<link href='".base_url()."assets/css/" . $css.".css' rel='stylesheet' type='text/css' />";
		}
	} 
	?>
	
	
	
	<!-- jQuery 2.1.3 -->
    <script src="<?php echo base_url('assets'); ?>/js/jQuery-2.1.3.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo base_url('assets'); ?>/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- SlimScroll -->

	<!-- Javascript Load -->
	<?php 
	if(isset($jsFiles)){
    foreach($jsFiles as $js) {
        echo "<script src='".base_url()."assets/js/" . $js.".js' type='text/javascript' /></script>";
		}
	} 
	?>

  <script type="text/javascript">
    $(document).ready(function(){
        (window.matchMedia('(min-width: 767px)').matches) ? $("#buttonshowsidebar").removeClass('fa-cogs').addClass('fa-times'):null;
        
    });
  </script>
  </head>
  <body class="skin-blue wysihtml5-supported pace-done fixed">
	
	
    <script src="<?php echo base_url('assets'); ?>/js/jquery.slimScroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='<?php echo base_url('assets'); ?>/js/fastclick.min.js' type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url('assets'); ?>/js/app.min.js" type="text/javascript"></script>

    <!-- Site wrapper -->
    <div class="wrapper">
      
      <header class="main-header">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="fa fa-cogs fa-lg" id="buttonshowsidebar"></span>
    <span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>
		<a href="<?php echo base_url(); ?>" class="logo"><img src="<?=webLogo();?>" width="50px" class="img-thumbnail" align="middle" style="vertical-align: middle;" alt="<?=webTitle();?>"> <?=webTitle();?></a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
		  

          <?php echo $topbar; ?>

		  </nav>
      </header>

      <!-- =============================================== -->

      <!-- Left side column. contains the sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          
		  <?php echo $sidebar; ?> 
		  
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- =============================================== -->

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

		<?php echo $content; ?>
		<!-- /.content -->
      </div><!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          Page rendered in <strong>{elapsed_time}</strong> seconds. | <b>Version</b> 1.0 Alpha
        </div>
        <strong>Copyright &copy; 2018 <a href="http://sefunsoed.org">SEF Unsoed</a>.</strong> All rights reserved.
      </footer>
    </div><!-- ./wrapper -->

    
	
  </body>
</html>