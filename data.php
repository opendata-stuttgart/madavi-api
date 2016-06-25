<?php

$headers['Sensor'] = $_SERVER['HTTP_SENSOR'];
$json = file_get_contents('php://input');

$results = json_decode($json,true);

$now = gmstrftime("%Y/%m/%d %H:%M:%S");
$today = gmstrftime("%Y-%m-%d");

// copy sensor data values to values array
foreach ($results["sensordatavalues"] as $sensordatavalues) {
	$values[$sensordatavalues["value_type"]] = $sensordatavalues["value"];
}

// check for used sensors
if (isset($values["durP1"]) && isset($values["durP2"])) { $has_ppd42ns = 1; } else { $has_ppd42ns = 0; }
if (((! isset($values["durP1"])) && isset($values["P1"])) || (isset($values["SDS_P1"]) && isset($values["SDS_P2"]))) { $has_sds011 = 1; } else { $has_sds011 = 0; }
if (isset($values["temperature"]) && isset($values["humidity"])) { $has_dht = 1; } else { $has_dht = 0; }
if (isset($values["BMP_temperature"]) && isset($values["BMP_pressure"])) { $has_bmp = 1; } else { $has_bmp = 0; }

// print transmitted values
echo "Sensor: ".$headers['Sensor']."\r\n";
if (isset($values["P1"])) echo "P1: ".$values["P1"]."\r\n";
if (isset($values["P2"])) echo "P2: ".$values["P2"]."\r\n";
if (isset($values["temperature"])) echo "DHT temp.: ".$values["temperature"]."\r\n";
if (isset($values["humidity"])) echo "DHT humidity: ".$values["humidity"]."\r\n";
if (isset($values["BMP_pressure"])) echo "BMP pressure: ".$values["BMP_pressure"]."\r\n";
if (isset($values["BMP_temperature"])) echo "BMP temp.: ".$values["BMP_temperature"]."\r\n";
if (isset($values["SDS_P1"])) echo "SDS_P1: ".$values["SDS_P1"]."\r\n";
if (isset($values["SDS_P2"])) echo "SDS_P2: ".$values["SDS_P2"]."\r\n";
if (isset($values["samples"])) echo "Samples: ".$values["samples"]."\r\n";
if (isset($values["min_micro"])) echo "Min cycle: ".$values["min_micro"]."\r\n";
if (isset($values["max_micro"])) echo "Max cycle: ".$values["max_micro"]."\r\n";
if ($has_ppd42ns) echo "Daten von PPD42NS gesendet.\r\n";
if ($has_sds011) echo "Daten von SDS011 gesendet.\r\n";
if ($has_dht) echo "Daten von DHT gesendet.\r\n";
if ($has_bmp) echo "Daten von BMP gesendet.\r\n";

// check if data dir exists, create if not
if (!file_exists('data')) {
	mkdir('data', 0755, true);
}

// create update string P1,P2 for ppd42ns

if ($has_ppd42ns) {
	$update_string_ppd42ns = time().":";
	if ($values["ratioP1"] < 15) { $update_string_ppd42ns .= $values["P1"]; }
	$update_string_ppd42ns .= ":";
	if ($values["ratioP2"] < 15) { $update_string_ppd42ns .= $values["P2"]; }
	echo $update_string_ppd42ns."\r\n";
}

if ($has_sds011) {
	$update_string_sds011 = time().":";
	if (isset($values["SDS_P1"])) {
		$update_string_sds011 .= $values["SDS_P1"];
		$update_string_sds011 .= ":";
		$update_string_sds011 .= $values["SDS_P2"];
	} else {
		$update_string_sds011 .= $values["P1"];
		$update_string_sds011 .= ":";
		$update_string_sds011 .= $values["P2"];
	}
	echo $update_string_sds011."\r\n";
}

if ($has_dht) {
	$update_string_dht = time().":";
	$update_string_dht .= $values["temperature"];
	$update_string_dht .= ":";
	$update_string_dht .= $values["humidity"];
	echo $update_string_dht."\r\n";
}

