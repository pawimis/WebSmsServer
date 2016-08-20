?<?php
require_once('connectvars.php');
$logfile = 'Log.txt';
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$today = date("Y-m-d H:i:s");
$log = array(
		'DATE: '=> $today,
		' SUCCES: '=> ' true ',
		' USER ID: ' => ' NULL ',
		' MESSAGE ID ' => ' NULL ',
		' DESCRIPTION ' => " SCRIPT STARTED ".PHP_EOL
	);
	file_put_contents ($logfile ,$log , FILE_APPEND );
while(1){
	$today = date("Y-m-d H:i:s");
	$query = "SELECT * FROM message_db WHERE send_date = '$today' ";
	$data = mysqli_query($dbc, $query)
		or die('Error with database1');
	if (mysqli_num_rows($data) == 1) {
		$row = mysqli_fetch_array($data);
		$message_id = $row['id'];
		$user_id = $row['user_id'];
		$message_text = $row['message_text'];
		$number = $row['send_phone_num'];
		$sent = $row['sent'];
		if($sent == 0){
			if(!empty($user_id)){
				$query = "SELECT gcm_regid FROM user WHERE id = '$user_id' ";
				$data2 = mysqli_query($dbc, $query) 
					or die('Error with gcm_regid');
				if (mysqli_num_rows($data2) == 1) {
					$GCMID = mysqli_fetch_assoc($data2);
					$gcm_id = $GCMID['gcm_regid'];
					if (!empty($message_text) && !empty($number) && !empty($gcm_id) ){
						$path_to_gmc_server = 'https://android.googleapis.com/gcm/send';
						$msg = array('to' => '/topics/global/',
						'message' => $message_text ,
						'number' => $number,
						'title' => 'MobileOn is calling'
						);
						$fields = array
						(
							'registration_ids' 	=> array($gcm_id),
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
							die(/*'Curl failed: ' . curl_error($ch)*/);
							$log = array(
								' DATE: '=> $today,
								' SUCCES: '=> ' false ',
								' USER ID: ' => $user_id,
								' MESSAGE ID: ' => $message_id,
								' DESCRIPTION: ' => ' FAIL WITH GCM SERVICE ',
							);
							file_put_contents ($logfile ,$log , FILE_APPEND );
						}else{
							
							$query = "UPDATE message_db  SET send=1 WHERE id = '$message_id'";
							$data = mysqli_query($dbc, $query)
								or die('Error with database1');
							$log = array(
								'DATE: '=> $today,
								' SPACE ' => "  ",
								' SUCCES: '=> 'true',
								' SPACE ' => "  ",
								' USER ID: ' => $user_id,
								' SPACE ' => "  ",
								' MESSAGE ID: ' => $message_id,
								' SPACE ' => "  ",
								' DESCRIPTION: ' => ''.PHP_EOL
							);
							file_put_contents ($logfile ,$log , FILE_APPEND );
						}
						curl_close($ch);
						file_put_contents($logfile,$result,FILE_APPEND);
					}else{
						$log = array(
								'DATE: '=> $today,
								' SUCCES: '=> 'false',
								' USER ID: ' => $user_id,
								' MESSAGE ID: ' => $message_id,
								' DESCRIPTION: ' => 'Error with fetching to send one of three values'.PHP_EOL
							);
						file_put_contents ($logfile ,$log , FILE_APPEND );
					}			
				}else{
					$log = array(
							'DATE: '=> $today,
							' SUCCES: '=> 'false',
							' USER ID: ' => $user_id,
							' MESSAGE ID: ' => $message_id,
							' DESCRIPTION: ' => 'NO GCM ID'.PHP_EOL
						);
					file_put_contents ($logfile ,$log , FILE_APPEND );
				}
			}else{
				$log = array(
						'DATE: '=> $today,
						' SUCCES: '=> 'false',
						' USER ID: ' => $user_id,
						' MESSAGE ID: ' => $message_id,
						' DESCRIPTION: ' => 'Message without user ID'.PHP_EOL
					);
					file_put_contents ($logfile ,$log , FILE_APPEND );
			}
		}
	}
}
?>