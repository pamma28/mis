<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4><i class="fa fa-envelope"></i> How To Activate Reply Features</h4>
</div>
<div class="modal-body">
	<h2 class="text-primary text-center">You need synchronized the contacts, follow the instructions below:</h2>
	
	<section>
				<div class="wizard">
					<div class="wizard-inner">
						<div class="connecting-line"></div>
						<ul class="nav nav-tabs" role="tablist">

							<li role="presentation" class="active" style="width:16.6%;">
								<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1">
									<span class="round-tab">
										<i class="fa fa-sign-in"></i> Login WebSms
									</span>
								</a>
							</li>

	
							<li role="presentation" class="disabled" style="width:16.6%;">
								<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2">
									<span class="round-tab">
										<i class="fa fa-book"></i> New Category
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled" style="width:16.6%;">
								<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3">
									<span class="round-tab">
										<i class="fa fa-download"></i> Down. Contacts
									</span>
								</a>
							</li>
							
							<li role="presentation" class="disabled" style="width:16.6%;">
								<a href="#step4" data-toggle="tab" aria-controls="step4" role="tab" title="Step 4">
									<span class="round-tab">
										<i class="fa fa-edit"></i> View Category
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled" style="width:16.6%;">
								<a href="#step5" data-toggle="tab" aria-controls="step5" role="tab" title="Step 5">
									<span class="round-tab">
										<i class="fa fa-save"></i> Import Contacts
									</span>
								</a>
							</li>

							<li role="presentation" class="disabled" style="width:16.6%;">
								<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
									<span class="round-tab">
										<i class="fa fa-check"></i> Confirm Import
									</span>
								</a>
							</li>
						</ul>
					</div>

					
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="step1">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-sign-in fa-lg"></i> Login WebSms</h4>
									<div class="panel-body">
										<p>
											Login into system. You may click the button.
											<form action="http://web.sms-anda.com:6148/" method="POST" target="after" onsubmit="close()">
											<?=$user.$pass;?>
											<input type="submit" value="Login WebSms" class="btn btn-primary btn-block">
											</form>
										</p>
									</div>
									<div class="panel-footer text-right">
										<button type="button" class="btn btn-primary next-step">Next Step</button>
									
									</div>
								</div>
							</div>
							<div class="tab-pane" role="tabpanel" id="step2">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-book fa-lg"></i> Create New Phonebook Category</h4>
									<div class="panel-body">
										<p>
											Create Phonebook Category. Click the button to create category.
											<form action="http://web.sms-anda.com:6148/pilihan.php?fungsi=buat&op=create_yes" method="POST" target="after" onsubmit="close()">
											<?=$period.$code;?>
											<input type="submit" value="Create Phonebook Category" class="btn btn-primary btn-block">
											</form>
										</p>
									
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-primary next-step">Next Step</button>
								</div>
							</div>	
							</div>
							<div class="tab-pane" role="tabpanel" id="step3">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-download fa-lg"></i> Download Recent Contacts</h4>
									<div class="panel-body">								
										<p>
											Download Contacts to be imported into WebSms System. Click the button to download.
											<a href="<?=base_url('Organizer/SmsBroadcast/downloadcontacts')?>" class="btn btn-primary btn-block">Download (Contacts (Datetime-Created).txt)</a>
										</p>
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-primary next-step">Next Step</button>
								</div>
							</div>	
							</div>
							<div class="tab-pane" role="tabpanel" id="step4">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-edit fa-lg"></i> Access Phonebook Category</h4>
									<div class="panel-body">
										<p>
											Access Phonebook Category. Choose import on <b><i>"<?=$txtperiod;?>"</i></b> category.
											<img src="<?=base_url("assets/images/chooseimport.jpg");?>" class="img-responsive"/>
											<a href="http://web.sms-anda.com:6148/websms.php" target="after" class="btn btn-primary btn-block">Access Phonebook Category</a>
										</p>
									
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-primary next-step">Next Step</button>
								</div>
							</div>	
							</div>
							<div class="tab-pane" role="tabpanel" id="step5">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-edit fa-lg"></i> Import Contacts</h4>
									<div class="panel-body">
										<p>
											Import Contacts.txt to WebSMS. Choose file previously downloaded.
											<img src="<?=base_url("assets/images/choosefile.jpg");?>" class="img-responsive"/>
											
										</p>
									
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<button type="button" class="btn btn-primary next-step">Next Step</button>
								</div>
							</div>	
							</div>
							
							<div class="tab-pane" role="tabpanel" id="complete">
								<div class="panel panel-primary">
									<h4 class="text-center"><i class="fa fa-check fa-lg"></i> Confirm Import</h4>
									<div class="panel-body">
										<p>
											Confirm Import by clicking "Import Nomer" button.
											<img src="<?=base_url("assets/images/importphone.jpg");?>" class="img-responsive"/>
										</p>
											<div class="bg-yellow text-center">
											<h3>
											<span class="fa fa-exclamation-triangle"></span> 
											Press green button "Confirm Synchronize" to update the last update of synchronization.
											</h3>
											</div>
										
									</div>
								<div class="panel-footer text-right">
									<button type="button" class="btn btn-default prev-step">Previous</button>
									<a class="btn btn-success" href="<?=base_url('Organizer/SmsBroadcast/confirmsynch');?>" target="after">Confirm Synchronize</a>
								</div>
							</div>	
							
							</div>
							<div class="clearfix"></div>
						</div>
				</div>
			</section>
		  	
	<h3 class="text-primary text-center">Web Result:</h3>
	<div class="embed-responsive embed-responsive-16by9 bg-gray" id="webframe">
	<iframe class="embed-responsive-item" id="after" name="after"></iframe>
	</div>


</div>
<div class="modal-footer">
	<a class="btn btn-default btn-ok btn-lg" data-dismiss="modal">Close</a>
</div>
<script>
function close() {
    $('#after').on('load', function() {
        window.close();
    });
}

$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title], input, textarea, select').tooltip();
    
    //Wizard
    $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        var $target = $(e.target);
    
        if ($target.parent().hasClass('disabled')) {
            return false;
        }
    });

    $(".next-step").click(function (e) {
		var $active = $('.wizard .nav-tabs li.active');
        $active.next().removeClass('disabled');
        nextTab($active);

    });
    $(".prev-step").click(function (e) {
        var $active = $('.wizard .nav-tabs li.active');
        prevTab($active);

    });
});

function nextTab(elem) {
    $(elem).next().find('a[data-toggle="tab"]').click();
	
}
function prevTab(elem) {
    $(elem).prev().find('a[data-toggle="tab"]').click();
}

$('#webframe').load(function(){

if($("#webframe").contents().text().search("Selamat datang")!=-1){
    alert("found");
}

});
</script>