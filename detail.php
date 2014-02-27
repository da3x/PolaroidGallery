<?php include 'includes/redirect.php'; ?>
<?php include 'includes/functions.php'; ?>
<?php include 'includes/data.php'; ?>
<?php include 'includes/cookies.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo(language()); ?>">
	<head>
		<meta charset="utf-8" />
		
		<title>★ <?php echo(str("title")); ?> | <?php echo(getTimeSpan()); ?> | <?php echo($_GET['file']); ?> ★</title>

		<link rel="stylesheet" href="/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/css/iOS.css" type="text/css" />
		
		<link href='http://fonts.googleapis.com/css?family=Indie+Flower' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Rock+Salt' rel='stylesheet' type='text/css' />
	</head>
	<body>
		<div class="detail">
			<h1><a href="/show/<?php echo($_GET['gallery']); ?>" id="home"><?php echo(str("title")); ?> — <?php echo(getTimeSpan()); ?></a></h1>

<?php
			// Wir bestimmten erstmal schnell den Vorgänger und Nachfolger für's Blättern...
			$self = null; $prev = null; $next = null; $prevLarge = null; $nextLarge = null;
			foreach ($files as $path) {
				if (endsWith($path, "/".$_GET['file'])) {
					$self = $path;
				}
				if ($self == null) $prev = $path;
				else if ($next == null && $self != $path) {
					$next = $path;
					break;
				}
			}
			if ($prev == null) $prev = end($files);
			if ($next == null) $next = reset($files);
			if ($prev != null) {
				$prevPI = pathinfo($prev);
				$prevLarge = "/".$prevPI['dirname']."/".$prevPI['filename'].".large.jpeg";
			}
			if ($next != null) {
				$nextPI = pathinfo($next);
				$nextLarge = "/".$nextPI['dirname']."/".$nextPI['filename'].".large.jpeg";
			}
			
			$path        = $self; // "galleries/".$_GET['gallery']."/".$_GET['file'];
			$pathinfo	 = pathinfo($path);
			$dirname	 = $pathinfo['dirname'];
			$filename	 = $pathinfo['filename']; // ohne Extension!
			$thumbPath   = "$dirname/$filename.thumb.jpeg";
			$largePath   = "$dirname/$filename.large.jpeg";
			$zipPath	 = "$dirname/Archiv.zip";

			$markers     = "";
			$gmapmarkers = "gmap.addMarker({ lat: 52.49902, lng: 13.47913, title: 'Home', infoWindow: { content: '<p>Home sweet Home...</p>' } });\n";
			$lat         = exif_lat($path);
			$lon         = exif_lon($path);
			if ($lat != 0 && $lon != 0) {
				$markers	 = $markers.$lat.",".$lon."|";
				$gmapmarkers = $gmapmarkers."gmap.addMarker({ lat: ".$lat.", lng: ".$lon.", title: '".$filename."', infoWindow: { content: '<p>Hier wurde das Bild aufgenommen...</p>' } });\n";
			}

			$orientation = exif_orientation($path);
			$date		 = date("d.m.Y", strtotime(exif_date($path)));
			if (language() == "en") {
				$date		 = date("m/d/Y", strtotime(exif_date($path)));
			}
			$time		 = date("H:i",   strtotime(exif_date($path)));
			if (!file_exists($zipPath))   zip($files, $zipPath);
			if (!file_exists($thumbPath)) resample($path, $thumbPath, 250, $orientation);
			if (!file_exists($largePath)) resample($path, $largePath, 1500, $orientation);
?>
			<div class="polaroid">
				<a href="/detail/<?php echo($_GET['gallery']); ?><?php echo(strrchr($prev, "/")); ?>" class="prev" id="prev">{</a>
				<a href="/detail/<?php echo($_GET['gallery']); ?><?php echo(strrchr($next, "/")); ?>" class="next" id="next">}</a>
				<img src="/<?php echo(srcEncode($largePath)); ?>" alt="Preview" />
				<span><?php echo($_GET['file']); ?> — <?php echo($date); ?> — <?php echo($time); ?></span>
			</div>

<?php
	// Die Karte geben wir nur aus, wenn auch GPS Koordinaten gefunden wurden.
	if (strstr($markers,",")) {
?>
			<div class="map">
				<div id="gmap">
					<img src="http://maps.google.com/maps/api/staticmap?size=640x480&maptype=roadmap&sensor=false&markers=color:green|label:H|Berlin+Deutschland&markers=color:red|<?php echo($markers); ?>" alt="Map" onclick="activateMap();" />
				</div>
			</div>
<?php
	}
?>

			<div class="download">
				<a href="/<?php echo(srcEncode($path)); ?>" title="Download…">
					<img src="/design/box.png" alt="Download" />
				</a>
			</div>

			<div class="footer">
				<p><?php echo(str("p.footer")); ?></p>
			</div>
			<div class="ribbon">
				<span><?php echo(str("span.ribbon")); ?></span>
			</div>
			
		</div>
	
		<!-- Das ganze JavaScript am Ende, weil die Seite dann schneller lädt. -->
		<script src="/js/jquery-1.7.1.min.js"></script>
		<script>
			$(document).ready(function(){
				$("<img />").attr("src", "<?php echo($prevLarge); ?>"); /* preload */
				$("<img />").attr("src", "<?php echo($nextLarge); ?>"); /* preload */
			});
			$(document).keydown(function(e){
				if (e.keyCode == 27) { 
					window.location = $("#home").attr("href");
					return false;
				}
				if (e.keyCode == 37) { 
					window.location = $("#prev").attr("href");
					return false;
				}
				if (e.keyCode == 39) { 
					window.location = $("#next").attr("href");
					return false;
				}
			});
		</script>

<?php
	if (strstr($markers,",")) {
?>
		<!-- Interactive Google Map -->
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="/js/gmaps.js"></script>
		<script type="text/javascript">
			function activateMap() {
				var gmap = new GMaps({
					div: '#gmap',
					lat: 52.49902,
					lng: 13.47913
				});
				<?php echo($gmapmarkers); ?>
				gmap.fitZoom();
			};
		</script>
<?php
	}
?>

		<!-- Google Analytics -->
		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-1111111-11']);
		  _gaq.push(['_setDomainName', 'domain.com']);
		  _gaq.push(['_trackPageview']);
		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>
		<!-- Google Analytics -->
		
	</body>
</html>
