<?php 
	// DEVELOPER :- SIPL 
	// PURPOSE :- ADMIN HEADER MENU
	// DATE :- 4 sep 2012
?>
<div style="display:block;">
    <ul class="header_menu">
        <li><a href="home.php" <?php if(preg_match('/home.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Home</a></li>
        <li><a href="setting.php" <?php if(preg_match('/setting.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Edit Settings</a></li>
        <li><a href="members.php" <?php if(preg_match('/members.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Members List</a></li>
        <li><a href="statistics.php" <?php if(preg_match('/statistics.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Statistics</a></li>
        <li><a href="newsletter.php" <?php if(preg_match('/newsletter.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Newsletter Templates</a></li>
        <li><a href="massmail.php" <?php if(preg_match('/massmail.php/i',$_SERVER['PHP_SELF'])){ echo 'class="selected"' ;}?>>Mass Mail</a></li>
        <li><a href="action.php?actiontype=logout">Logout</a></li>
    </ul>
</div>