        <!-- Main content -->
        <section class="content">

         <div class="box box-primary box-solid">
                <div class="box-header with-border">
                  <h5 class="box-title"></h5>
                  <div class="box-tools pull-right">
                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-lg fa-caret-up"></i></button>
                  </div><!-- /.box-tools -->
                </div><!-- /.box-header -->
					
                <div class="box-body">
				<div class="clearfix">
				<img src="<?=base_url('assets/images/UNSOED-banner.png')?>" width=20% class="img-responsive pull-left"/> 
				<img src="<?=base_url('assets/images/SEF-banner.png')?>" width=20% class="img-responsive pull-right"/>
				<h3 class="text-center">Welcome to the Regular Class <br/><?=$r['period'];?> Period</h3>
				</div>
				<hr class="divider"/>
				
					<div class="panel panel-info text-center">
					<div class="panel-heading">
					<b>Registration Phase</b>
					</div>
					<div class="panel-body">
					 <h4>The registration <span id="date_regist"></span><span id="date_regist2"></span></h4>
					 <h5>for only <span class="text-warning"><?=$r['quota'];?> Students</span>, by only <span class="text-success">IDR <?=$r['price'];?></span></h5>
					</div>
					</div>
					
					
					<div class="panel panel-info text-center">
					<div class="panel-heading">
					<b>Contact Us</b>
					</div>
					<div class="panel-body">
						<div class="row">
						<div class="col-md-4">
						<i class="fa fa-phone"></i> <?=$r['cp'];?>
						</div>
						<div class="col-md-4">
						<i class="fa fa-bbm">BBM</i> <?=$r['bbm'];?> 
						</div>
						<div class="col-md-4">
						<i class="fa fa-envelope"></i> <?=$r['email'];?>
						</div>
					</div>
					</div>			
								
				</div><!-- /.box-body -->
              </div>
		<hr class="divider"/>
		<div class="row">
            <div class="col-md-6" >
             
			<h4 class="text-center text-warning">Recent Articles</h4>
			  <div class="panel-body">
						<ul class="products-list product-list-in-box">
							<li class="item">
								<div class="product-img">
								<img src="http://placehold.it/50x50/d2d6de/ffffff" alt="Product Image">
								</div>
							<div class="product-info">
								<a href="" class="product-title">Title</a>
								<span class="label label-success fixed pull-right">authors</span> 
								<span class="label label-primary pull-right">date</span>
								<span class="product-description">
							
								Description
							
								</span>
							</div>
							</li><!-- /.item -->
						</ul>
					</div>
			 
            </div><!-- /.col -->
			<div class="col-md-6">
				<h4 class="text-center text-warning">Agendas</h4>
					<!-- The time line -->
              <ul class="timeline">
                <!-- timeline time label -->
                <li class="time-label">
                  <span class="bg-red">
                    09 June 2015
                  </span>
                </li>
                <!-- /.timeline-label -->
                <!-- timeline item -->
                <li>
                  <i class="fa fa-envelope bg-blue"></i>
                  <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> 08:00</span>
                    <h3 class="timeline-header"><a href="#">Opening</a> Registration</h3>
                    <div class="timeline-body">
							
                    </div>
                    <div class="timeline-footer">
                      <!--
					  <a class="btn btn-primary btn-xs">Read more</a>
                      <a class="btn btn-danger btn-xs">Delete</a>
					  -->
                    </div>
                  </div>
                </li>
                <!-- END timeline item -->
                
              
                <li>
                  <i class="fa fa-clock-o bg-gray"></i>
                </li>
              </ul>
					
			</div>
          </div>
		
        </section>

<script type="text/javascript">
var s = '<?=$r['begin'];?>';
f = '<?=$r['expired'];?>';
format = '%-w <sub>week%!w</sub> ' + '%-d <sub>day%!d</sub> ' + '%H <sub>hr</sub> '+ '%M <sub>min</sub> '+ '%S <sub>sec</sub>';

$('#date_regist').countdown(s)
	.on('update.countdown', function(event) {	
	$(this).html("<b>will be open </b><br/>"+event.strftime(format));})
	.on('finish.countdown', function(event) {
		$("#date_regist2").countdown(f)
		.on('update.countdown', function(event) {
		$(this).html("<b> is open for </b><br/> "+event.strftime(format));})
		.on('finish.countdown', function(event) {
		$(this).html("<b> is closed.</b>");
		});
	});
</script>