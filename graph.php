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
	create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over one day",$sensor,101);
	create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over one day",$sensor,125);

	echo "<table>";
	echo "<tr><td>";
	echo "<img src='images/sensor-".$sensor."-1-day.png' alt='Generated RRD image'>";
	echo "</td><td>";
	echo "<img src='images/sensor-".$sensor."-25-day.png' alt='Generated RRD image'>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo "<img src='images/sensor-".$sensor."-1-week.png' alt='Generated RRD image'>";
	echo "</td><td>";
	echo "<img src='images/sensor-".$sensor."-25-week.png' alt='Generated RRD image'>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo "<img src='images/sensor-".$sensor."-1-month.png' alt='Generated RRD image'>";
	echo "</td><td>";
	echo "<img src='images/sensor-".$sensor."-25-month.png' alt='Generated RRD image'>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo "<img src='images/sensor-".$sensor."-1-year.png' alt='Generated RRD image'>";
	echo "</td><td>";
	echo "<img src='images/sensor-".$sensor."-25-year.png' alt='Generated RRD image'>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo "<img src='images/sensor-".$sensor."-1-floating.png' alt='Generated RRD image'>";
	echo "</td><td>";
	echo "<img src='images/sensor-".$sensor."-25-floating.png' alt='Generated RRD image'>";
	echo "</td></tr>";
	echo "</table>";
	exit;

} elseif (isset($_GET['showday'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-day.png", "-1d", "Sensor data over one day (high res)",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-day.png", "-1d", "Sensor data over one day (high res)",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-day.png' alt='Generated RRD image'>
<img src='images/sensor-".$sensor."-25-day.png' alt='Generated RRD image'>
<br /><br /><br />";
	}

} elseif (isset($_GET['showweek'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-week.png", "-1w", "Sensor data over one week",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-week.png", "-1w", "Sensor data over one week",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-week.png' alt='Generated RRD image'> <img src='images/sensor-".$sensor."-25-week.png' alt='Generated RRD image'><br /><br /><br />";
	}

} elseif (isset($_GET['showmonth'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-month.png", "-1m", "Sensor data over one month",$sensor,1);
		create_graph("images/sensor-".$sensor."-25-month.png", "-1m", "Sensor data over one month",$sensor,25);
		echo "<img src='images/sensor-".$sensor."-1-month.png' alt='Generated RRD image'> <img src='images/sensor-".$sensor."-25-month.png' alt='Generated RRD image'><br /><br /><br />";
	}

} elseif (isset($_GET['showfloat'])) {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
		create_graph("images/sensor-".$sensor."-1-floating.png", "-8d", "Floating 24h average over one day",$sensor,101);
		create_graph("images/sensor-".$sensor."-25-floating.png", "-8d", "Floating 24h average over one day",$sensor,125);
		echo "<img src='images/sensor-".$sensor."-1-floating.png' alt='Generated RRD image'> <img src='images/sensor-".$sensor."-25-floating.png' alt='Generated RRD image'><br /><br /><br />";
	}

} else {

	foreach (glob("data/*-highres.rrd") as $filename) {
		$sensor = substr($filename,10,-12);
		echo "<a href='graph.php?sensor=".$sensor."'>".$sensor."</a><br />\n";
	}

}

function create_graph($output, $start, $title, $sensor, $option_nr) {
	$options = array(
//		"--slope-mode",
		"--start", $start,
//		"--start", "e".$start,
//		"--end", "19:00 20160101",
		"--title=$title",
		"--vertical-label=Partikel / Liter",
		"--lower=0",
		"-w 500",
		"-h 250",
	);

	if ($option_nr === 1) {
		array_push($options,"DEF:PMone=data/data-".$sensor."-highres.rrd:PMone:AVERAGE:step=30");
		array_push($options,"CDEF:v1=PMone");
		array_push($options,"CDEF:v2=PREV(v1)");
		array_push($options,"CDEF:v3=PREV(v2)");
		array_push($options,"CDEF:v4=PREV(v3)");
		array_push($options,"CDEF:v5=PREV(v4)");
		array_push($options,"CDEF:avg1PMone=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP");
//		array_push($options,"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/");
//		array_push($options,"CDEF:avg2PMone=v1,1000,*,283,/");
//		array_push($options,"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"); // Median over 5 values
		array_push($options,"CDEF:avg2PMone=PMone,300,TRENDNAN"); // 24h average
//		array_push($options,"LINE1:PMone#FFFF00:'PM1'");
//		array_push($options,"LINE1:avg1PMone#FF0000:'PM1'");
		array_push($options,"LINE1:avg2PMone#FF0000:'PM1'");
	} else if ($option_nr === 25) {
		array_push($options,"DEF:PMtwo=data/data-".$sensor."-highres.rrd:PMtwo:AVERAGE:step=30");
		array_push($options,"CDEF:v1=PMtwo");
		array_push($options,"CDEF:v2=PREV(v1)");
		array_push($options,"CDEF:v3=PREV(v2)");
		array_push($options,"CDEF:v4=PREV(v3)");
		array_push($options,"CDEF:v5=PREV(v4)");
		array_push($options,"CDEF:avg1PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP");
//		array_push($options,"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/");
//		array_push($options,"CDEF:avg2PMtwo=v1,1000,*,283,/");
//		array_push($options,"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"); // Median over 5 values
		array_push($options,"CDEF:avg2PMtwo=PMtwo,300,TRENDNAN"); // 24h average
//		array_push($options,"LINE1:PMtwo#FFFF00:'PM2.5'");
//		array_push($options,"LINE1:avg1PMtwo#FF0000:'PM2.5'");
		array_push($options,"LINE1:avg2PMtwo#0000FF:'PM2.5'");
	} else if ($option_nr === 101) {
		array_push($options,"DEF:PMone=data/data-".$sensor."-highres.rrd:PMone:AVERAGE:step=30");
		array_push($options,"CDEF:v1=PMone");
		array_push($options,"CDEF:v2=PREV(v1)");
		array_push($options,"CDEF:v3=PREV(v2)");
		array_push($options,"CDEF:v4=PREV(v3)");
		array_push($options,"CDEF:v5=PREV(v4)");
//		array_push($options,"CDEF:avg1PMone=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP");
		array_push($options,"CDEF:avg1PMone=v1,v2,v3,3,SORT,POP,2,REV,POP");
		array_push($options,"CDEF:avg2PMone=PMone,86400,TRENDNAN"); // 24h average
		array_push($options,"LINE1:avg2PMone#FF0000:'PM1'");
	} else if ($option_nr === 125) {
		array_push($options,"DEF:PMtwo=data/data-".$sensor."-highres.rrd:PMtwo:AVERAGE:step=30");
		array_push($options,"CDEF:v1=PMtwo");
		array_push($options,"CDEF:v2=PREV(v1)");
		array_push($options,"CDEF:v3=PREV(v2)");
		array_push($options,"CDEF:v4=PREV(v3)");
		array_push($options,"CDEF:v5=PREV(v4)");
		array_push($options,"CDEF:avg1PMtwo=v1,v2,v3,3,SORT,POP,2,REV,POP");
		array_push($options,"CDEF:avg2PMtwo=PMtwo,86400,TRENDNAN"); // 24h average
		array_push($options,"LINE1:avg2PMtwo#0000FF:'PM2.5'");
	}

//	if ($start === '-1d') {
//		array_push($options,"--rigid");
//		if ($option_nr == 1) {
//			array_push($options,"--upper-limit=150000");
//		} else {
//			array_push($options,"--upper-limit=5000");
//		} 
//	}

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
