﻿<?php
	require_once('startsession.php');
	$page_title = 'Wyślij Sms';
	require_once('header.php');
	require_once('navigationpanel.php');
	require_once('connectvars.php');
	
	if (isset($_SESSION['id'])) {
		if (isset($_POST['submit'])) {
			$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			$send_date = mysqli_real_escape_string($dbc, trim($_POST['send_date']));
			$send_hour =  mysqli_real_escape_string($dbc, trim($_POST['send_hour']));
			$sms_text = mysqli_real_escape_string($dbc, trim($_POST['sms_text']));
			$phone_number = mysqli_real_escape_string($dbc, trim($_POST['phone_number']));
	
			$user_pass_phrase = SHA1($_POST['verify']);
			if ($_SESSION['pass_phrase'] == $user_pass_phrase) {
				if (!empty($send_date) && !empty($sms_text) && !empty($phone_number) && !empty($send_hour)){
					if (preg_match('/^\d{9}$/',$phone_number)) {
						if (preg_match('/^\d{4}-\d{2}-\d{2}$/',$send_date)){
							if(preg_match('/^\d{2}:\d{2}$/',$send_hour)){
								$full_send_date .= "$send_date $send_hour:00";
								$today = date("Y-m-d H:i:s");
								if ($full_send_date >= $today){
									$current_session_id = $_SESSION['id'];
									$query = "SELECT gcm_regid FROM user WHERE id = '$current_session_id'";
									$data = mysqli_query($dbc, $query)
										or die('Error with database');
									if (mysqli_num_rows($data) == 1) {
										$row = mysqli_fetch_array($data);
										$registatoin_ids = $row['gcm_regid'];
										mysqli_close($dbc);
										echo '<p>Message send!</p>';
										echo '<p><strong>Receiver number:</strong> ' . $phone_number . '<br />';
										echo '<strong>Text:</strong> ' . $sms_text . '</p>';
										echo '<p>Date : '.$full_send_date.'</p>';
										$path_to_gmc_server = 'https://android.googleapis.com/gcm/send';
										$msg = array('topic' => 'sms',
										'message' => $sms_text ,
										'number' => $phone_number,
										'title' => 'MobileOn is calling'
										);
										$fields = array
										(
											'registration_ids' 	=> array($registatoin_ids),
											'data'			=> $msg
										);
										$headers = array(
											'Authorization: key=' . GOOGLE_API_KEY,
											'Content-Type: application/json'
											);
										$ch = curl_init();
										curl_setopt($ch, CURLOPT_URL, $path_to_gmc_server);
										curl_setopt($ch, CURLOPT_POST, true);
										curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
										curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
									 
										$result = curl_exec($ch);
										if ($result === FALSE) {
											die('Curl failed: ' . curl_error($ch));
										}
										curl_close($ch);
										echo '<p> Result' . $result . ' </p>';		
									}
								}
								else{
									echo '<p class="error">Date is incorrect. Today is '.$today.' . You wrote: '.$full_send_date.'</p>';
								}
							}
							else{
								echo '<p class="error">Wrong hour.</p>';
							}
						}
						else{
							echo '<p class="error">Wrong date.</p>';
						}
					}
					else{
						echo '<p class="error">Wrong receiver number.</p>';
					}					 
				}
				else{
					echo '<p class="error">All field must be fulfilled. </p>';
				}
			}
			else {
				echo '<p class="error">Rewrite given code.</p>';
			}
		}
	}
?>

  <hr />
  <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<label for="phone_number">Number :</label>
    <input type="text" id="phone_number" name="phone_number" value="<?php if (!empty($phone_number)) echo $phone_number; ?>" /><br />
    <label for="send_date">Date:</label>
    <input type="text" id="send_date" name="send_date" value="<?php if (!empty($send_date)) echo $send_date; else echo 'YYYY-MM-DD'; ?>" /><br />
	<label for="send_hour">Hour:</label>
    <input id="send_hour" name="send_hour" value="<?php if (!empty($send_hour)) echo $send_hour; else echo 'HH:MM'; ?>" /><br />
	<br></br>
	<label class="dist" for="sms_text">Text :</label>
	<p class="box">
				<textarea type="text" id="sms_text" name = "sms_text" cols = "70" rows = "10" placeholder="Message text..."> </textarea>
	</p>
	<p class="captcha">
	<label for="verify">Validation: </label>
	<input type="text" id="verify" name="verify" value="Type password." />
	<img src="captcha.php" alt="Verification"/>
	</p>
	<br/>
    <input type="submit" value="Add" name="submit" />
  </form>
</body> 
</html>