if ($has_bmp) {
	$update_string_bmp = time().":";
	$update_string_bmp .= $values["BMP_temperature"];
	$update_string_bmp .= ":";
	$update_string_bmp .= $values["BMP_pressure"];
	echo $update_string_bmp."\r\n";
}

// update ppd42ns rrd file
if ($has_ppd42ns) {

	$datafile = "data/data-".$headers['Sensor']."-ppd42ns-highres.rrd";

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

	$ret = rrd_update($datafile,array($update_string_ppd42ns));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

// update sds011 rrd file
if ($has_sds011) {

	$datafile = "data/data-".$headers['Sensor']."-sds011-highres.rrd";

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

	$ret = rrd_update($datafile,array($update_string_sds011));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

// update dht rrd file
if ($has_dht) {

	$datafile = "data/data-".$headers['Sensor']."-dht-highres.rrd";

	if (!file_exists($datafile)) {
		$opts = array(
			"--step", "30", "--start", time(),
			"DS:temperature:GAUGE:55:U:U",
			"DS:humidity:GAUGE:55:U:U",
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

	$ret = rrd_update($datafile,array($update_string_dht));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

// update bmp rrd file
if ($has_bmp) {

	$datafile = "data/data-".$headers['Sensor']."-bmp-highres.rrd";

	if (!file_exists($datafile)) {
		$opts = array(
			"--step", "30", "--start", time(),
			"DS:temperature:GAUGE:55:U:U",
			"DS:pressure:GAUGE:55:U:U",
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

	$ret = rrd_update($datafile,array($update_string_bmp));
	if (! $ret) {
		$err = rrd_error();
		echo "<b>Update error: </b> $err\n";
	}
}

// save max, min sample times
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

// save data values to CSV (one per day)
$datafile = "data/data-".$headers['Sensor']."-".$today.".csv";

if (!file_exists($datafile)) {
	$outfile = fopen($datafile,"a");
	fwrite($outfile,"Time;durP1;ratioP1;P1;durP2;ratioP2;P2;SDS_P1;SDS_P2;Temp;Humidity;Pressure;Samples;Min_cycle;Max_cycle\n");
	fclose($outfile);
}

if (! isset($values["durP1"])) { $values["durP1"] = ""; }
if (! isset($values["ratioP1"])) { $values["ratioP1"] = ""; }
if (! isset($values["P1"])) { $values["P1"] = ""; }
if (! isset($values["durP2"])) { $values["durP2"] = ""; }
if (! isset($values["ratioP2"])) { $values["ratioP2"] = ""; }
if (! isset($values["P2"])) { $values["P2"] = ""; }
if (! isset($values["SDS_P1"])) { $values["SDS_P1"] = ""; }
if (! isset($values["SDS_P2"])) { $values["SDS_P2"] = ""; }
if (! isset($values["temperature"])) { $values["temperature"] = ""; }
if (! isset($values["humidity"])) { $values["humidity"] = ""; }
if (! isset($values["BMP_temperature"])) { $values["BMP_temperature"] = ""; }
if (! isset($values["BMP_pressure"])) { $values["BMP_pressure"] = ""; }
if (! isset($values["samples"])) { $values["samples"] = ""; }
if (! isset($values["min_micro"])) { $values["min_micro"] = ""; }
if (! isset($values["max_micro"])) { $values["max_micro"] = ""; }

$outfile = fopen($datafile,"a");
fwrite($outfile,$now.";".$values["durP1"].";".$values["ratioP1"].";".$values["P1"].";".$values["durP2"].";".$values["ratioP2"].";".$values["P2"].";".$values["SDS_P1"].";".$values["SDS_P2"].";".$values["temperature"].";".$values["humidity"].";".$values["BMP_temperature"].";".$values["BMP_pressure"].";".$values["samples"].";".$values["min_micro"].";".$values["max_micro"]."\n");
fclose($outfile);

?>
ok
