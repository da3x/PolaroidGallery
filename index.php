<?php include 'includes/redirect.php'; ?>
<?php include 'includes/functions.php'; ?>
<?php include 'includes/data.php'; ?>
<?php include 'includes/cookies.php'; ?>
<?php include 'includes/comments.php'; ?>
<!DOCTYPE html>
<html lang="<?php echo(language()); ?>">
	<head>
		<meta charset="utf-8" />
		
		<title>★ <?php echo(str("title")); ?> | <?php echo(getTimeSpan()); ?> ★</title>

		<link rel="stylesheet" href="/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/css/iOS.css" type="text/css" />
		
		<link href='http://fonts.googleapis.com/css?family=Indie+Flower' rel='stylesheet' type='text/css' />
		<link href='http://fonts.googleapis.com/css?family=Rock+Salt' rel='stylesheet' type='text/css' />
	</head>
	<body>
		<div class="wrapper">
			<h1><?php echo(str("title")); ?> — <?php echo(getTimeSpan()); ?></h1>
<?php
	if (count($files) > 0) {
?>
			<h2><?php echo(str("h2.images")); ?></h2>
			<ul class="gallery polaroids">
<?php
		$markers = "";
		$gmapmarkers = "gmap.addMarker({ lat: 52.49902, lng: 13.47913, title: 'Home', infoWindow: { content: '<p>Home sweet Home...</p>' } });\n";
		foreach ($files as $path) {
			$pathinfo	 = pathinfo($path);
			$dirname	 = $pathinfo['dirname'];
			$basename	 = $pathinfo['basename']; // mit Extension!
			$filename	 = $pathinfo['filename']; // ohne Extension!
			$thumbPath   = "$dirname/$filename.thumb.jpeg";
			$largePath   = "$dirname/$filename.large.jpeg";
			$zipPath	 = "$dirname/Archiv.zip";
			$lat         = exif_lat($path);
			$lon         = exif_lon($path);
			if ($lat != 0 && $lon != 0) {
				$markers	 = $markers.$lat.",".$lon."|";
				$gmapmarkers = $gmapmarkers."gmap.addMarker({ lat: ".$lat.", lng: ".$lon.", title: '".$filename."', infoWindow: { content: '<img src=\"/".srcEncode($thumbPath)."\" />' } });\n";
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
				<li>
					<a href="/detail/<?php echo($_GET['gallery']); ?>/<?php echo($basename); ?>" title="<?php echo($basename); ?>">
						<img src="/<?php echo(srcEncode($thumbPath)); ?>" alt="Thumbnail" />
					</a>
					<span><?php echo($date); ?> — <?php echo($time); ?></span>
				</li>
<?php
		}
?>
			</ul>
<?php
	}
?>
<?php
	// Die Karte geben wir nur aus, wenn auch GPS Koordinaten gefunden wurden.
	if (strstr($markers,",")) {
?>
			<div class="map">
				<h2><?php echo(str("h2.map")); ?></h2>
				<div id="gmap">
					<img src="http://maps.google.com/maps/api/staticmap?size=640x480&maptype=roadmap&sensor=false&markers=color:green|label:H|Berlin+Deutschland&markers=color:red|<?php echo($markers); ?>" alt="Map" onclick="activateMap();" />
				</div>
			</div>
<?php
	}
?>

<?php
	$videos = glob("{galleries/".$_GET['gallery']."/*.mov,galleries/".$_GET['gallery']."/*.mp4,galleries/".$_GET['gallery']."/*.m4v}",GLOB_BRACE);
	if (count($videos) > 0) {
?>
			<div class="video">
				<h2><?php echo(str("h2.video")); ?></h2>
<?php
		foreach ($videos as $path) {
?>
				<video controls="controls" preload="none" poster="/<?php echo(srcEncode(videoThumbnail($path))); ?>">
					<source src="/<?php echo(srcEncode($path)); ?>" type="video/mp4" />
					<em><?php echo(str("no.video")); ?></em>
				</video>
				<a href="/<?php echo(srcEncode($path)); ?>">Download (<?php echo(round(filesize($path)/(1024*1024),0)); ?> MB)</a>
<?php
		}
?>
			</div>
<?php
	}
?>

<?php
	if (file_exists($zipPath)) {
?>
			<div class="download">
				<p><?php echo(str("p.download")); ?></p>
				<a href="/<?php echo(srcEncode($zipPath)); ?>" title="Download…">
					<img src="/design/box.png" alt="Download" />
				</a>
			</div>
<?php
	}
?>

			<div class="footer">
				<p><?php echo(str("p.footer")); ?></p>
			</div>
			<div class="ribbon">
				<span><?php echo(str("span.ribbon")); ?></span>
			</div>


		</div>

		<div class="comments">
			<ol>
				<h2><?php echo(str("h2.comments")); ?></h2>
<?php
	foreach (loadComments() as $comment) {
?>				
				<li>
					<label><?php echo($comment['name']); ?></label>
					<time datetime="<?php echo(date('Y-m-d\TH:iP', $comment['timestamp'])); ?>"><?php echo(date('\a\m d.m.Y \u\m G:i', $comment['timestamp'])); ?></time>
<?php
	if ($cID == $comment['userID']) {
?>
					<a href="/index.php?gallery=<?php echo($_GET['gallery']); ?>&delete=<?php echo($comment['commentID']); ?>" onclick="return confirm('<?php echo(str("comment.delete.confirm")); ?>');" title="<?php echo(str("comment.delete.title")); ?>">X</a>
<?php
	}
?>
					<p><?php echo($comment['comment']); ?></p>
					<!--<span >...zu IMG_1234.jpg</span>-->
				</li>
<?php
	}
?>
			</ol>
			<form action="/index.php" method="get">
				<input type="hidden" name="gallery" value="<?php echo($_GET['gallery']); ?>" />
				<input type="text" name="name" value="<?php echo($cName); ?>" placeholder="Name" />
				<textarea name="comment" placeholder="<?php echo(str("comment.placeholder.text")); ?>"></textarea>
				<input type="submit" value="<?php echo(str("comment.submit")); ?>" />
			</form>
		</div>

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
					lat: 52.30,
					lng: 13.25
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
