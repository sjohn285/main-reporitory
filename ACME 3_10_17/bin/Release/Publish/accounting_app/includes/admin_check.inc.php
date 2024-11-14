<?php
	
	if(isset($_SESSION['role']) && $_SESSION['role'] == 2){

	}else{
		var_dump($_SESSION);

		header("Location: login-screen.php");
	}

?>