<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Dashboard<small>Administrator</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-success">
				<div class="panel-body">
					<h3 class="text-center">SUMMARY OF <?=$thisperiod;?> PERIOD</h3>
				<div class="row">
					<div class="col-lg-3 col-xs-6">
						  <!-- small box -->
						  <div class="small-box bg-aqua">
							<div class="inner">
							  <h3><?=$totpds;?></h3>
							  <p>Registered Member</p>
							</div>
							<div class="icon">
							  <i class="fa fa-copy"></i>
							</div>
							<a class="small-box-footer" href="#">More info <i class="fa fa-arrow-circle-right"></i></a>
						  </div>
					</div><!-- ./col -->
					<div class="col-lg-3 col-xs-6">
						  <!-- small box -->
						  <div class="small-box bg-green">
							<div class="inner">
							  <h3><?=$totfullpay;?><sup style="font-size: 20px"></sup></h3>
							  <p>Full Payment</p>
							</div>
							<div class="icon">
							  <i class="fa fa-ticket"></i>
							</div>
							<a class="small-box-footer" href="#">More info <i class="fa fa-arrow-circle-right"></i></a>
						  </div>
					</div><!-- ./col -->
					<div class="col-lg-6 col-xs-12">
						  <!-- small box -->
						  <div class="small-box bg-primary">
							<div class="inner">
							  <h3><?=$totmoney;?></h3>
							  <p>Money Collected</p>
							</div>
							<div class="icon">
							  <i class="fa fa-money"></i>
							</div>
							<a class="small-box-footer" href="#">More info <i class="fa fa-arrow-circle-right"></i></a>
						  </div>
					</div><!-- ./col --> 
				</div>
				<p class="text-bold text-success text-center">Progress Total Fully Paid Registrant of <?=$thisperiod;?> Period</p>
					<div class="progress">
					  <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="<?=$progressreg;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$progressreg;?>%">
						<span class="text-bold"><?=$progressreg;?>% Complete</span>
					  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Online Time statistic
				</div>
				<div class="panel-body">
					<div id="onlinediv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
					<?//=$userol;?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Registrant Faculty
				</div>
				<div class="panel-body">
					<div id="facdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Latest Data
				</div>
				<div class="panel-body">
					<!-- Custom tabs (Registration and Payments)-->
              <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs pull-right">
                  <li><a data-toggle="tab" href="#translist">Payment</a></li>
                  <li><a data-toggle="tab" href="#pdslist">Registration</a></li>
                  <li class="active"><a data-toggle="tab" href="#loginlist">Last Login</a></li>
                  <li class="pull-left header"><i class="fa fa-file-text"></i> Latest Data</li>
                </ul>
                <div class="tab-content no-padding">
					<div class="tab-pane table-responsive active" id="loginlist">
                    <?=$donline;?>
					</div>
					
					<div class="tab-pane table-responsive" id="pdslist">
                    <?=$dpds;?>
					</div>
				  
					<div class="tab-pane table-responsive" id="translist">
                    <?=$dpay;?>
					</div>
					
				</div>
              </div><!-- /.nav-tabs-custom -->


				</div>
			</div>
		</div>
	</div>
		
</section>


		<!-- amCharts javascript sources -->
		<script src="http://www.amcharts.com/lib/3/amcharts.js" type="text/javascript"></script>
		<script src="http://www.amcharts.com/lib/3/serial.js" type="text/javascript"></script>
		<script src="http://www.amcharts.com/lib/3/pie.js" type="text/javascript"></script>
		<script src="http://www.amcharts.com/lib/3/themes/light.js" type="text/javascript"></script>

		<!-- amCharts javascript code -->
		<script type="text/javascript">
			AmCharts.makeChart("onlinediv",
				{
					"type": "serial",
					"categoryField": "hourly",
					"dataDateFormat": "YYYY-MM-DD HH",
					"theme": "light",
					"categoryAxis": {
						"minPeriod": "hh",
						"parseDates": true
					},
					"chartCursor": {
						"enabled": true,
						"categoryBalloonDateFormat": "JJ:NN"
					},
					"chartScrollbar": {
						"enabled": true
					},
					"trendLines": [],
					"graphs": [
						{
							"bullet": "round",
							"id": "AmGraph-1",
							"title": "Admin",
							"valueField": "Admin"
						},
						{
							"bullet": "square",
							"id": "AmGraph-2",
							"title": "Organizer",
							"valueField": "Organizer"
						},
						{
							"id": "AmGraph-3",
							"bullet": "round",
							"title": "Member",
							"valueField": "Member"
						}
					],
					"guides": [],
					"valueAxes": [
						{
							"id": "ValueAxis-1",
							"title": "Total User"
						}
					],
					"allLabels": [],
					"balloon": {},
					"legend": {
						"enabled": true,
						"useGraphSettings": true
					},
					
					"dataProvider": <?=$userol;?>
				}
			);
			
			AmCharts.makeChart("facdiv",
				
				{
					"type": "pie",
					"angle": 12,
					"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
					"depth3D": 15,
					"titleField": "faculty",
					"valueField": "total",
					"theme": "light",
					"allLabels": [],
					"balloon": {},
					"export": {
						"enabled": true
					},
					"titles": [],
					"dataProvider": <?=$regfac;?>
				}
				
			);
		</script>
		