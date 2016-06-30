<html>
<head>
</head>
<body>
<?php
if ($_GET['sensor']) {

// view files (per date) for selected sensor

	$sensor = $_GET['sensor'];

	foreach (glob("data/data-$sensor-*.csv") as $filename) {
		$sensor = substr($filename,10,-15);
		echo "<a href='csvfiles.php?sensor=$sensor'>$sensor</a> - Datei: <a href='$filename'>".substr($filename,5)."</a><br />\n";
	}

} else {

// view list of available sensors with saved data

	echo "<a href='get_zip.php'>Alle Daten als Zip-Datei</a><br /><br />\n";

	$last_sensor = "";
	foreach (glob("data/*.csv") as $filename) {
		$sensor = substr($filename,10,-15);
		if ($sensor != $last_sensor) {
			echo "<a href='csvfiles.php?sensor=$sensor'>$sensor</a><br />\n";
			$last_sensor = $sensor;
		}
	}
}

?>
<br />
<br />
hosted @ <a href="https://www.madavi.de">madavi.de</a>
</body>
