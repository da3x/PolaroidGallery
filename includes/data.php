<?php

	function get($key) {
		if (file_exists("galleries/".$_GET['gallery']."/data.json")) {
			$json   = json_decode(file_get_contents("galleries/".$_GET['gallery']."/data.json"), true);
			if (isset($json[$key]) && $json[$key]) return $json[$key];
		}
		return null;
	}

	function put($key, $value) {
		$path = "galleries/".$_GET['gallery']."/data.json";
		$handle = fopen($path, "a+");

		// Falls das LOCK nicht klappt, ignoriere ich das erstmal einfach...
		if (flock($handle, LOCK_EX)) {

			$json = json_decode(file_get_contents($path), true);
			if ($json == null) $json = array();

			$json[$key] = $value;

		    ftruncate($handle, 0);
		    fwrite($handle, json_encode($json));
		    flock($handle, LOCK_UN);
		}

		fclose($handle);
	}

	function getTimeSpan() {
		$minTime = get("minTime");
		$maxTime = get("maxTime");
		$min = date("d.m.Y", $minTime);
		$max = date("d.m.Y", $maxTime);
		if (language() == "en") {
			$min = date("m/d/Y", $minTime);
			$max = date("m/d/Y", $maxTime);
		}
		if ($min == $max) return $min;
		return $min." - ".$max;
	}

	// Hier speichern wir uns die Pfade aller gefundenen Bilder...
	$files = glob("{galleries/".$_GET['gallery']."/*.jpg,galleries/".$_GET['gallery']."/*.JPG}",GLOB_BRACE);
	if (count($files) > 0) {
		array_multisort(array_map("exif_date", $files), SORT_STRING, SORT_ASC, $files);
	}

	// Die Zeitstempel des jüngsten und des ältesten Bildes merken wir uns!
	if (get("maxTime") == null) {
		if (count($files) > 0) {
			put("minTime", strtotime(exif_date($files[0])));
			put("maxTime", strtotime(exif_date($files[count($files)-1])));
		}
	}
?>
