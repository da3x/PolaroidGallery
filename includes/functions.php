<?php

	// Für Mehrsprachigkeit hier ein wenig Kleinkram... ich will englisch und
	// deutsch (default) unterstützen. Ich hatte erst auf gettext() gesetzt...
	// das funktioniert aber in keinster Weise!
	function language() {
		// Der Request Parameter überschreibt alles!
		if (isset($_GET['lang']) && $_GET['lang'] == "en") return "en";
		if (isset($_GET['lang']) && $_GET['lang'] == "de") return "de";
		// Ansonsten versuchen wir die Angaben des Browsers auszulesen...
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($langs as $value){
				if (substr($value,0,2) == "en") {
					return "en";
				}
				if (substr($value,0,2) == "fr") {
					return "en"; // erstmal englisch für die Franzosen...
				}
				if (substr($value,0,2) == "de") {
					return "de";
				}
			}
		}
		// Das default ist deutsch...
		return "de";
	}
	function str($key) {
		$lang = language();

		// Alle Texte lassen sich je Galerie individuell überschreiben... dazu muss man
		// legiglich die korrekte data.json mit in das Verzeichnis legen. Wenn ich einen Text
		// dort vorfinde, dann ziehe ich diesen entsprechend in die Oberfläche.
		if (file_exists("galleries/".$_GET['gallery']."/data.json")) {
			$json   = json_decode(file_get_contents("galleries/".$_GET['gallery']."/data.json"), true);
			if (isset($json[$key]) && $json[$key]) return $json[$key];
		}

		// Einige bekannte User übersetzen wir direkt...
		if ($key == "User50a6c8f0db12e") return "Daniel Bleisteiner"; // MacBook, Chrome
		if ($key == "User50a9d854e697c") return "Daniel Bleisteiner"; // iMac, Chrome, ECG
		if ($key == "User50a6ceb23baf5") return "Matthias Wappler";
		if ($key == "User50a7c6e057c59") return "Anita Bleisteiner";

		// Wenn es keine solchen Sonder-Texte gibt, greifen die defaults...
		if ($lang == "de") {
			if ($key == "title") return "Galerie";
			if ($key == "h1") return "Galerie";
			if ($key == "h2.images") return "Wir haben mal unsere Kamera geleert und die brauchbaren Bilder zusammen gestellt – viel Spaß!";
			if ($key == "h2.map") return "Die folgende Karte zeigt die Orte, an denen einige der Bilder entstanden sind. Ein Klick auf die Karte aktiviert diese und erlaubt so, genauer nachzusehen.";
			if ($key == "h2.video") return "Diesmal ist das eine oder andere Video dabei – viel Vergnügen!";
			if ($key == "h2.comments") return "Kommentare";
			if ($key == "no.video") return "Dein Browser unterstützt leider kein Video – Google's Chrome wäre eine bessere Alternative!";
			if ($key == "p.download") return "Wer mag, kann sich die Bilder auch in voller Auflösung herunterladen – dazu einfach das folgende Icon anklicken. Eventuell vorhandene Videos sind nicht Teil des Archivs und können einzeln herunter geladen werden.";
			if ($key == "p.footer") return "Bitte behandelt den Link vertraulich – die Galerie ist nicht für's große Publikum bestimmt. Die Kontrolle über die Verbreitung soll in unserer Hand bleiben. Wer sich nicht daran hält, muss damit rechnen, beim nächsten Mal außen vor zu bleiben. Danke!";
			if ($key == "span.ribbon") return "Bitte nicht weitergeben!";
			if ($key == "comment.delete.confirm") return "Willst Du deinen Kommentar wirklich wieder löschen?";
			if ($key == "comment.delete.title") return "Kommentar löschen...";
			if ($key == "comment.placeholder.text") return "...was denkst Du?";
			if ($key == "comment.submit") return "Kommentieren";
		}
		if ($lang == "en") {
			if ($key == "title") return "Gallery";
			if ($key == "h1") return "Gallery";
			if ($key == "h2.images") return "We just cleared our camera and collected some good images for you – have fun!";
			if ($key == "h2.map") return "The following map shows the places where some of the images have been taken. A click on that map activates it and allows a closer look.";
			if ($key == "h2.video") return "This time we've got some video – enjoy!";
			if ($key == "h2.comments") return "Comments";
			if ($key == "no.video") return "Your browser doesn't support video – Google's Chrome would be a better alternative!";
			if ($key == "p.download") return "You may download all images at full resolution if you like – simply click the following icon. Possibly available videos are not part of that archive and may be downloaded individually.";
			if ($key == "p.footer") return "Please keep the link private – this gallery is not meant for the public. We want to keep control over propagation. By not respecting this wish you'll risk not to be informed of any new galleries in the future. Thanks!";
			if ($key == "span.ribbon") return "Please do not share!";
			if ($key == "comment.delete.confirm") return "Do you really want to delete your comment again?";
			if ($key == "comment.delete.title") return "Delete comments...";
			if ($key == "comment.placeholder.text") return "...what do you think?";
			if ($key == "comment.submit") return "Comment";
		}

		// Nur, wenn wir gar nichts finden, geben wir den Key wie er kam wieder zurück.
		return $key;
	}

	// Die folgenden EXIF Funktionen erlauben uns einen einfachen aber nicht
	// unbedingt effizienten Zugriff auf die Bild-Informationen. Nicht effizient
	// deshalb, weil wir sie jede mal neu auslesen.
	function exif_date($file) {
		$exif = exif_read_data("$file", 0, true);
		return $exif['EXIF']['DateTimeOriginal'];
	}
	function exif_comment($file) {
		$exif = exif_read_data("$file", 0, true);
		return $exif['EXIF']['Comment'];
	}
	function exif_orientation($file) {
		$exif = exif_read_data("$file", 0, true);
		return $exif['IFD0']['Orientation'];
	}
	function exif_lat($file) {
		$exif = exif_read_data("$file", 0, true);
		return getGps($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);
	}
	function exif_lon($file) {
		$exif = exif_read_data("$file", 0, true);
		return getGps($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);
	}
	function exif_dump($file) {
		$exif = exif_read_data("$file", 0, true);
		foreach ($exif as $key => $section) {
			foreach ($section as $name => $val) {
				echo "$key.$name: $val<br />\n";
			}
		}
	}

	// Die GPS Koordinaten sind als kodiertes Array im EXIF hinterlegt. Diese Funktion
	// macht für uns die notwendige Dekodierung.
	function getGps($exifCoord, $hemi) {
		$degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
		return round($flip * ($degrees + $minutes / 60 + $seconds / 3600), 5);
	}
	function gps2Num($coordPart) {
		$parts = explode('/', $coordPart);
	 	if (count($parts) <= 0) return 0;
		if (count($parts) == 1) return $parts[0];
	  	return floatval($parts[0]) / floatval($parts[1]);
	}

	// Um die Bilder auf vernünftige Zwischengrößen zu skallieren, bedienen
	// wir uns dieser Funktion, die das einfach und effizient für uns erledigt.
	function resample($jpgFile, $thumbFile, $width, $orientation) {
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($jpgFile);
		$height = (int) (($width / $width_orig) * $height_orig);
		// Resample
		$image_p = imagecreatetruecolor($width, $height);
		$image   = imagecreatefromjpeg($jpgFile);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Fix Orientation
		switch($orientation) {
			case 3:
				$image_p = imagerotate($image_p, 180, 0);
				break;
			case 6:
				$image_p = imagerotate($image_p, -90, 0);
				break;
			case 8:
				$image_p = imagerotate($image_p, 90, 0);
				break;
		}
		// Output
		imagejpeg($image_p, $thumbFile, 90);
	}

	// Das Archiv mit allen Bildern in Original-Auflösung wird ebenfalls beim
	// ersten Zugriff auf die Galerie erstellt. PHP bringt alles mit, was wir dazu
	// benötigen.
	function zip($files = array(), $destination = '', $overwrite = false) {
		if (file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if (is_array($files)) {
			foreach ($files as $file) {
				if (file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if (count($valid_files)) {
			$zip = new ZipArchive();
			if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			$zip->close();	
			return file_exists($destination);
		}
		return false;
	}

	// In SRC Attributen von Tags muss das Space als %20 kodiert sein... nicht als "+" – daher
	// die Anpassung in diesem Teil.
	function srcEncode($path) {
		return preg_replace("/\\+/", "%20", implode("/", array_map("urlencode", explode("/", $path))));
	}

	function beginsWith( $str, $sub ) {
		return ( substr( $str, 0, strlen( $sub ) ) === $sub );
	}
	
	function endsWith( $str, $sub ) {
		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
	}

	function videoThumbnail($path) {
		if (file_exists($path)) {
			$pathinfo	 = pathinfo($path);
			$dirname	 = $pathinfo['dirname'];
			$filename	 = $pathinfo['filename']; // ohne Extension!
			$thumbPath   = "$dirname/$filename.thumb.jpeg";
			if (!file_exists($thumbPath)) {
				$ffmpeg = "/usr/bin/ffmpeg -an -y -ss 0 -vframes 1";
				exec($ffmpeg." -i '".$path."' '".$thumbPath."'");
			}
			return $thumbPath;
		}
	}

?>
