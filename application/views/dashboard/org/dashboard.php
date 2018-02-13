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
					<div class="col-lg-4 col-xs-12">
						<div class="info-box bg-aqua">
				            <span class="info-box-icon"><i class="fa fa-copy"></i></span>
				            <div class="info-box-content">
				              <span class="info-box-text">Registered Member</span>
				              <span class="info-box-number"><?=$totpds;?> out of <?=$quota;?><sub></sub></span>

				              <div class="progress">
				                <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="<?=$progressreg;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$progressreg;?>%"></div>
				              </div>
				                  <span class="progress-description">
				                    <?=$progressreg;?>% Complete
				                  </span>
				            </div>
				            <div class="info-box-footer">
				            </div>
				            <!-- /.info-box-content -->
				          </div>
						  
					</div><!-- ./col -->
					<div class="col-lg-4 col-xs-12">
						<div class="info-box bg-green">
				            <span class="info-box-icon"><i class="fa fa-ticket"></i></span>
				            <div class="info-box-content">
				              <span class="info-box-text">Full Payment</span>
				              <span class="info-box-number"><?=$totfullpay;?> out of <?=$quota;?><sub></sub></span>

				              <div class="progress">
				                <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="<?=$progressreg;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$progresspay;?>%"></div>
				              </div>
				                  <span class="progress-description">
				                    <?=$progresspay;?>% Complete
				                  </span>
				            </div>
				            <div class="info-box-footer">
				            </div>
				            <!-- /.info-box-content -->
				          </div>
						  
					</div><!-- ./col -->
					<div class="col-lg-4 col-xs-12">
						<div class="info-box bg-blue">
				            <span class="info-box-icon"><i class="fa fa-money"></i></span>
				            <div class="info-box-content">
				              <span class="info-box-text">Money Collected</span>
				              <span class="info-box-number"><?=$totmoney;?> <sub></sub></span>

				              
				            </div>
				            <div class="info-box-footer">
				            </div>
				            <!-- /.info-box-content -->
				          </div>
						  
					</div><!-- ./col -->
				</div>
				
				
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="box box-primary">
				<div class="box-body">
					<?=$dashtmp;?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
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
                  <li class="active"><a data-toggle="tab" href="#pdslist">Registration</a></li>
                  <li class="pull-left header"><i class="fa fa-file-text"></i> Latest Data</li>
                </ul>
                <div class="tab-content no-padding">
					
					<div class="tab-pane table-responsive active" id="pdslist">
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
		<div class="col-md-4">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Latest Login Users
				</div>
				<div class="panel-body">
					<?=$donline;?>
					
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Faculty of Registrant Comparison Last 3 Years
				</div>
				<div class="panel-body">
					<div id="facdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-primary">
				<div class="panel-heading text-center">
					Gender of Registrant Year by Year
				</div>
				<div class="panel-body">
					<div id="yeardiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				</div>
			</div>
		</div>
	</div>
	
		
</section>


		<!-- amCharts javascript sources -->
		<script src="<?=base_url('assets/js/armchart/armcharts.js');?>" type="text/javascript"></script>
		<script src="<?=base_url('assets/js/armchart/serial.js');?>" type="text/javascript"></script>
		<script src="<?=base_url('assets/js/armchart/light.js');?>" type="text/javascript"></script>
		
		<!-- amCharts javascript code -->
		<script type="text/javascript">			
			AmCharts.makeChart("facdiv",
				
				{
					"type": "serial",
					"categoryField": "faculty",
					"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
					"titleField": "faculty",
					"startDuration": 1,
					"theme": "light",
					"chartCursor": {"enabled": true},
					"chartScrollbar": {"enabled": true},
					"trendLines": [],
					"graphs": [
							{"balloonText": "[[title]], [[category]] Registrant:[[value]]","fillAlphas": 1,"id": "AmGraph-1","labelText": "[[value]]","title": "<?=$thisperiod;?>","type": "column","valueField": "total"},
							{"balloonText": "[[title]], [[category]] Registrant:[[value]]","bullet": "round","id": "AmGraph-2","labelText": "[[value]]","lineThickness": 2,"title": "<?=($thisperiod-1);?>","valueField": "lasttotal"},
							{"balloonText": "[[title]], [[category]] Registrant:[[value]]","bullet": "square","id": "AmGraph-3","labelText": "[[value]]","lineThickness": 1,"title": "<?=($thisperiod-2);?>","valueField": "lasttotall"}
							],
					"guides": [],
					"valueAxes": [{"id": "ValueAxis-1","title": "Total Registrant"}],
					"allLabels": [],
					"balloon": {},
					"export": {
						"enabled": true
					},
					"legend": {"enabled": true,	"useGraphSettings": true},
					"dataProvider": <?=$regfac;?>
				}
				
			);
			
			AmCharts.makeChart("yeardiv",{
			"type": "serial",
			"categoryField": "pyear",
			"rotate": true,
			"startDuration": 1,
			"theme": 'light',
			"autoDisplay": true,
			"categoryAxis": {
				"gridPosition": "start"
			},
			"trendLines": [],
			"graphs": [
				{
					"balloonText": "[[category]], [[title]] : [[value]]",
					"fillAlphas": 1,
					"id": "AmGraph-1",
					"title": "Male Registrant",
					"type": "column",
					"valueField": "totmale"
				},
				{
					"balloonText": "[[category]], [[title]] : [[value]]",
					"fillAlphas": 1,
					"id": "AmGraph-2",
					"title": "Female Registrant",
					"type": "column",
					"valueField": "totfemale"
				}
			],
			"guides": [],
			"valueAxes": [
				{
					"id": "ValueAxis-1",
					"stackType": "regular",
					"title": "Total Registrant"
				}
			],
			"allLabels": [],
			"balloon": {},
			"legend": {
				"enabled": true,
				"useGraphSettings": true
			},
			"dataProvider": <?=$yearbyyear;?>
		});
			
	
		</script>
		