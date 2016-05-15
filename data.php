<?php

$headers = apache_request_headers();
$json = file_get_contents('php://input');

$results = json_decode($json,true);

$now = gmstrftime("%Y/%m/%d %H:%M:%S");
$today = gmstrftime("%Y-%m-%d");

foreach ($results["sensordatavalues"] as $sensordatavalues) {
	$values[$sensordatavalues["value_type"]] = $sensordatavalues["value"];
}

print "Sensor: ".$headers['Sensor']."\r\n";
print "P1: ".$values["P1"]."\r\n";
print "P2: ".$values["P2"]."\r\n";
print "temp: ".$values["temp"]."\r\n";
print "humidity: ".$values["humidity"]."\r\n";
print "SDS_P1: ".$values["SDS_P1"]."\r\n";
print "SDS_P2: ".$values["SDS_P2"]."\r\n";
print "Samples: ".$values["samples"]."\r\n";
print "Min cycle: ".$values["min_micro"]."\r\n";
print "Max cycle: ".$values["max_micro"]."\r\n";

if (!file_exists('data')) {
	mkdir('data', 0755, true);
}

$datafile = "data/data-".$headers['Sensor'].".rrd";

if (!file_exists($datafile)) {
	$opts = array(
		"--step", "30", "--start", time(),
		"DS:PMone:GAUGE:45:U:U",
		"DS:PMtwo:GAUGE:45:U:U",
		"RRA:AVERAGE:0.5:10:288",
		"RRA:AVERAGE:0.5:120:168",
		"RRA:AVERAGE:0.5:2880:365",
	);
	$ret = rrd_create($datafile, $opts);
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Creation error: </b> $err\n";
	}
}

$update_string = time().":";
if ($values["ratioP1"] < 15) { $update_string .= $values["P1"]; }
$update_string .= ":";
if ($values["ratioP2"] < 15) { $update_string .= $values["P2"]; }

print $update_string."\r\n";
$ret = rrd_update($datafile,array($update_string));
if (! $ret) {
	$err = rrd_error();
	echo "<b>Update error: </b> $err\n";
}

$datafile = "data/data-".$headers['Sensor']."-highres.rrd";

if (!file_exists($datafile)) {
	$opts = array(
		"--step", "30", "--start", time(),
		"DS:PMone:GAUGE:55:U:U",
		"DS:PMtwo:GAUGE:55:U:U",
		"RRA:AVERAGE:0.5:1:25920",
		"RRA:AVERAGE:0.5:30:672",
		"RRA:AVERAGE:0.5:720:1460",
	);
	$ret = rrd_create($datafile, $opts);
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Creation error: </b> $err\n";
	}
}

$ret = rrd_update($datafile,array($update_string));
if (! $ret) {
	$err = rrd_error();
	echo "<b>Update error: </b> $err\n";
}

if (isset($values["SDS_P1"]) || isset($values["SDS_P2"])) {

	$update_string = time().":".$values["SDS_P1"].":".$values["SDS_P2"];

	$datafile = "data/data-SDS".$headers['Sensor']."-highres.rrd";

	if (!file_exists($datafile)) {
		$opts = array(
			"--step", "30", "--start", time(),
			"DS:PMone:GAUGE:55:U:U",
			"DS:PMtwo:GAUGE:55:U:U",
			"RRA:AVERAGE:0.5:1:2880",
			"RRA:AVERAGE:0.5:30:672",
			"RRA:AVERAGE:0.5:720:1460",
		);
		$ret = rrd_create($datafile, $opts);
		if (! $ret) {
			$err = rrd_error();
			echo "<b>Creation error: </b> $err\n";
		}
	}

	$ret = rrd_update($datafile,array($update_string));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

if (isset($values["min_micro"]) || isset($values["max_micro"])) {

	$update_string = time().":".$values["min_micro"].":".$values["max_micro"];

	$datafile = "data/data-".$headers['Sensor']."-time.rrd";

	if (!file_exists($datafile)) {
		$opts = array(
			"--step", "30", "--start", time(),
			"DS:minmicro:GAUGE:55:U:U",
			"DS:maxmicro:GAUGE:55:U:U",
			"RRA:MIN:0.5:1:2880",
			"RRA:MAX:0.5:1:2880",
			"RRA:MIN:0.5:30:672",
			"RRA:MAX:0.5:30:672",
			"RRA:MIN:0.5:720:1460",
			"RRA:MAX:0.5:720:1460",
		);
		$ret = rrd_create($datafile, $opts);
		if (! $ret) {
			$err = rrd_error();
			echo "<b>Creation error: </b> $err\n";
		}
	}

	$ret = rrd_update($datafile,array($update_string));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

$datafile = "data/data-".$headers['Sensor']."-".$today.".csv";

if (!file_exists($datafile)) {
	$outfile = fopen($datafile,"a");
	fwrite($outfile,"Time;durP1;ratioP1;P1;durP2;ratioP2;P2;SDS_P1;SDS_P2;Temp;Humidity;Samples;Min_cycle;Max_cycle\n");
	fclose($outfile);
}
$outfile = fopen($datafile,"a");
fwrite($outfile,$now.";".$values["durP1"].";".$values["ratioP1"].";".$values["P1"].";".$values["durP2"].";".$values["ratioP2"].";".$values["P2"].";".$values["SDS_P1"].";".$values["SDS_P2"].";".$values["temp"].";".$values["humidity"].";".$values["samples"].";".$values["min_micro"].";".$values["max_micro"]."\n");
fclose($outfile);

?>
ok
