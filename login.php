<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	require_once('connectvars.php');
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die('BNo connection with MySQL server');
	$user_login = $_POST["user_login"];
	$user_password = $_POST["user_password"];
	$gcm_id = $_POST["gcm_regid"];
	if(!empty($user_login) && !empty($user_password) && !empty($gcm_id)){
		$query = "SELECT * FROM user WHERE user_login = '$user_login' AND user_password = SHA('$user_password')";
		$result = mysqli_fetch_array(mysqli_query($dbc,$query));
		if(isset($result)){
			$query = "UPDATE user SET gcm_regid ='$gcm_id' WHERE user_login = '$user_login'".
			" AND user_password = SHA('$user_password');";
			$data = mysqli_query($dbc,$query);
			mysqli_close($dbc);
			if($data){
				echo 'Logged in ';
			}else{
				echo 'Problem with logging ';
			}
		}
		else{
			echo 'Wrong login or password';
		}
	}
	else{
		echo 'ERROR';
	}
}
?>