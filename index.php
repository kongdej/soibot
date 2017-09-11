<?php
$access_token = '2QNzT+0OTx1/DInNMjPhLk8rbEEa9AxCsJRCOGdawUVpdbmHGeYlDIOWwcweN8lSI1jHm5sFgMfgtAXUxjhzj1YHnoXG2BzcN8RNpa7HWr1If1Iki25xYQZR8m5GhnWojTWecw1ye9pFhKDo/r2yrgdB04t89/1O/w1cDnyilFU=';

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

			// Build message to reply back
			
			if (substr($text,0,1) == "?") {
				$res = get();
				$otext = json_decode($res);
				$payload = $otext[0]->payload;
				$datas = explode(',', $payload) // temp,ec,tub,ph
				if (substr($text,1) == "?") {
					$data = "Help\n?? help\n?t = Temperature\n?ec = EC\ntb = Turbidity\nph = PH";
				}
				else if (substr($text,1) == "t") {
					$data = $data[0];
				}
				else if (substr($text,1) == "ec") {
					$data = $data[1];
				}
				else if (substr($text,1) == "tb") {
					$data = $data[2];
				}
				else if (substr($text,1) == "ph") {
					$data = $data[3];
				}

				$messages = [
					'type' => 'text',
					'text' => $data
				];

				// Make a POST Request to Messaging API to reply to sender
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
function get() {
	 $url = "https://api.netpie.io/topic/PudzaSOI/data?auth=xXCgD7V2IbWlArR:QgrhkLHJ3xbbm58B9TsVtK15d";
	 $ch = curl_init($url);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	 curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $tmsg);
	 curl_setopt($ch, CURLOPT_USERPWD, "{YOUR NETPIE.IO APP KEY}:{YOUR NETPIE.IO APP SECRET}");
	 $response = curl_exec($ch);
	 curl_close ($ch);
	 return $response;
}
