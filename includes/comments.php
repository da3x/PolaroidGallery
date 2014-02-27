<?php

	function addComment($userID, $name, $comment) {
		$path = "galleries/".$_GET['gallery']."/comments.json";
		$handle = fopen($path, "a+");

		// Falls das LOCK nicht klappt, ignoriere ich das erstmal einfach...
		if (flock($handle, LOCK_EX)) {

			$json = json_decode(file_get_contents($path), true);
			if ($json == null) $json = array();

			array_push($json, array(
				'commentID' => uniqid("Comment"),
				'userID' => $userID,
				'name' => htmlspecialchars($name),
				'comment' => htmlspecialchars($comment),
				'timestamp' => time()
				)
			);

		    ftruncate($handle, 0);
		    fwrite($handle, json_encode($json));
		    flock($handle, LOCK_UN);
		}

		fclose($handle);

		push($name." schreibt: ".$comment);

		// Den zuletzt benutzen Namen merken wir uns...
    	setcookie('name', $name, time() + (3600 * 24 * 365 * 5), '/'); // 5 Jahre...

		// Wir springen am Ende immer wieder auf die normale URL zurück!
		header('Location: /show/'.$_GET['gallery']);
	}

	function deleteComment($commentID) {
		$path = "galleries/".$_GET['gallery']."/comments.json";
		$handle = fopen($path, "a+");

		// Falls das LOCK nicht klappt, ignoriere ich das erstmal einfach...
		if (flock($handle, LOCK_EX)) {

			$json = json_decode(file_get_contents($path), true);
			if ($json == null) $json = array();

			$i = 0;
			$cmt = null;
			$name = null;
			foreach ($json as $comment) {
				if ($comment['commentID'] == $commentID) {
					$cmt  = $comment['comment'];
					$name = $comment['name'];
					unset($json[$i]);
					break;
				}
				$i++;
			}

		    ftruncate($handle, 0);
		    fwrite($handle, json_encode($json));
		    flock($handle, LOCK_UN);
		}

		fclose($handle);

		push($name." löscht: ".$cmt);
	}

	function loadComments() {
		$path = "galleries/".$_GET['gallery']."/comments.json";
		return json_decode(file_get_contents($path), true);
	}

	if (isset($_GET['name']) && isset($_GET['comment']) && strlen($_GET['name']) > 0 && strlen($_GET['comment']) > 0) {
		addComment($cID, $_GET['name'], $_GET['comment']);
	}

	if (isset($_GET['delete']) && strlen($_GET['delete']) > 0) {
		deleteComment($_GET['delete']);
	}

	// Wir springen am Ende immer wieder auf die normale URL zurück!
	if (isset($_GET['name']) || isset($_GET['comment']) || isset($_GET['delete'])) {
		header('Location: /show/'.$_GET['gallery']);
	}

	function commentTimestamp($comment) {
		if ($lang == "en") return date('\o\n Y-m-d \a\t H:i', $comment['timestamp']);
		return date('\a\m d.m.Y \u\m H:i', $comment['timestamp']);
	}
?>
