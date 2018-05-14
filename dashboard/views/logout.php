<?php  session_start();


	setcookie("user_id", '', strtotime('-5 days'), '/');
	setcookie("username", '', strtotime('-5 days'), '/');	
	// Destroy the session variables
        $_SESSION['user_id']=null;
	$_SESSION['username']=null;
        unset($_SESSION['username']);
        unset($_SESSION['user_id']);
	session_destroy();	

// Double check to see if their sessions exists
if (isset($_SESSION['username'])) {
	header("location: home.php?msg=Error:_Logout_Failed");
} else {
	header("location:login.php");
	exit();
}
?>
