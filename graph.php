<html>
<head>
<meta name="viewport" content="width=device-width">
<style type="text/css">
img {
	width: 98%;
	max-width: 500px;
}
</style>
</head>
<body>
<a href="graph.php?showday">über einen Tag</a> - <a href="graph.php?showweek">über eine Woche</a> - <a href="graph.php?showmonth">über einen Monat</a> - <a href="graph.php?showfloat">gleitender 24h-Durchschnitt</a><br /><br />
<?php

$sensorplaces['esp8266-422191-sds011'] = "Rajko";
$sensorplaces['esp8266-422557-ppd42ns'] = "Rajko";
$sensorplaces['esp8266-422191-ppd42ns'] = "Rajko";
$sensorplaces['esp8266-9136089-sds011'] = "Leonberg (E.T.)";
$sensorplaces['esp8266-10696119-ppd42ns'] = "Stuttgart Vergleichsmessungen";
$sensorplaces['esp8266-13597651-ppd42ns'] = "Hedelfingen/Heumaden";
$sensorplaces['esp8266-13601902-sds011'] = "Rajko";
$sensorplaces['esp8266-13601904-bmp'] = "Rajko";
$sensorplaces['esp8266-13601904-dht'] = "Rajko";
$sensorplaces['esp8266-13601904-ppd42ns'] = "Rajko";
$sensorplaces['esp8266-13601904-sds011'] = "Rajko";
$sensorplaces['esp8266-13597771-dht'] = "Stuttgart Pragsattel";
$sensorplaces['esp8266-13597771-ppd42ns'] = "Stuttgart Pragsattel";
$sensorplaces['esp8266-13927729-ppd42ns'] = "Ostritz bei Görlitz";
$sensorplaces['esp8266-14426623-ppd42ns'] = "Birkach";
$sensorplaces['esp8266-14697627-ppd42ns'] = "OKLab Köln";
$sensorplaces['esp8266-15951095-ppd42ns'] = "Stuttgart Vergleichsmessungen";
$sensorplaces['esp8266-16401996-ppd42ns'] = "Stuttgart Vergleichsmessungen";
$sensorplaces['esp8266-16562418-ppd42ns'] = "Stuttgart Vergleichsmessungen";

if (!file_exists('images')) {
	mkdir('images', 0755, true);
}

