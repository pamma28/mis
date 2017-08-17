<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Payment<small>Procedure</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
	              <div class="info-box">
	                <span class="info-box-icon bg-teal"><i class="fa fa-money"></i></span>
	                <div class="info-box-content">
	                  <span class="info-box-text">Current Price</span>
	                  <span class="info-box-number"><b>Rp. <?=$price;?></b></span>
	                </div><!-- /.info-box-content -->
	              </div><!-- /.info-box -->
	            </div>
	            <div class="col-md-6 col-sm-6 col-xs-12">
	              <div class="info-box">
	                <span class="info-box-icon bg-green"><i class="fa fa-barcode"></i></span>
	                <div class="info-box-content">
	                  <span class="info-box-text">Unique Code</span>
	                  <span class="info-box-number"><b><i><?=$code;?></i></b></span>
	                </div><!-- /.info-box-content -->
	              </div><!-- /.info-box -->
	            </div>
	</div>
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-12">
	        <div class="info-box">
	    	    <span class="info-box-icon bg-light-blue"><i class="fa fa-info-circle"></i></span>
	        	    <div class="info-box-content">
	                  <span class="info-box-text">Total Price</span>
	                  <span class="info-box-number"><b class="text-danger"><h3>Rp. <?=($price+$code);?></h3></b></span>
	                </div><!-- /.info-box-content -->
	        </div><!-- /.info-box -->
	    </div>
	    <div class="col-md-6 col-sm-6 col-xs-12">
	        <div class="info-box">
	    	    <span class="info-box-icon bg-navy"><i class="fa fa-bank"></i></span>
	        	    <div class="info-box-content">
	                  <span class="info-box-text">Transfer To</span>
	                  <span class="info-box-number"><b class="text-primary"><h4><?=$bname;?> Bank, No. <?=$accno;?> on the behalf of "<?=$accname;?>"</h4></b></span>
	                </div><!-- /.info-box-content -->
	        </div><!-- /.info-box -->
	    </div>
	</div>

	<div class="box box-solid box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
				<div class="panel panel-default">
				<div class="panel-body">
					<h4 class="text-center "><b class="text-primary">
					<?=$tmptitle;?></b></h4>
					<hr class="divider"/>
					<?=$tmp;?>
				</div>
				</div>
				</div>
			</div>

			
		
		</div>
	</div>
		
</section>		