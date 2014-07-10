<?php
	include('includes/header.php');
?>
	<script>
    	$(document).ready(function(e) {
            $('#forgot_button').click(function(){
				var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i ;
				var forgot_email = $('#forgot_email').val();
				if(filter.test(forgot_email)){
						$('.forgot_password_inner').fadeOut('slow');
						$('.forgot_password_msg_div').html('loading.....');
						$('.forgot_password_msg_div').fadeIn(1500);
						$.post('action.php', {actiontype : 'adminaction', action : 'forgot_password', email : $('#forgot_email').val()}, function(data) {
						$('.forgot_password_msg_div').html(data);
						$('.forgot_password_msg_div').fadeOut(1500);
						$('input[type=text]').val('');
						$('.forgot_password_inner').fadeIn(5000);
						});
				}else{
					
				}
			});
			
			$('li.forgot').click(function(){
				$('.content div#sub_content_div').css('display','none');
				$('input[type=text]').val('');
				$('input[type=password]').val('');
				$('.content .div_forgot').css('display','block');
				
			});
			$('li.login').click(function(){
				$('.content div#sub_content_div').css('display','none');
				$('input[type=text]').val('');
				$('input[type=password]').val('');
				$('.content .div_loginform').css('display','block'); 
			});
			$('.success_message').fadeOut(4000);

			
			$('#loginform').validate({
				rules: {
				  username: {
					minlength: 2,
					required: true
				  },
				  password: {
					required: true
				  }
				},
				messages:{
				  username : '*',
				  password : '*'
				},
				highlight: function(label) {
				},
				success: function(label) {
				}
		  });
			
        });
    </script>
    	<div class="login_box">
        	<div class="login_header">
            	<h1>Admin</h1>
                <ul class="nav"> <li class="login"> Login </li> <li class="forgot"> Forgot Password</li> </ul>
            </div>
			<?php if(isset($_SESSION['error_message'])){?>
                <div class="success_message">
                    <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
                </div>
            <?php }?>
			<?php if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout' && !preg_match( '/index.php/i',$_SERVER['HTTP_REFERER'])){?>
                <div class="success_message">Logout Successfully </div>
            <?php }?>
            <div class="content">
                <div class="div_loginform" id="sub_content_div">
                    <form action="action.php" name="loginform" id="loginform" method="post">
                        <input type="hidden" name="actiontype" value="adminlogin"/>
                        <p class="form_item"><label>Username :</label> <input type="text" name="username" placeholder="Username"/></p>
                        <p class="form_item"><label>Password :</label> <input type="password" name="password" placeholder="Password"/></p>
                        <p class="form_button"><input type="submit" name="login" value="Login" title="Login" class="btn_black" /></p>
                    </form>
                </div>
                <div class="div_forgot" id="sub_content_div" style="display:none">
                	<div class="forgot_password_msg_div"></div>
                	<div class="forgot_password_inner">
                    <p class="form_item"><label>Email :</label>  <input  type="text" id="forgot_email"/></p>
                    <p class="form_button"><input type="button" id="forgot_button" value="Send Email" title="Send Email" /></p>
                    </div>
                </div>
            </div>
        </div>
<?php
	include('includes/footer.php');
?>