<?php
class Application_Model_Sendpush 
{
	function  sendPush($deviceToken,$deviceType,$pushpayload){	
      $docpath =  $_SERVER['DOCUMENT_ROOT'].'/';
		$payload = json_encode($pushpayload);
/* 	 	echo $deviceType;
		echo '==================================';
		echo $deviceToken;
		echo '==================================';
		print_r($payload);  */
		if($deviceType == 'iPhone'){
			$apnsHost = 'gateway.push.apple.com';          /*for distribution */
			//$apnsHost = 'gateway.sandbox.push.apple.com';    /*for development  */
			$apnsPort = '2195';
		    $apnsCert = $docpath.'distribution/ck.pem';/*for distribution */
			//$apnsCert = $docpath.'development/ck.pem';/*for development  */
			
			$passPhrase = '';
			
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
							
			$apnsConnection = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 60, STREAM_CLIENT_CONNECT, $streamContext);

			if ($apnsConnection == false) {
				//echo "False";return;
				exit;
			}	
			$apnsMessage = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
			if(fwrite($apnsConnection, $apnsMessage)) {
				//echo "Done";
			}
			fclose($apnsConnection);	
		}
		if($deviceType == 'Android'){ 
			$jsonreturn = $this->andriodPushNew($deviceToken,$pushpayload);
			//echo "<pre>";print_r($jsonreturn); die;
			$jsonObj = json_decode($jsonreturn);
			//echo '<br>';
			//print_r($jsonObj);
			$result = $jsonObj->results;
			$key = $result[0];			
			if($jsonObj->failure == 0 and $jsonObj->canonical_ids > 0)
			{
				$newdivicetoken = $key->registration_id;
			}
			else if($jsonObj->failure > 0 and $key->error == 'Unavailable')
			{
				$this->andriodPushNew($deviceToken,$pushpayload);
			}
		}	
	}


	function  andriodPushNew($device_token,$payload)
	{
	    //echo $device_token; 
		//echo "<pre>";print_r($payload);
		//$params = $_GET;
		$registrationIDs = array( $device_token );
		$apiKey = 'AIzaSyCXvdfaK1edDrC4Wy3k9JLEYdL_NnJ-eHQ';
		//$message = "hello";
		//$msg=array($message);
		$url = 'https://android.googleapis.com/gcm/send';

		//$push_msg['aps']=array('alert' => "testing push","push_type" =>0,"sound" =>"default","badge"=>0);	
		$push_data['payload'] = $payload;
		$fields = array(
		'registration_ids'  => $registrationIDs,
		'data'              => $push_data
		);

		$headers = array(
		'Authorization: key=' . $apiKey,
		'Content-Type: application/json'
		);

		$ch=curl_init();
		$u=curl_setopt( $ch, CURLOPT_URL, $url );
		$p=curl_setopt( $ch, CURLOPT_POST, true );
		$f=curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$h=curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		$t=curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$c=curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$j=curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$jsonn=json_encode($fields);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}

