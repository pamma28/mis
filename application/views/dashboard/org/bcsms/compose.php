<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-write-o fa-lg"></i> Compose<small>SMS</small></h1>
		<ol class="breadcrumb">
            <?=set_breadcrumb();?>
		</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="box">
			<?php if ($this->session->flashdata('v')!=null){ ?>
			<div class="box-header">
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('v');?>
			</div>
			<?php } else if ($this->session->flashdata('x')!=null){ ?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<?=$this->session->flashdata('x');?>
			</div>		
			</div>
			<?php }  
			echo form_open(base_url('Organizer/SmsBroadcast/sendsms'),array('name'=>'fmail', 'method'=>'POST','class'=>'form-inline'));
			($sender=='') ? print('<h4 class="text-center text-danger">You need to configure the SMS Account (WebSms) through bottom box.</h4>'):null;
			?>	
		    <div class="box-body <?php ($sender!='') ? print('visible'):print('hidden'); ?>">
				<div class="row">
				
					<div class="col-md-4 col-sm-6 col-xs-12">
					  <div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
						<div class="info-box-content bg-info">
						  <span class="info-box-text">(With Reply) Credit</span>
						  <span class="info-box-number"><span id="withrepcredit">Rp. <?=$repcredit;?></span></span>
						  <p>Cost: Rp. 40/sms</p>
						</div><!-- /.info-box-content -->
					  </div><!-- /.info-box -->
					</div><!-- /.col -->
					
					<div class="col-md-4 col-sm-6 col-xs-12">
					  <div class="info-box">
						<div class="bg-yellow">
							<p class="text-info text-center"> Press the "Refresh Credit", to update the credit.</p>
							<button class="btn btn-block btn-info btn-lg" id="refreshcredit" type="button"><i class="fa fa-refresh" id="iconrefresh"></i> Refresh Credit</button>
						</div>
					  </div><!-- /.info-box -->
					</div><!-- /.col -->
					
					<div class="col-md-4 col-sm-6 col-xs-12">
					  <div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
						<div class="info-box-content bg-info">
						  <span class="info-box-text">(No Reply) Credit</span>
						  <span class="info-box-number"><span id="norepcredit">Rp. <?=$norepcredit;?></span></span>
						  <p>Cost: Rp. 20/sms</p>
						</div><!-- /.info-box-content -->
					  </div><!-- /.info-box -->
					</div><!-- /.col -->

				  </div>
				 
				
				<div class="row">
					<div class="col-md-6">
						<div class="well">
							<?=$metadata;?>
						</div>
					</div>
					<div class="col-md-6">
						<div class="well">
						
							<h4 class="text-center"><strong>Special Code</strong></h4>
							<div class="bg-info">
							<div class="row">
							<div class="col-md-6">
										<dl class="dl-horizontal">
											<dt><code>{honor}</code></dt> <dd>: Mr./Ms.</dd>
											<dt><code>{name}</code></dt> <dd>: Full Name</dd>
											<dt><code>{NIM}</code></dt> <dd>: NIM</dd>
											<dt><code>{faculty}</code></dt> <dd>: Faculty</dd>
											<dt><code>{period}</code></dt> <dd>: Period</dd>
										</dl>
									
							</div>
							<div class="col-md-6">
									
										<dl class="dl-horizontal">
											<dt><code>{email}</code></dt> <dd>: Email</dd>
											<dt><code>{phone}</code></dt> <dd>: Phone Number</dd>
											<dt><code>{level}</code></dt> <dd>: Level (if any)</dd>
											<dt><code>{payment}</code></dt> <dd>: Payment Status</dd>
											<dt><code>{birthdate}</code></dt> <dd>: Birthdate</dd>
										</dl>
							</div>
							</div>
						</div>
							
							<small class="text-info">Note: Put the code in SMS Content and it will change into each spesific detail of SMS Recipient. <br/>e.g: {name} will send as "Bill Gates"</small>
							
						
						</div>
					</div>
				</div>
				
				
				<div class="row">
						<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
							<p><b>SMS Content: </b></p>
							<?=$editor;?>
							<div>						
								<div class="input-group bg-danger">
									<span class="btn btn-info">
									<a href="#choosetemplate" data-toggle="collapse" id="btnfooter" class="collapse in"> 
									<i class="fa fa-caret-up"></i>
									<i class="bg-aqua">Use Template</i>
									</a>
									</span>
								</div>
								<p id="choosetemplate" class="collapsed in"> <?=$opttmp;?></p> 
							</div>
							</div>
							<div class="panel-footer ">
								<div class="text-right">
								Total Credit will be used <?=$countercredit;?> Total Characters left <?=$counterchar;?> Total SMS <?=$countersms;?> 
								</div>
							</div>
						</div>
						</div>
						<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-body">
							<h4 class="text-center text-primary"><b>Reply Features</b></h4>
							<div class="text-left">
								<div class="input-group">
		                        <span class="input-group-addon">
		                          <?=$usereply;?>
		                        </span>
		                        <span class="input-group-addon">
									<b>Use Reply Features</b> (Rp.40/sms)
		                        </span>
								</div>
								
								<div class="input-group">
		                        <span class="input-group-addon">
									<a href="<?=base_url('Organizer/SmsBroadcast/helpreply');?>" data-target=".bs-help" data-toggle="modal" role="button" alt="Help">
									<i class="fa fa-question-circle"></i>
									<i>How to set up reply features</i></a>
		                        </span>
		                        <span class="input-group-addon">
									<i><small>(Last syncronized in <?=$lastsync;?>)</small></i>
								</span>
								</div>
							</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-body">
							<h4 class="text-center text-primary"><b>Footer Usage</b></h4>
							<div class="text-left">
								<div class="input-group">
									<span class="input-group-addon">
									  <?=$usefoo;?> 
									</span>
									<span class="input-group-addon">
									<b>Use Footer</b>
									</span>
								</div>
								<div class="input-group">
									<span class="input-group-addon">
									<a href="#detailfooter" data-toggle="collapse" id="btnfooter" class="collapse in"> 
									<i class="fa fa-caret-up"></i>
									<i>Toogle Footer</i>
									</a>
									</span>
								</div>
								
								<div class="box box-primary box-solid bg-gray collapsed in" id="detailfooter">
									<div class="box-body">
										<b>Footer SMS : </b>
										<code><?=$footer;?></code>
										<?=$codefooter;?>
									</div>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>
				
				
				<div class="btn-group btn-group-justified" role="group" aria-label="myButton">
					<div class="btn-group" role="group">
						<button type="reset" id="res" class="btn btn-danger btn-lg">Reset</button>
					</div>
					<div class="btn-group" role="group">
					  <?=$fusersto.$listto.$nameto.$redi.$inred;?>
					</div>
					<div class="btn-group" role="group">
					  <?=$inbtn;?>
					</div>
				</div>
			
			</div>
			<?php 
			echo form_close();
			?>
  		
	<div class="box-footer clearfix">
			<?php echo form_open($furlsave,array('name'=>'fsetting', 'method'=>'POST','class'=>'form-horizontal'));?>
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting SMS Account (WebSms)</a></legend>
			<div id="collapseSetting" class="collapse">
				<div class="row well">
				<div class="col-md-6 col-sm-12">
				<h4 class="text-info text-center"><span class="fa fa-user"></span> Account Details (WebSms)</h4>
				<p><b>WebSms User: </b> <?=$accuser;?></p>
				<p><b>WebSms Phone Number: </b> <?=$accno;?></p>
				<p><b>WebSms Password: </b>
					<div class="input-group"><?=$accpass;?>
					<span class="input-group-addon"><a class="btn btn-xs btn-default" type="button" id="togglePassword"><i class="fa fa-eye"></i></a></span>
					</div>
				</p>
				</div>
				
				<div class="col-md-6 col-sm-12">
				<h4 class="text-warning text-center"><span class="fa fa-gear"></span> Programming Details</h4>
				<p><b>WebSms (With Reply) URL: </b> <?=$urlbc;?></p>
				<p><b>WebSms(No Reply) URL: </b> <?=$urlnotif;?></p>
				<p><b>WebSms (With Reply) API: </b> <?=$funcbc;?></p>
				<p><b>WebSms(No Reply) API: </b> <?=$funcnotif;?></p>
				<p><b>WebSms Check Credit Function: </b> <?=$funcsaldo;?></p>
					<div class="bg-info">
					<h4><i class="text-info fa fa-links"></i> Link References </h4>
					<ul>
						<li><a href="web.sms-anda.com:6148/pilihan.php?fungsi=http_api&op=daftar" target="_blank" alt="WebSMS reference">WebSMS reference</a> </li>
						<li><a href="http://www.freesms4us.com/kirimsms.php" target="_blank" alt="SMS Notification reference">SMS Notification reference</a> </li>
					</ul>
					</div>
				
				<div class="bg-warning"><i class="text-danger fa fa-exclamation-triangle"></i> Please be advice to change to programming setting, or system will not able to send SMS.</div>
				</div>
				
				<br class="clearfix"/>
				<div class="text-right">
					<?=$btnupdateset;?>
				</div>
				
				
				</div>
			</div>
			</fieldset>
			<?=form_close();?>
			
		</div>
	</div>
	
	<!-- Modal Help-->
	<div class="modal fade bs-help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
			
        </div>
    </div>
	</div>
	
	
	<script type="text/javascript">
	 
	$(document).ready(function () {
		getlist();
		
		function countcredit(){
		var j = 0;
		if($("#usefoo").is(':checked')){
			var totchar = $("#smstext").val().length + $("#mali").val().length + $('input[name="ffooter"]').val().length;
		} else {
			var totchar = $("#smstext").val().length + $("#mali").val().length;
		}

		var to = $('#mto').tokchify('getValue').map(function(a) {return j++;});
			if ($("#muserep").is(':checked')){
				$("#mcountercredit").val((Math.ceil(totchar / 160)) * 40 * j);
			} else {
				$("#mcountercredit").val((Math.ceil(totchar / 160)) * 20 * j);
			}
			if (totchar == 0){
				$("#mcounterchar,#mcountersms,#mcountercredit").val(0);
				
			} else {
				$("#mcounterchar").val(160 - (totchar % 160));
				$("#mcountersms").val(Math.ceil(totchar / 160));
				
			}
		}
		
		$("#smstext,#mali").on('keyup',function () {
			countcredit();
		});
		
		$("#muserep").on('change',function(){
			if($("#smstext").val().length>0)
			{
			countcredit();
			}
		});
		
		$("#refreshcredit").click(function(){
		var credit = [];
		$('#iconrefresh').addClass("fa-spin");
		$('#refreshcredit').addClass("disabled");
		$.post('<?=base_url('Organizer/SmsBroadcast/checkcredit')?>',{}, 
			function (data) {
				if (data.length < 100){
                credit = $.parseJSON(data);
				$.post('<?=base_url('Organizer/SmsBroadcast/updatecredit')?>',{
				rep: credit[1],
				norep: credit[0]
				});
				}
			if ($.isNumeric(credit[0])){
				$("#withrepcredit").text("Rp. " + credit[1]);
				$("#norepcredit").text("Rp. " + credit[0]);
			} else {
				$("#withrepcredit,#norepcredit").text('Network Error');
			}
			$('#iconrefresh').removeClass("fa-spin");
			$('#refreshcredit').removeClass("disabled");
            });
		});
		
		$('.bs-help').on("hidden.bs.modal", function (e) {
		$(e.target).removeData("bs.modal").find(".modal-body").empty();
		});
			
		
		if ($('input[name=mbc][value=1]:radio').is(':checked')){
			$("#optbc").attr("class","visible");
			getlist();
		}
		
        var contacts = {};
		function checkReady () {    
					// Common Tokchi options object used for contact auto-completion
				   var options = {
	                    autoFocus :true,
	                    searchKeywordDelimiter : null,
	
	                    onSearchKeyword : function (tokchi, keyword) {
	                        keyword = keyword.toLocaleLowerCase();
	                        var letter = keyword.charAt(0);
	                        var list = contacts[letter];
	                        
	                        if (list) {
	                            var result = [];
	                            
	                            for (var i = 0; i < list.length; ++i) {
	                                var contact = list[i];
	                                
	                                if (contact.name.toLocaleLowerCase().indexOf(keyword) > -1) {
	                                    result.push(contact);
	                                }
	                            }
	                            
	                            result.sort(function (a, b) {
	                                return a.name.localeCompare(b.name);
	                            });
	
	                            tokchi.setSearchResult(result);
	                        } else {
	                            tokchi.setSearchResult(null);
	                        }
	                    },
	                    
	                    onCreateToken : function (tokchi, tokenHTMLNode, tokenObj) {
	                        $(tokenHTMLNode).text(tokenObj.phone).append(
	                            $(' <span>')
	                                .text('')
	                                .addClass('fa fa-times')
	                                .click(function () {
	                                    tokchi.removeToken(tokenHTMLNode);
	                                })
	                        );
	                    },
						
						onUnwrapToken : function (tokchi, tokenHTMLNode, tokenObj) {
							return tokenObj.name;
	                    },
	
	                    onCreateDropdownItem : function (tokchi, itemHTMLNode, resultItem) {
	                        var lvl='';
							if(resultItem.level!=null){lvl = '/'+resultItem.level;}
							$(itemHTMLNode).html(resultItem.name + ' <small><i>' + resultItem.role + '/' + resultItem.year + lvl + '</i></small> (' + resultItem.phone +')');
	                    },
	                };
	                
	                // Set up input fields for email auto-completion
	                options.autoFocus = false,
					options.allowLineBreaks = false,
					options.dropdownStyle = 'fixed',
	                $('#mto').tokchify(options);
	                $('#mcc').tokchify(options);
                
            }
		
		
		function getlist(){
		$.post('<?=base_url('Organizer/SmsBroadcast/getcontactlist')?>',{
					role: $('#optrole').val(),
					period: $('#optyear').val(),
					lunas: $('#optlunas').val(),
					level: $('#optlvl').val()
				}, function (data) {
                contacts = $.parseJSON(data);
            });	
			checkReady();
		}
		
		
		$("input[name=mbc][value=1]:radio").change(function () {
			$("#optbc").attr("class","visible");
			getlist();
			
		});
		$("input[name=mbc][value=0]:radio").change(function () {
			$("#optbc").attr("class","hidden");
			$("#optrole,#optyear,#optlvl").val('');
			$('#mto').tokchify('setValue','');
			getlist();
			
		});
		
		$("#optyear,#optlvl,#optlunas").change(function () {
			getlist();
			
		});
		
		$("#optrole").change(function (e) {
			if (this.value=="3"){
			$("#lbllvl,#lbllunas").attr("class","visible");
			$("#optlvl,#optlunas").attr("class","form-control");} 
			else {
			$("#lbllvl, #optlvl,#lbllunas,#optlunas").attr("class","hidden");
			$("#optlvl,#optlunas").val('');
			}
			getlist();
			
		});
		
		$("#applyall").click(function (e) {
			$('#mto').tokchify('setValue','');
			$('#mto').tokchify('setValue',contacts['all']);
		});
		
		$("#submit,#redi").click(function(){
			$('input[name="flistto"]').val( $('#mto').tokchify('getValue').map(function(a) {return a.phone;}).toString());
			$('input[name="fnameto"]').val($('#mto').tokchify('getValue').map(function(a) {return a.name;}).toString());
			$('input[name="fusersto"]').val($('#mto').tokchify('getValue').map(function(a) {return a.user;}).toString());	
		});
		
		$("#submit").click(function(){
			$('input[name="fredi"]').val('');			
		});
		
		$("#res").click(function(){
			$('#mto,#mcc').tokchify('setValue','');
			
		});
		
		
		$('#btnfooter').on('click', function(){
			$(this).parent().find("i.fa").toggleClass('fa-caret-down fa-caret-up');;
		});
		
		$('#togglePassword').on('click', function(){
			$(this).find("i.fa").toggleClass('fa-eye fa-eye-slash');
			var type    = ($(this).find("i.fa").hasClass('fa-eye-slash') ? 'text' : 'password');
			var input   = $('#accpass');
			var replace = input.clone().attr('type', type);
			input.replaceWith(replace);
		});
		
		$("#opttmp").change(function (e) {
			$('#smstext').val('');
			if ($(this).val()!=''){
				$.post('<?=base_url('Organizer/SmsBroadcast/gettmpdata')?>',{
					idtmp: $(this).val()
				}, function (data) {
					tmp = $.parseJSON(data);	
					$('#smstext').val(tmp['tmpcontent']);
					countcredit();
				});	
				
			}
		});

		
	});
  	</script>
</section>