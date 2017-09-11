<?php
$access_token = '2QNzT+0OTx1/DInNMjPhLk8rbEEa9AxCsJRCOGdawUVpdbmHGeYlDIOWwcweN8lSI1jHm5sFgMfgtAXUxjhzj1YHnoXG2BzcN8RNpa7HWr1If1Iki25xYQZR8m5GhnWojTWecw1ye9pFhKDo/r2yrgdB04t89/1O/w1cDnyilFU=';

$url_cmd  = "https://api.netpie.io/topic/PudzaSOI/cmd?retain&auth=xXCgD7V2IbWlArR:QgrhkLHJ3xbbm58B9TsVtK15d";
$url_data = "https://api.netpie.io/topic/PudzaSOI/data?auth=xXCgD7V2IbWlArR:QgrhkLHJ3xbbm58B9TsVtK15d";

// Build message to reply back
$cmd_word = array("on","off");
$inf_word = array("t","ec","tb","ph","tbv","??");


// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
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
			
			if (in_array($text, $cmd_word)) {
				if ($text == "on") {
					$reply = "Turn On";
					$cmd = "11";
				}
				else if ($text == "off") {
					$reply = "Turn Off";
					$cmd = "10";
				}
	
				// send to test topic
				put($url_cmd,$cmd);
				$messages = [
					'type' => 'text',
					'text' => $reply
				];
			 }

			 if (in_array($text, $inf_word)) {
			 	$res = get($url_data);
				$otext = json_decode($res);
				$payload = $otext[0]->payload;
				$datas = explode(',', $payload);

				if ($text == "t") {
					$reply = $datas[0]." C";
				}
				else if ($text == "ec") {
					$reply = $datas[1] ." mS/cm";
				}
				else if ($text == "tbv") {
					$reply = $datas[2]." V";
				}
				else if ($text == "ph") {
					$reply = $datas[3];
				}
				else if ($text == "tb") {
					$X = (float)$datas[2];
				    $tub = -1120.4*$X*$X + 5742.3*$X - 4352.9

					$tubformat = sprintf("%.2f mg/L", $tub);
					$reply = $tubformat;
				}
				else if ($text == "??") {
					$reply = "Help\n?? Command list\nt = Temperature\nec = EC\ntb = Turbidity\ntbv = Tubidity Volt\nph = PH";
				}

				$messages = [
					'type' => 'text',
					'text' => $reply
				];
			 }


			 if (in_array($text, $cmd_word) || in_array($text, $inf_word)) {
				// Reply to ...
				// == Make a POST Request to Messaging API to reply to sender
				//echo testing
//				$messages = [
//					'type' => 'text',
//					'text' => $text
//				];

				$url = 'https://api.line.me/v2/bot/message/reply';
				$data = [
					'replyToken' => $replyToken,
					'messages' => [$messages],
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

				echo $result . "\r\n";
			}
		}
	}
}

echo "OK";

function get($url) {
	 $ch = curl_init($url);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	 curl_setopt($ch, CURLOPT_USERPWD, "{YOUR NETPIE.IO APP KEY}:{YOUR NETPIE.IO APP SECRET}");
	 $response = curl_exec($ch);
	 curl_close ($ch);

	 return $response;
}

function put($url,$tmsg) {                 
     $ch = curl_init($url); 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
     curl_setopt($ch, CURLOPT_POSTFIELDS, $tmsg);
     curl_setopt($ch, CURLOPT_USERPWD, "{YOUR NETPIE.IO APP KEY}:{YOUR NETPIE.IO APP SECRET}");
     $response = curl_exec($ch);
     curl_close ($ch);
     
     return $response;
}