if (isset($_GET['sensor'])) {

	if (!file_exists('data/data-'.$_GET['sensor'].'-highres.rrd')) {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		echo "<h2>Sensor nicht gefunden.</h2>";
	} else {
		$sensor = $_GET['sensor'];

		create_graph("images/sensor-".$sensor."-1-day.png", "-1d", "Sensor data over one day", $sensor, 1);
		create_graph("images/sensor-".$sensor."-25-day.png", "-1d", "Sensor data over one day", $sensor, 25);
		create_graph("images/sensor-".$sensor."-1-week.png", "-1w", "Sensor data over one week", $sensor, 1);
		create_graph("images/sensor-".$sensor."-25-week.png", "-1w", "Sensor data over one week", $sensor, 25);
		create_graph("images/sensor-".$sensor."-1-month.png", "-1m", "Sensor data over one month", $sensor, 1);
		create_graph("images/sensor-".$sensor."-25-month.png", "-1m", "Sensor data over one month", $sensor, 25);
		create_graph("images/sensor-".$sensor."-1-year.png", "-1y", "Sensor data over one year", $sensor, 1);
		create_graph("images/sensor-".$sensor."-25-year.png", "-1y", "Sensor data over one year", $sensor, 25);
		create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over 7 days", $sensor, 101);
		create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over 7 days", $sensor, 125);

		echo "<table>";
		echo "<tr><td>";
		echo "<img src='images/sensor-".$sensor."-1-day.png' alt='Sensor data over one day'>";
		echo "</td><td>";
		echo "<img src='images/sensor-".$sensor."-25-day.png' alt='Sensor data over one day'>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "<img src='images/sensor-".$sensor."-1-week.png' alt='Sensor data over one week'>";
		echo "</td><td>";
		echo "<img src='images/sensor-".$sensor."-25-week.png' alt='Sensor data over one week'>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "<img src='images/sensor-".$sensor."-1-month.png' alt='Sensor data over one month'>";
		echo "</td><td>";
		echo "<img src='images/sensor-".$sensor."-25-month.png' alt='Sensor data over one month'>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "<img src='images/sensor-".$sensor."-1-year.png' alt='Sensor data over one year'>";
		echo "</td><td>";
		echo "<img src='images/sensor-".$sensor."-25-year.png' alt='Sensor data over one year'>";
		echo "</td></tr>";
		echo "<tr><td>";
		echo "<img src='images/sensor-".$sensor."-1-floating.png' alt='Floating 24h average over 7 days'>";
		echo "</td><td>";
		echo "<img src='images/sensor-".$sensor."-25-floating.png' alt='Floating 24h average over 7 days'>";
		echo "</td></tr>";
		echo "</table>";
		exit;

	}

} elseif (isset($_GET['showday'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a> (";
		if (isset($sensorplaces[$sensor])) echo $sensorplaces[$sensor];
		echo ")<br />\n";
		create_graph("images/sensor-".$sensor."-1-day.png", "-1d", "Sensor data over one day (high res)",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-day.png", "-1d", "Sensor data over one day (high res)",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-day.png' alt='Sensor data over one day'>
		<img src='images/sensor-".$sensor."-25-day.png' alt='Sensor data over one day'><br /><br /><br />";
	}

} elseif (isset($_GET['showweek'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a> (";
		if (isset($sensorplaces[$sensor])) echo $sensorplaces[$sensor];
		echo ")<br />\n";
		create_graph("images/sensor-".$sensor."-1-week.png", "-1w", "Sensor data over one week",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-week.png", "-1w", "Sensor data over one week",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-week.png' alt='Sensor data over one week'>
		<img src='images/sensor-".$sensor."-25-week.png' alt='Sensor data over one week'><br /><br /><br />";
	}

} elseif (isset($_GET['showmonth'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a> (";
		if (isset($sensorplaces[$sensor])) echo $sensorplaces[$sensor];
		echo ")<br />\n";
		create_graph("images/sensor-".$sensor."-1-month.png", "-1m", "Sensor data over one month",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-month.png", "-1m", "Sensor data over one month",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-month.png' alt='Sensor data over one month'>
		<img src='images/sensor-".$sensor."-25-month.png' alt='Sensor data over one month'><br /><br /><br />";
	}

} elseif (isset($_GET['showfloat'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a> (";
		if (isset($sensorplaces[$sensor])) echo $sensorplaces[$sensor];
		echo ")<br />\n";
		create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,101);
		create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,125);
		echo "<img src='images/sensor-".$sensor."-1-floating.png' alt='Floating 24h average over 7 days'>
		<img src='images/sensor-".$sensor."-25-floating.png' alt='Floating 24h average over 7 days'><br /><br /><br />";
	}

} else {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a> (";
		if (isset($sensorplaces[$sensor])) echo $sensorplaces[$sensor];
		echo ")<br />\n";
	}

}

function create_graph($output, $start, $title, $sensor, $option_nr) {

	if (strpos($sensor,"ppd42ns")) {
		$unit1 = "Partikel / Liter ";
		$unit2 = "Partikel / Liter";
		$ds1 = "PMone"; $ds1_title = "PM1";
		$ds2 = "PMtwo"; $ds2_title = "PM2.5";
	} else if (strpos($sensor,"sds011")) {
		$unit1 = "µg / m³";
		$unit2 = "µg / m³";
		$ds1 = "PMone"; $ds1_title = "PM10";
		$ds2 = "PMtwo"; $ds2_title = "PM2.5";
	} else if (strpos($sensor,"dht")) {
		$unit1 = "° Celsius";
		$unit2 = "%";
		$ds1 = "temperature"; $ds1_title = "Temperatur";
		$ds2 = "humidity"; $ds2_title = "rel. Luftfeuchte";
	} else if (strpos($sensor,"bmp")) {
		$unit1 = "° Celsius";
		$unit2 = "Pascal";
		$ds1 = "temperature"; $ds1_title = "Temperatur";
		$ds2 = "pressure"; $ds2_title = "Luftdruck";
	}

	$options = array(
		"--start", $start,
		"--title=$title",
		"--lower=0",
		"-w 500",
		"-h 250",
	);

	if ($option_nr === 1) {
		array_push($options,"--vertical-label=$unit1");
		array_push($options,"DEF:DSone=data/data-$sensor-highres.rrd:$ds1:AVERAGE:step=30");
		array_push($options,"CDEF:avgDSone=DSone,300,TRENDNAN"); // 5 min (300 sec) floating average
		array_push($options,"LINE1:avgDSone#FF0000:$ds1_title");
	} else if ($option_nr === 25) {
		array_push($options,"--vertical-label=$unit2");
		array_push($options,"DEF:DStwo=data/data-$sensor-highres.rrd:$ds2:AVERAGE:step=30");
		array_push($options,"CDEF:avgDStwo=DStwo,300,TRENDNAN"); // 5 min (300 sec) floating average
		array_push($options,"LINE1:avgDStwo#0000FF:$ds2_title");
	} else if ($option_nr === 101) {
		array_push($options,"--vertical-label=$unit1");
		array_push($options,"DEF:DSone=data/data-$sensor-highres.rrd:$ds1:AVERAGE:step=30");
		array_push($options,"CDEF:avgDSone=DSone,86400,TRENDNAN"); // 24h (24 * 60 * 60 = 86400 sec) floating average
		array_push($options,"LINE1:avgDSone#FF0000:$ds1_title");
	} else if ($option_nr === 125) {
		array_push($options,"--vertical-label=$unit2");
		array_push($options,"DEF:DStwo=data/data-$sensor-highres.rrd:$ds2:AVERAGE:step=30");
		array_push($options,"CDEF:avgDStwo=DStwo,86400,TRENDNAN"); // 24h (24 * 60 * 60 = 86400 sec) floating average
		array_push($options,"LINE1:avgDStwo#0000FF:$ds2_title");
	}

	$ret = rrd_graph($output, $options);

	if (! $ret) {
		echo "<b>Graph error: </b>".rrd_error()."\n";
	}
}
?>
<br />
<br />
hosted @ <a href="https://www.madavi.de">madavi.de</a>
</body>
