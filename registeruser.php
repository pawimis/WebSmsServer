<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
 require_once('connectvars.php');
	 $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die('No SQL server connection');
	$user_login = $_POST['user_login'];
	$user_password = $_POST['user_password'];
	$user_phone_number = $_POST['user_phone_number'];
	$user_email = $_POST['user_email'];
	if(!empty($user_login) && !empty($user_password) && !empty($user_phone_number) && !empty($user_email)){
		$query = "SELECT * FROM user WHERE user_login = '$user_login' OR user_email = 'user_email' OR user_phonenumber = '$user_phone_number' ";
		$check = mysqli_fetch_array(mysqli_query($dbc,$query));
		if(isset($check)){
			echo 'Account already exists';
		}
		else{
			$query = "INSERT INTO user (join_date,user_login,user_password,user_phonenumber,user_email)".
				"VALUES(NOW(),'$user_login',SHA('$user_password'),'$user_phone_number','$user_email')";
			mysqli_query($dbc,$query)
				or die('ERROR');
			echo 'Registered';
		}
	}
	else{
		echo "Wrong data";
	}
}
?>