#!/usr/bin/perl

use DateTime qw( );
use Date::Calc qw(Add_Delta_Days);
use Time::Local;
use RRDs;

# 2015-12-21T23:52:35.018573+00:00

my $y;
my $m;
my $d;
my $h;
my $min;
my $sec;
my $fract;
my $tzh;
my $tzm;
my $dt;
my @dirparts;
my @nameparts;
my $filename;
my $file_part1;
my $file_part2;
my $sensor_name;

my @dirs = <mirror/archive.luftdaten.info/2*>;

my @dirs_sorted = sort(@dirs);

while ($dirs_sorted[0] lt "mirror/archive.luftdaten.info/2015-10-10/") { shift @dirs_sorted; }

sub create_graph {
	my ($output,$start,$title,$sensor,$type,$particle,$ymd) = @_;
	if ($particle == 1) {
	        RRDs::graph(
        	        $output, "--start", "e".$start, "--end", $ymd, "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
        	        "DEF:PMone=data/data-sensor-".$sensor."-".$type.".rrd:PMone:AVERAGE:step=30",
			"CDEF:avgPMone=PMone,1000,*,283,/",
			"LINE1:avgPMone#FF0000:'PM1'"
		);
	} elsif ($particle == 2) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor."-".$type.".rrd:PMtwo:AVERAGE:step=30",
			"CDEF:avgPMtwo=PMtwo,1000,*,283,/",
			"LINE1:avgPMtwo#0000FF:'PM2.5'"
        	);
	} elsif ($particle == 3) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMone=data/data-sensor-".$sensor."-".$type.".rrd:PMone:AVERAGE:step=86400",
			"CDEF:avgPMone=PMone,1000,*,283,/",
			"LINE1:avgPMone#FF0000:'PM1'",
        	);
	} elsif ($particle == 4) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor."-".$type.".rrd:PMtwo:AVERAGE:step=86400",
			"CDEF:avg2PMtwo=PMtwo,1000,*,283,/",
			"LINE1:avg2PMtwo#0000FF:'PM2.5'",
        	);
	} elsif ($particle == 5) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMone=data/data-sensor-".$sensor."-".$type.".rrd:PMone:AVERAGE:step=30",
			"CDEF:avg2PMone=PMone,86400,TRENDNAN",					# 24h average
			"LINE1:avg2PMone#0000FF:'PM1'",
        	);
	} elsif ($particle == 6) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor."-".$type.".rrd:PMtwo:AVERAGE:step=30",
			"CDEF:avg2PMtwo=PMtwo,86400,TRENDNAN",					# 24h average
			"LINE1:avg2PMtwo#0000FF:'PM2.5'",
        	);
	}
        my $ERR=RRDs::error;
        print "ERROR while creating graph: $ERR\n" if $ERR;
}

foreach $daydir (@dirs_sorted) {

	print $daydir."\n";

	my @files_per_day = <$daydir/*.csv>;

	foreach my $file (@files_per_day) {

		@dirparts = split("/",$file);
		$filename = $dirparts[3];
		$filename = substr($filename,0,-4);
		@nameparts = split("_",$filename);
		$sensor_date = $nameparts[0];
		$sensor_type = $nameparts[1];
		$sensor_name = $nameparts[3];

		if (! -f 'data/data-sensor-'.$sensor_name.'-'.$sensor_type.'.rrd') {
			RRDs::create(
				"data/data-sensor-".$sensor_name."-".$sensor_type.".rrd", "--step=30", "--start=946684800",
				"DS:PMone:GAUGE:300:U:U", "DS:PMtwo:GAUGE:300:U:U",
				"RRA:AVERAGE:0,99999:1:92160", "RRA:AVERAGE:0,99999:30:35136", "RRA:AVERAGE:0,99999:720:14640",
			);
			my $ERR=RRDs::error;
			die "ERROR while updating data/data-sensor-$sensor_name-$sensor_type.rrd: $ERR\n" if $ERR;
		}

		open (SENSORDATA, $file);

		while (<SENSORDATA>) {
			if (index($_,'sensor_id') == -1) {	
				@fields = split(';',$_);
#				($sensor_id,$sensor_type,$location,$lat,$lon,$timestamp,$P1,$durP1,$ratioP1,$P2,$durP2,$ratioP2) = split(';',$_);
				$timestamp = substr($fields[5],0,19);
				($y,$m,$d,$h,$min,$sec) = ($timestamp =~ m/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/);
				$dt = timegm($sec,$min,$h,$d,($m-1),$y);
				$dt = $dt;
	                        if (($fields[6] < 0) || ($fields[8] > 15)) { $P1 = ''; } else { $P1 = $fields[6]; }
	                        if (($fields[9] < 0) || ($fields[11] > 15)) { $P2 = ''; } else { $P2 = $fields[9]; }
				RRDs::update("data/data-sensor-".$sensor_name."-".$sensor_type.".rrd", "$dt\@$P1\:$P2");
				my $ERR=RRDs::error;
				print "ERROR while updating data/data-sensor-$sensor_name-$sensor_type.rrd: $ERR\n" if ($ERR && (index($ERR,"illegal attempt to update using time") == -1));
			}
		}

		# create image dir per day
		if (! -d "images/$y$m$d") {
			mkdir "images/$y$m$d";
		}
	}

	my ($ynew,$mnew,$dnew) = Add_Delta_Days($y,$m,$d,1);
	my $newdate = sprintf("%04d%02d%02d",$ynew,$mnew,$dnew);
	my @data_files = <data/data-sensor-*.rrd>;
	foreach $data_sensor (@data_files) {
		$data_sensor = substr($data_sensor,0,-4);
		@nameparts = split("-",$data_sensor);
		$sensor_name=$nameparts[2];
		$sensor_type=$nameparts[3];
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-day.png", "-1d", "Sensor data over one day", $sensor_name,$sensor_type,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-week.png", "-1w", "Sensor data over one week", $sensor_name,$sensor_type,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-month.png", "-1m", "Sensor data over one month", $sensor_name,$sensor_type,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-year.png", "-1y", "Sensor data over one year", $sensor_name,$sensor_type,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-day.png", "-1d", "Sensor data over one day", $sensor_name,$sensor_type,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-week.png", "-1w", "Sensor data over one week", $sensor_name,$sensor_type,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-month.png", "-1m", "Sensor data over one month", $sensor_name,$sensor_type,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-year.png", "-1y", "Sensor data over one year", $sensor_name,$sensor_type,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-24-hour-week.png", "-1w", "Sensor data PM1 24hours, one week", $sensor_name,$sensor_type,3,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-24-hour-week.png", "-1w", "Sensor data PM2.5 24hours, one week", $sensor_name,$sensor_type,4,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-24-hour-float.png", "-8d", "Sensor data PM1 floating 24hours, one week", $sensor_name,$sensor_type,5,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-24-hour-float.png", "-8d", "Sensor data PM2.5 floating 24hours, one week", $sensor_name,$sensor_type,6,"$newdate");
	}
}
