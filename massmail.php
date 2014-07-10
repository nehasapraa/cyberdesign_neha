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
            $('#send_btn').click(function(){
				var subject  = $.trim($('input[name=subject]').val());
				var editor_data = $.trim(CKEDITOR.instances.editor1.getData());
				if(subject != '' && editor_data !=''){
					$('.subject_error').html('');
					$('.editor_error').html('');
					$('.massmail-content').fadeOut('slow', function() {});
					$('.massmail-sending').fadeIn('slow', function() {});
					
					$.post('action.php', { actiontype : 'adminmassmail' , formname : '' , replyto : '' , subject : subject, message : editor_data}, 
						function(data){
							//$('.massmail-sending').css('display','none');
							//$('.massmail-content').css('display','block');
							$('input[name=subject]').val('') ;
							CKEDITOR.instances.editor1.setData('');
							$('.massmail-content').fadeIn('slow', function() {});
							$('.massmail-sending').fadeOut('slow', function() {});
							$('.massmail_message').html(data);
							setTimeout(function(){$('.massmail_message').fadeOut('slow')},3000);
					});
				}else {
						(subject == '') ? $('.subject_error').html('*') : $('.subject_error').html('');
						(editor_data == '')  ? $('.editor_error').html('*'): $('.editor_error').html('');
				}
			});
        });
    </script>

    <div class="wrapper">
		<?php if(isset($_SESSION['error_message'])){?>
            <div class="error_message">	
                <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
            </div>
        <?php }?>
	
		<div class="blue_head"><h1>Mass Mail</h1></div>
    	<div class="main_menu">
	        <?php	include('includes/header_menu.php'); ?>
        </div>
        	<div class="clear"></div>
            <div class="massmail-sending" style="display:none">Loading........</div>
            <div class="massmail_message" ></div>
        	<div class="massmail-content" >
            <fieldset class="fieldset">
              <legend>Mass Mail</legend>
            	<form method="post" action="action.php">
                <input type="hidden" name="actiontype"  value="adminmassmail" />
                <input type="hidden" name="formname" placeholder="formname"/>
                <input type="hidden" name="replyto" placeholder="replyto"/>
            	<table cellspacing="3" cellpadding="3" border="0" width="100%">
                	<tr>
                    	<td><label>Subject :</label> </td>
                        <td><input type="text" name="subject" size="100"/> <span class="subject_error"></span></td>
                    </tr>
                	<tr>
                    	<td><label>Message :</label></td>
                        <td> <textarea class="ckeditor" cols="80" id="editor1" name="message" rows="10"></textarea><span class="editor_error"></span></td>
                    </tr>
                	<tr>
                        <td colspan="2" align="center"><input type="button" value="Send" title="Send" id="send_btn"/></td>
                    </tr>
                </table>
                </form>
                </fieldset>
            </div>
    </div>
<?php
	include('includes/footer.php');
?>