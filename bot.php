<?php
$access_token = 'DCIp/W+z2xakOLOQ1Jbz/Lz8qpOqsmkNtXKtzGGFjZomMIORZ14wX3wA2q87NRFaE74UXv8XQfGgKer+hSyu2UoJpmyPhnDPd3kOrkIFyDynIWdm7fUjvqK7M7Uxg9daPwlD9C7HX9OIuIHcOIRmgwdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);

$myfile = fopen("/usr/share/nginx/html/json.log","a");

foreach (getallheaders() as $name => $value) {
    fwrite($myfile,"$name: $value\n");
}

$postdata = file_get_contents("php://input");
fwrite($myfile,$postdata);
fwrite($myfile,"\n +++++++++++++++++++++++++ \n");



foreach (getallheaders() as $name => $value) {
    echo "$name: $value\n";
        echo "<br>";
}

// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent
			$text = $event['message']['text'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			$message1 = [
				'type' => 'text',
				'text' => $text
			];

			$message2 = [
				'type' => 'template',
				'altText' => 'This is a confirm Template',
				'template' => array(
						'type' => 'confirm',
						'text' => 'Are you sure?',
						'actions' => array(
							array(
								'type' => 'message',
								'label' => 'Yes',
								'text' => 'yes'
							),
							array(
								'type' => 'message',
								'label' => 'No',
								'text' => 'no'
							)
						)
				)
			];

			$message3 = [
				'type' => 'template',
				'altText' => 'This is a carousel template',
				'template' => array(
						'type' => 'carousel',
						'columns' => array(
							array(
								'thumbnailImageUrl' => 'https://iservices.me/imgBC/1.png',
								'title' => 'this is menu',
								'text' => 'text description',
								'actions' => array(
									array(
										'type' => 'uri',
										'label' => 'View in Google',
										'uri' => 'https://www.google.com'
									),
									array(
										'type' => 'message',
										'label' => 'Say Hello',
										'text' => 'Hello'
									)
								)
							),
							array(
								'thumbnailImageUrl' => 'https://iservices.me/imgBC/2.png',
								'title' => 'this is menu',
								'text' => 'text description',
								'actions' => array(
									array(
										'type' => 'uri',
										'label' => 'View in LINE',
										'uri' => 'https://line.me'
									),
									array(
										'type' => 'message',
										'label' => 'Say Hello',
										'text' => 'Hello'
									)
								)
							)
						)
				)
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => array($message1,$message2,$message3),
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			fwrite($myfile,$result);
			fwrite($myfile,"\n +++++++++++++++++++++++++ \n");

			//echo $result . "\r\n";
		}
	}
}



echo "OK";

?>
