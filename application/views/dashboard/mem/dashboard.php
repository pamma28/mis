<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Dashboard<small>Member</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-solid box-primary">
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">
				<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title text-center "><b class="text-primary">Membership Progress</b></h4>
				</div>
				<div class="panel-body">
				<div class="col-md-12">
					<ul class="timeline timeline-horizontal" style="width: 100%;overflow-y: auto;">
						<?=$arrprogress;?>
					</ul>
				</div>
				</div>
				</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
				<div class="box box-primary">
				<div class="box-body">
					<?=$tmpcontent;?>
				</div>
				</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-info">
						<div class="panel-body">
						<h4 class="text-center text-info"><b>Important Dates</b></h4>
						<hr/>
						<?=$dtphase;?>
						</div>	
					</div>
				</div>
			</div>
			<div class="row">
            	<div class="col-md-6" >
            	<div class="panel panel-default">
            		<div class="panel-body">             
					<h4 class="text-center text-info"><b>Personal Data Sheet</b></h4>
					<hr/>
						
							<?=$dtpds;?><!-- /.item -->
						
					</div>
					<div class="panel-footer text-right">
						<a class="btn btn-primary" href="<?=base_url('Member/ManagePDS');?>" alt="Manage PDS"><span class="fa fa-arrow-right"></span> Detail PDS</a>
					</div>
				</div>
           		 </div><!-- /.col -->

				<div class="col-md-6">
					<div class="panel panel-default">
					<div class="panel-body">
					<h4 class="text-center text-info"><b>Payment List</b></h4>
					<hr/>
						<?=$dtpay;?>
						<div class="row bg-success">
							<div class="col-md-6">
								<h4><b>Status: <span class="text-primary"><?=$lunas?></span></b></h4>
							</div>
							<div class="col-md-6">
								<h4 class="text-right"><b>Total Paid: <span class="text-primary"><?=$totpay?>,-</span></b></h4>
							</div>
						</div>
             		</div>
             		<div class="panel-footer text-right">
						<a class="btn btn-primary" href="<?=base_url('Member/Confirmpay/payment');?>" alt="My Payment"><span class="fa fa-arrow-right"></span> Detail Payment</a>
					</div>
             		</div>
					
				</div>
          	</div>
		
		</div>
		</div>
	</div>
		
</section>		