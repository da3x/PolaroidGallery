<?php

	// Ich nutze PushOver, um mir bestimmte Events anzusagen...
	function push($message) {
		$url   = "https://api.pushover.net/1/messages.json";
		$token = "abcdefghijklmnopqrstuvwxyz";
		$user  = "abcdefghijklmnopqrstuvwxyz";
		$loc   = str("title") ." | ". getTimeSpan() ." — ";
		// Anscheind testet PushOver die übergebene URL und löst damit eine Endlos-Schleife aus...
		// $url   = "http://gallery.da3x.de/show/".$_GET['gallery'];
		$params = array('http' => array(
			'method' => 'POST',
			'content' => 'token='.$token.'&user='.$user.'&message='.urlencode($loc.$message) // .'&url='.urlencode($url)
		));
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			@fclose($fp);
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		@fclose($fp);
		return $response;
 	}

	$cID = isset($_COOKIE['userID']) ? $_COOKIE['userID'] : uniqid("User");
	setcookie('userID', $cID, time() + (3600 * 24 * 365 * 5), '/'); // 5 Jahre...

	$cName = str(isset($_COOKIE['name']) ? $_COOKIE['name'] : null);
	setcookie('name', $cName, time() + (3600 * 24 * 365 * 5), '/'); // 5 Jahre...

	$cLast = isset($_COOKIE['last']) ? intval($_COOKIE['last']) : 0;
	setcookie('last', time(), time() + (3600 * 24 * 365 * 5), '/'); // 5 Jahre...

	// Jeder Aufruf nach 12h Inaktivität soll vorerst über Push an mein Handy gehen...
	if (time() - $cLast > 3600 * 12) {
		push("Besuch von ".($cName != null ? $cName : $cID));
	}

?>
