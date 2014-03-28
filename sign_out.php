<?php
		session_start(); 
		session_destroy();
		header('Location: http://192.168.68.61:3000');
		exit();
?>
