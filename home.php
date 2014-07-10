<?php
	// DEVELOPER :- SIPL 
	// PURPOSE :- Admin Home page 
	// DATE :- 4 sep 2012

	include('includes/header.php');
?>
	<script>
		$(document).ready(function(e) {
   			$('.success_message').fadeOut(4000);
        });
    </script>
    <div class="wrapper">
		<?php if(isset($_SESSION['error_message'])){?>
            <div class="error_messsage">
                <?php echo $_SESSION['error_message']; session_unregister('error_message');?>
            </div>
        <?php }?>
		<div class="blue_head"><h1>Admin Dashboard</h1></div>
    	<div class="main_menu">
	        <?php include('includes/header_menu.php'); ?>
        </div>
        <h1 class="welcome_txt">Welcome In Admin Dashboard</h1>
    	<span class="clr">&nbsp;</span>
    </div>
<?php
	include('includes/footer.php');
?>
