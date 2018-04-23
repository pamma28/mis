<!-- Main content -->
    <section class="content">
	<div class="box box-primary box-solid">
        <div class="box-body">
        	<div class="row">
            <div class="col-md-3 col-sm-2 text-center">
				<img src="<?=base_url('assets/images/UNSOED.png')?>" height="150px" class="img-rounded"/>
			</div>
			<div class="col-md-6 col-sm-8">
				<h2 class="text-center">Welcome to <?=$appname;?> <br/><?=$period;?> Period</h2>
				<hr/>
				<p class="lead text-center text-primary">
					<?=$descweb;?>
				</p>
			</div>
			<div class="col-md-3 col-sm-2 text-center"> 
				<img src="<?=base_url('assets/images/SEF.png')?>"  height="150px" class="img-rounded"/>
			</div>
			</div>	
		
		<hr class="clearfix"/>
			<div class="row text-center">
				<div class="col-md-3 text-primary">
					<i class="fa fa-users fa-4x"></i>
					<h3>Quota</h3>
					<p class="lead"><b><i><?=$quoregist;?> Member</i></b></p>
					<hr/>
				</div>
				<div class="col-md-3 text-success">
					<i class="fa fa-money fa-4x"></i> 
					<h3>Price </h3>
					<p class="lead"><b><i>Rp. <?=$priceregist;?></i></b>
					<hr/>
				</div>
				<div class="col-md-3 text-info">
					<i class="fa fa-calendar fa-4x"></i> 
					<h3>Registration </h3>
					<p class="lead"><b><i><?=$dateregist;?></i></b>
					<hr/>
 				</div>
 				<div class="col-md-3 text-info">
					<i class="fa fa-phone fa-4x"></i> 
					<h3>Contacs </h3>
					<p class="lead"><b><i>WA/SMS <?=$cp;?></i></b>
					<hr/>
 				</div>
			</div>
		</div>
	</div>

	<div class="box box-primary box-solid">
        <div class="box-header with-border  text-center">
            <h5 class="box-title"><b>Welcome Message</b></h5>
        </div><!-- /.box-header -->
        <div class="box-body">
        	
			<?=$tmphome;?>
			
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