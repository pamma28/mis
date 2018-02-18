<!-- Content Header (Page header) -->
<section class="content-header">
	
	<h1><i class="fa fa-write-o fa-lg"></i> Compose<small>Email</small></h1>
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
			echo form_open_multipart(base_url('Organizer/Mailbroadcast/sendmail'),array('name'=>'fmail', 'method'=>'POST','class'=>'form-inline'));
			($sender=='') ? print('<h4 class="text-center text-danger">You need to configure the Gmail Account (sender) through bottom box.</h4>'):null;
			?>	
		    <div class="box-body <?php ($sender!='') ? print('visible'):print('hidden'); ?>">
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
							<small class="text-info">Note: Put the code in Mail Content and it will change into each spesific detail of Mail Recipient. <br/>e.g: {name} will send as "Bill Gates"</small>
						</div>
					</div>
					</div>
				
				
				<div class="row">
					<div class="col-md-6">
						<div id="texteditor">	
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default wrapper">
						<div class="panel-heading"><b>File Attachment (s)</b></div>
						<div id="upload" class="row panel-body">
							<div id="drop" class="col-md-4">
								Drop Here

								<a>Browse</a>
								<?=$attach;?>
							</div>

							<div class="col-md-6">
								<ul>
								<!-- The file uploads will be shown here -->
								
								</ul>
							</div>
							
						</div>
						</div>
					
					
					</div>
				</div>
				
				
				<div class="panel panel-default">
					<div class="panel-footer">
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
						
						<div class="box box-default bg-gray collapsed in" id="detailfooter">
							<div class="box-body">
								<b>Footer Mail :</b><br/>
								<?=$footer;?>
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
					  <?=$fusersto.$listto.$ccto.$code.$nameto.$redi.$att.$listfile.$cancelfile.$inred;?>
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
			
			<br/>
			<br/>
			
			<fieldset class="scheduler-border">
			<legend class="scheduler-border"><a role="button" data-toggle="collapse" href="#collapseSetting" aria-expanded="false" aria-controls="collapseSetting">Setting Gmail Account (Sender)</a></legend>
			<div id="collapseSetting" class="collapse">
				<div class="well">
				<h5 class="text-info"><span class="fa fa-info-circle"></span> Status Authorization</h5>
				
				<p><b>Current Status: </b> <?=$status;?></p>
				<p><b>Email Address (Sender): </b> <var><?=$sender;?></var></p>
				<div class="text-right">
				<?php if ($sender!='') { ?>
					<a href="<?=base_url('Organizer/Mailbroadcast/removesetting')?>" class="btn btn-danger">Remove Configuration</a>
				<?php } else{ ?>
					<a href="<?=$urlauth;?>" class="btn btn-primary">Authorize</a>
				<?php } ?>
				</div>
				
				<div class="bg-warning">
				<h5 class="text-warning"><span class="fa fa-info-circle"></span> Additional Information (Remove)</h5>
				<blockquote>
				<p>In order to fully remove the configuration, please remove our App (SEF Membership) from Security Account through this <a href="https://security.google.com/settings/security/permissions" target="_blank" alt="remove apps">link</a></p>
					<div class="text-center">
					<img class="img-rounded" src="<?=base_url('upload/system/remove gapps.JPG')?>" width="50%"><br/>
					<small>Please remove our App, by clicking remove as shown in picture.</small>
					</div>
				</blockquote>
				</div>
				</div>
			</div>
			</fieldset>
			
		</div>
	</div>
	
	
	<script type="text/javascript">
	 
	$(document).ready(function () {
		getlist();
		$('#senderstat').bootstrapToggle();
	
		$("#texteditor").summernote({
			 minHeight: 300
		});
		$("#texteditor").summernote('code',$('input[name="fcode"]').val());
		
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
	                        $(tokenHTMLNode).text(tokenObj.email).append(
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
							$(itemHTMLNode).html(resultItem.name + ' <small><i>' + resultItem.role + '/' + resultItem.year + lvl + '</i></small> (' + resultItem.email +')');
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
		$.post('<?=base_url('Organizer/Mailbroadcast/getcontactlist')?>',{
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
			$('input[name="flistto"]').val( $('#mto').tokchify('getValue').map(function(a) {return a.email;}).toString());
			$('input[name="fnameto"]').val($('#mto').tokchify('getValue').map(function(a) {return a.name;}).toString());			
			$('input[name="fccto"]').val($('#mcc').tokchify('getValue').map(function(a) {return a.email;}).toString());
			$('input[name="fusersto"]').val($('#mto').tokchify('getValue').map(function(a) {return a.user;}).toString());				
			$('input[name="fcode"]').val($('#texteditor').summernote('code'));			
			$('input[name="fatt"]').val('');			
		});
		
		$("#submit").click(function(){
			$('input[name="fredi"]').val('');			
		});
		
		$("#res").click(function(){
			$('#mto,#mcc').tokchify('setValue','');
			$('#texteditor').summernote('code','');
		});
	});
  
$(function(){

    var ul = $('#upload ul');

    $('#drop a').click(function(){
        $(this).parent().find('input').click();
    });

    $('#upload').fileupload({

        dropZone: $('#drop'),

        add: function (e, data) {

            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#337ab7" data-readOnly="1" data-bgColor="#fcf8e3" /><p></p><span class="fa fa-close fa-lg text-warning"></span><small></small><b class="hidden"></b></li>');

            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            data.context = tpl.appendTo(ul);

            tpl.find('input').knob();

            tpl.find('span').click(function(){

                if(tpl.hasClass('working')){
                    jqXHR.abort();
					data.context.find('small').html('<p>Upload File Cancelled.</p>');
					tpl.removeClass('working');
                }else {
					var clist = $('input[name="fcancelfile"]').val();
					if (clist=='') {
					$('input[name="fcancelfile"]').val(tpl.find('b').text());
					} else{
					$('input[name="fcancelfile"]').val($('input[name="fcancelfile"]').val() + ',' + tpl.find('b').text());
					}
                tpl.fadeOut(function(){
                    tpl.remove();
                });
				}

            });

            var jqXHR = data.submit();
        },

        progress: function(e, data){

            var progress = parseInt(data.loaded / data.total * 100, 10);

            data.context.find('input').val(progress).change();
			
            
			if(progress == 100){
                data.context.removeClass('working');
            }
        },

        fail:function(e, data){
            data.context.addClass('error');
        },
		
		done: function (e, data) {
			var res = jQuery.parseJSON(data.jqXHR.responseText);
			data.context.find('span').removeClass();
			if (res.status=='success'){
			data.context.find('span').addClass('fa fa-check fa-lg text-primary');
			data.context.find('small').html('<p>Upload Success</p>');
			data.context.find('b').text(res.file);
			var flist = $('input[name="flistfile"]').val();
				if (flist=='') {
				$('input[name="flistfile"]').val(res.file);
				} else{
				$('input[name="flistfile"]').val($('input[name="flistfile"]').val() + ',' + res.file);
				}
			} else {
			data.context.find('span').addClass('fa fa-exclamation-circle fa-lg text-danger');
			data.context.addClass('error');
			data.context.find('small').html(res.error).text();
			}
			
        }

    });


    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});  
	
		$('#btnfooter').on('click', function(){
			$(this).parent().find("i.fa").toggleClass('fa-caret-down fa-caret-up');;
		});
	</script>
</section>