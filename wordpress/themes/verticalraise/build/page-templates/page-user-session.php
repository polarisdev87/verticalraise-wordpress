<?php
/* Template Name: User Session */
if(isset($_POST['your_name'])) {
	session_start();
	$_SESSION['your_name'] = $_POST['your_name'];
	echo $_SESSION['your_name'];
}
if(isset($_POST['get_your_name'])) {
	session_start();
	echo $_SESSION['your_name'];
	//session_destroy();
}
?>