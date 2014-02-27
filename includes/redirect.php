<?php
		// Im Fehlerfall machen wir als erstes mal gleich die Weiterleitung
		// auf eine andere Seite... eine nicht existente Gallerie versuchen
		// wir gar nicht erst anzuzeigen.
		if (!isset($_GET['gallery']) || !$_GET['gallery'] || !is_dir("galleries/".$_GET['gallery'])) {
				header('HTTP/1.0 404 Not Found');
				header('Location: /error/404');
				exit();
		}
		if (isset($_GET['file']) && $_GET['file'] && !is_file("galleries/".$_GET['gallery']."/".$_GET['file'])) {
				header('HTTP/1.0 404 Not Found');
				header('Location: /error/404');
				exit();
		}
?>
