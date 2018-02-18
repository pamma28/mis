<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Payment<small>Procedure</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box box-solid box-default">
		<div class="box-body">
			<div class="row">
				<div class="col-md-6 col-sm-12">
					<div class="panel panel-info">
					<div class="panel-body">
						<h4 class="text-center "><b class="text-primary">
						<?=$tmptitle;?></b></h4>
						<hr class="divider"/>
						<?=$tmp;?>
					</div>
					</div>
				</div>

				<div class="col-md-6 col-sm-12">
					<div class="panel panel-default">
					<div class="panel-body">
						<h4 class="text-center "><b class="text-primary">
						Total Payment (Transfer)</b></h4>
						<hr class="divider"/>
						<div class="table-responsive bg-info">
								<table class="table table-condesed">
									<tbody><tr>
						                <th style="width:50%">Details</th>
						                <th>Amount</th>
						              </tr><tr>
						                <td>Price:</td>
						                <td><?=$price;?></td>
						              </tr>
						              <tr>
						                <td>Unique Code</td>
						                <td><?=$code;?></td>
						              </tr>
						              <tr>
						                <th><h4><b>Total:</b></h4></th>
						                <th><h4><b><?=$totpay;?></b></h4></th>
						              </tr>
						            </tbody>
								</table>
						</div>
					</div>
					</div>
					<div class="panel panel-default">
					<div class="panel-body">
						<div class="col-md-12">
									
									<h4 class="text-primary text-center"><b>Payment Channel</b></h4>
									<hr/>
									<?=$paychannel;?>
								</div>			
					</div>
					</div>
				</div>
			</div>

			
		
		</div>
	</div>
		
</section>		