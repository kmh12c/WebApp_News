<?php
	
	setcookie("NewsAppAccess", "", time()-3600);
	unset($_COOKIE["NewsAppAccess"]);
	header('Location: index.php');

?>