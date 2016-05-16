<html>
<head>
</head>
<body>
<a href="graph.php?showday">über einen Tag</a> - <a href="graph.php?showweek">über eine Woche</a> - <a href="graph.php?showmonth">über einen Monat</a> - <a href="graph.php?showfloat">gleitender 24h-Durchschnitt</a><br /><br />
<?php

if (!file_exists('images')) {
	mkdir('images', 0755, true);
}

if ($_GET['sensor']) {

	$sensor = $_GET['sensor'];

	create_graph("images/sensor-".$sensor."-1-day.png", "-1d", "Sensor data over one day",$sensor,1);
	create_graph("images/sensor-".$sensor."-25-day.png", "-1d", "Sensor data over one day",$sensor,25);
	create_graph("images/sensor-".$sensor."-1-week.png", "-1w", "Sensor data over one week",$sensor,1);
	create_graph("images/sensor-".$sensor."-25-week.png", "-1w", "Sensor data over one week",$sensor,25);
	create_graph("images/sensor-".$sensor."-1-month.png", "-1m", "Sensor data over one month",$sensor,1);
	create_graph("images/sensor-".$sensor."-25-month.png", "-1m", "Sensor data over one month",$sensor,25);
	create_graph("images/sensor-".$sensor."-1-year.png", "-1y", "Sensor data over one year",$sensor,1);
	create_graph("images/sensor-".$sensor."-25-year.png", "-1y", "Sensor data over one year",$sensor,25);
	create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,101);
	create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,125);

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

} elseif (isset($_GET['showday'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-day.png", "-1d", "Sensor data over one day (high res)",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-day.png", "-1d", "Sensor data over one day (high res)",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-day.png' alt='Sensor data over one day'>
		<img src='images/sensor-".$sensor."-25-day.png' alt='Sensor data over one day'><br /><br /><br />";
	}

} elseif (isset($_GET['showweek'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-week.png", "-1w", "Sensor data over one week",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-week.png", "-1w", "Sensor data over one week",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-week.png' alt='Sensor data over one week'>
		<img src='images/sensor-".$sensor."-25-week.png' alt='Sensor data over one week'><br /><br /><br />";
	}

} elseif (isset($_GET['showmonth'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-month.png", "-1m", "Sensor data over one month",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-month.png", "-1m", "Sensor data over one month",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-month.png' alt='Sensor data over one month'>
		<img src='images/sensor-".$sensor."-25-month.png' alt='Sensor data over one month'><br /><br /><br />";
	}

} elseif (isset($_GET['showfloat'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,101);
		create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over 7 days",$sensor,125);
		echo "<img src='images/sensor-".$sensor."-1-floating.png' alt='Floating 24h average over 7 days'>
		<img src='images/sensor-".$sensor."-25-floating.png' alt='Floating 24h average over 7 days'><br /><br /><br />";
	}

} else {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
	}

}

function create_graph($output, $start, $title, $sensor, $option_nr) {
	$options = array(
		"--start", $start,
		"--title=$title",
		"--vertical-label=Partikel / Liter",
		"--lower=0",
		"-w 500",
		"-h 250",
	);

	if ($option_nr === 1) {
		array_push($options,"DEF:PMone=data/data-".$sensor."-highres.rrd:PMone:AVERAGE:step=30");
		array_push($options,"CDEF:avgPMone=PMone,300,TRENDNAN"); // 5 min (300 sec) floating average
		array_push($options,"LINE1:avgPMone#FF0000:'PM1'");
	} else if ($option_nr === 25) {
		array_push($options,"DEF:PMtwo=data/data-".$sensor."-highres.rrd:PMtwo:AVERAGE:step=30");
		array_push($options,"CDEF:avgPMtwo=PMtwo,300,TRENDNAN"); // 5 min (300 sec) floating average
		array_push($options,"LINE1:avgPMtwo#0000FF:'PM2.5'");
	} else if ($option_nr === 101) {
		array_push($options,"DEF:PMone=data/data-".$sensor."-highres.rrd:PMone:AVERAGE:step=30");
		array_push($options,"CDEF:avgPMone=PMone,86400,TRENDNAN"); // 24h (24 * 60 * 60 = 86400 sec) floating average
		array_push($options,"LINE1:avgPMone#FF0000:'PM1'");
	} else if ($option_nr === 125) {
		array_push($options,"DEF:PMtwo=data/data-".$sensor."-highres.rrd:PMtwo:AVERAGE:step=30");
		array_push($options,"CDEF:avgPMtwo=PMtwo,86400,TRENDNAN"); // 24h (24 * 60 * 60 = 86400 sec) floating average
		array_push($options,"LINE1:avgPMtwo#0000FF:'PM2.5'");
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
