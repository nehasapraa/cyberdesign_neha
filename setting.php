<?php
	/*
		Developer :- SIPL 
		Purpose :- members management at admin 
		date :- sep 5 2012 
	*/
	include('includes/header.php');
?>
	<!-- Ck editor files -->
	<script type="text/javascript" src="../js/ckeditor.js"></script>
	<script type="text/javascript" src="../js/sample.js" ></script>
	<link href="../js/sample.css" rel="stylesheet" type="text/css" />
    <script>
    	$(document).ready(function(e) {
			// Hide and show various tab of setting 
			$('.sub_menu_wrapper_div div').css('display','none');
			$('.general_setting_tab ul li').click(function(){
				$('.sub_menu_wrapper_div div').css('display','none');
				$('.'+this.id+'_div').css('display','block');
			});
        });
		
		// check password field 
		function check_password( value , type ){
			if(type == 'oldpassword'){
				$.post('action.php', {actiontype : 'setting', action : type , password : value}, function(data) {
					if(data == '1'){
						$('span.'+type).removeClass('incorrect');
						$('span.'+type).addClass('correct');
						$('input#password_confirm').val('1');
					}else{
						$('span.'+type).removeClass('correct');
						$('span.'+type).addClass('incorrect');
						$('input#password_confirm').val('0');
					}
				});
			}else if(type == 'npassword'){
				var npassword = $('input[name=npassword]').val();
				if(npassword != ''){
					$('span.'+type).removeClass('incorrect');
					$('span.'+type).addClass('correct');
					$('input#npassword_confirm').val('1');
				}else{
					$('span.'+type).removeClass('correct');
					$('span.'+type).addClass('incorrect');
					$('input#npassword_confirm').val('0');
				}
			}else if(type == 'cnpassword'){
				var npassword = $('input[name=npassword]').val();
				var cnpassword = $('input[name=cnpassword]').val();
				if(npassword == cnpassword && npassword  != ''){
					$('span.'+type).removeClass('incorrect');
					$('span.'+type).addClass('correct');
					$('input#cnpassword_confirm').val('1');
				}else{
					$('span.'+type).removeClass('correct');
					$('span.'+type).addClass('incorrect');
					$('input#cnpassword_confirm').val('0');
				}
			}
			if($('input#password_confirm').val() == 1 && $('input#npassword_confirm').val() == 1 && $('input#cnpassword_confirm').val() == 1 ) {
				$('#save_password').removeAttr('disabled');
			}else{
				$('#save_password').attr('disabled', 'disabled');
			}
		}
		// call ajax for update the password 
		function call_change_password(){
			var npassword = $('input[name=npassword]').val();
			var cnpassword = $('input[name=cnpassword]').val();
			$.post('action.php', {actiontype : 'setting', action : 'update_password', npassword : npassword}, function(data) {
				$('.account_setting_div .msg_class').html(data);
				$('.account_setting_div .msg_class').css('display' , 'block');
				$('input[type=password]').val('');
				$('span').removeClass('correct');
				$('span').removeClass('incorrect');
			});
		}
		
		// call function for general setting 
		function call_generalsetting(value , type){
			var testresults = false; 
			if(type == 'username'){
				if($('#username').val()){
					$.post('action.php', {actiontype : 'setting', action : 'username' , username : value}, function(data) {
						if(data == '0'){
							$('span.'+type).removeClass('incorrect');
							$('span.'+type).addClass('correct');
							$('input#'+type+'_confirm').val('1');
						}else if(data == '1'){
							$('input#'+type+'_confirm').val('1');
						}else if(data == '-1'){
							$('span.'+type).removeClass('correct');
							$('span.'+type).addClass('incorrect');
							$('input#'+type+'_confirm').val('0');
						}
					});					
				}else{
					$('span.'+type).removeClass('correct');
					$('span.'+type).addClass('incorrect');
					$('input#'+type+'_confirm').val('0');
				}
			}else if (type == 'email'){
				var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i ;
				if (filter.test(value)){
					$.post('action.php', {actiontype : 'setting', action : 'email' , email : value}, function(data) {
						//alert(data);
						if(data == '0'){
							$('span.'+type).removeClass('incorrect');
							$('span.'+type).addClass('correct');
							$('input#'+type+'_confirm').val('1');
						}else if(data == '1'){
							$('span.'+type).removeClass('incorrect');
							$('span.'+type).addClass('correct');
							$('input#'+type+'_confirm').val('1');
						}else if(data == '-1'){
							$('span.'+type).removeClass('correct');
							$('span.'+type).addClass('incorrect');
							$('input#'+type+'_confirm').val('0');
						}
					});					
				}else{
					$('span.'+type).removeClass('correct');
					$('span.'+type).addClass('incorrect');
					$('input#'+type+'_confirm').val('0');
				}
				
				if($('#email_confirm').val() == '1' && $('#username_confirm').val() == '1'){
					$('#generalseeting_update').removeAttr('disabled');
				}else{
					$('#generalseeting_update').attr('disabled' , 'disabled');
				}
			}else if(type == 'update'){
				if($('#email_confirm').val() == '1' && $('#username_confirm').val() == '1'){
					$.post('action.php', {actiontype : 'setting', action : 'update_generalsetting' , username : $('#username').val() , email : $('#email').val()}, function(data) {
						$('.general_setting_div .msg_class').html(data);
						location.reload();
					});					
				}
			}
		}
    </script>
    <div class="wrapper">
		<?php if(isset($_SESSION['error_message'])){?>
            <div class="error_messsage">	
                <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
            </div>
        <?php }?>
		<div class="blue_head"><h1>Setting</h1></div>
    	<div class="main_menu">
	        <?php include('includes/header_menu.php'); ?>
        </div>
        	<div class="clear"></div>
		<div class="general_setting_tab sub_links">
        	 <ul> 
             	<li id="account_setting"><a href="#">Password Setting</a> </li> 
                <li id="general_setting"><a href="#">General Setting</a></li> 
             </ul>
        </div>
        <div class="clear"></div>
        <div class="sub_menu_wrapper_div">
        <!-- account setting srart here-->
        <div class="account_setting_div" style="display:none">
        	<div class="msg_class"></div>
            <fieldset class="fieldset">
              <legend>Change Password</legend>
        	<table cellspacing="3" cellpadding="3" border="0" width="100%">
            	<tr>
                	<td width="20%"><label>Old password :</label></td>
                    <td> 
                    	<input type="password" name="password"  onkeyup="check_password(this.value, 'oldpassword')"/>
                    	<input type="hidden" name="password_confirm"  id="password_confirm"  value="0"/>
                        <span class="oldpassword"></span>
                    </td>
                </tr>
            	<tr>
                	<td width="20%"><label>New Password :</label></td>
                    <td> 
                    	<input type="password" name="npassword" onkeyup="check_password(this.value, 'npassword')"/> 
                    	<input type="hidden" name="npassword_confirm" id="npassword_confirm" value="0"/> 
                        <span class="npassword"></span>
                    </td>
                </tr>
            	<tr>
                	<td><label>Confirm new password :</label></td>
                    <td> 
                        <input type="password" name="cnpassword" onkeyup="check_password(this.value, 'cnpassword')"/>
                    	<input type="hidden" name="cnpassword_confirm" id="cnpassword_confirm" value="0"/> 
                        <span class="cnpassword"></span>
                    </td>
                </tr>
            	<tr>
                	<td>&nbsp;</td>
                    <td align="left"> 
                    	<input type="button" name="save_password" id="save_password" value="Update" disabled="disabled" onclick="call_change_password();"/> 
                    </td>
                </tr>
            </table>
            </fieldset>
        </div>
        <!-- General setting srart here-->
        <div class="general_setting_div">
        	<div class="msg_class"></div>
            <fieldset class="fieldset">
                    	<legend>General Setting</legend>
        	<table cellspacing="3" cellpadding="3" border="0" width="100%">
            	<tr>
                	<td width="20%"><label>Username :</label></td>
                	<td> 
                    	<input type="text" name="username" id="username" value="<?php echo $_SESSION['admin_username']?>" onkeyup="call_generalsetting(this.value , 'username');"/> 
                        <input type="hidden" id="username_confirm"/>
                        <span class="username"> </span>
                    </td>
                </tr>
            	<tr>
                	<td><label>Email :</label></td>
                	<td> 
                    	<input type="text" name="email" id="email" value="<?php echo $_SESSION['admin_email']?>" onkeyup="call_generalsetting(this.value , 'email');"/> 
                        <input type="hidden" id="email_confirm"/>
                        <span class="email"></span>
                    </td>
                </tr>
            	<tr>
                	<td>&nbsp;</td>
                    <td align="left"><input type="button" id="generalseeting_update" value="update"  disabled="disabled" onclick="call_generalsetting(this.value , 'update')"/> </td>
                </tr>
            </table>
            </fieldset>
        </div>
        <!-- active news letter -->
        
        
        </div>
    </div>
<?php
	include('includes/footer.php');
?>