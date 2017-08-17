<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title><?php if (isset($title)){echo $title.' - ';}?>SEF Membership</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
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
	
	
	
	
	
	<!-- jQuery 2.1.3 -->
    <script src="<?php echo base_url('assets'); ?>/js/jQuery-2.1.3.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo base_url('assets'); ?>/js/bootstrap.min.js" type="text/javascript"></script>
    
	
  </head>
  <body>
	
    

		<div class="box box-solid box-border box-default">
			<div class="box-body">
			<div class="text-center text-success">
				<i class="fa fa-book fa-4x"></i>
				<i class="fa fa-check fa-4x"></i>
			</div>
				<div class="alert alert-success alert-dismissible text-center" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					Update last synchronization (<?=date("m-d-Y");?>) success.
				</div>
			</div>
		</div>
	
  </body>
</html>