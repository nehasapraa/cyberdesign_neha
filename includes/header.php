<?php
	// DEVELOPER :- SIPL 
	// PURPOSE :- ADMIN HEADER 
	// DATE :- 4 sep 2012

	ob_start();
	session_start();	
	echo '<pre>';
	//print_r($_SESSION);
	echo '</pre>';
	
	if(!isset($_SESSION['admin_id']) && !preg_match('/index.php/',$_SERVER['SCRIPT_FILENAME'])){
		header('location: index.php');
	}

	if(isset($_SESSION['admin_id']) && preg_match('/index.php/',$_SERVER['SCRIPT_FILENAME'])){
		header('location: home.php');
	}
?>
<!DOCTYPE >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php if(!isset($title)){echo 'Auto Responder';}else{ echo $title;}?></title>

<link rel="stylesheet" href="css/style.css"/>
<script src="../js/jquery-1-3-2.js"></script>
<script src="../js/jquery.validate.min.js"></script>

</head>
<body class="light_grey_bg">
<!-- div will be close in footer file-->
<div class="page_container">
    <div id="header">
    </div>