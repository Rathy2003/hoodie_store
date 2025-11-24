<?php
	session_start();

	 if(!isset($_SESSION["backend_id"]) && !isset($_SESSION["backend_email"]) && !isset($_SESSION['backend_username']) && !isset($_SESSION["backend_role"])){
	 	return header("location: login.php");
	 }
?>