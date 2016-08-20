<?php
	require_once('connectvars.php');
	require_once('startsession.php');
	$error_msg = "";
	if (!isset($_SESSION['id'])) {
		if (isset($_POST['submit'])) {
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		
			$user_username = mysqli_real_escape_string($dbc, trim($_POST['username']));
			$user_password = mysqli_real_escape_string($dbc, trim($_POST['password']));
			
			if (!empty($user_username) && !empty($user_password)) {
				$query = "SELECT id FROM user WHERE user_login = '$user_username' AND user_password = SHA('$user_password')";
				$data = mysqli_query($dbc, $query);
				
				if(mysqli_num_rows($data) == 1 ){
					$row = mysqli_fetch_array($data);
					$_SESSION['id'] = $row['id'];	
					$_SESSION['user_login'] = $user_username;
					setcookie('id', $row['id'], time() + (60 * 60 * 24 * 10));    //  10 days.
					setcookie('user_login', $user_username, time() + (60 * 60 * 24 * 10));    //  10 days.
					$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/send.php';
					header('Location: ' . $home_url);
				}
				else{
					$error_msg = 'Wrong password or username';
				}
			}
			else{
				$error_msg = 'Username and password required';
			}
		}
	}
	$page_title = 'Login page';
	require_once('header.php');
	if (empty($_SESSION['id'])) {
    echo '<p class="error">' . $error_msg . '</p>';
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<body>
	
    <fieldset>
      <legend>Logowanie</legend>
      <label for="username">Username:</label>
      <input type="text" name="username" value="<?php if (!empty($user_username)) echo $user_username; ?>" /><br />
      <label for="password">Password:</label>
      <input type="password" name="password" />
    </fieldset>
    <input type="submit" value="Log in" name="submit" />
		</body>
  </form>
<?php
	}
	else{
		 echo('<p class="login">Logged in.</p>');
		 $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/send.php';
		 header('Location: ' . $home_url);
	}
?>