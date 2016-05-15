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
	my ($output,$start,$title,$sensor,$particle,$ymd) = @_;
	if ($particle == 1) {
	        RRDs::graph(
        	        $output, "--start", "e".$start, "--end", $ymd, "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
        	        "DEF:PMone=data/data-sensor-".$sensor.".rrd:PMone:AVERAGE:step=30",
			"CDEF:v1=PMone", "CDEF:v2=PREV(v1)", "CDEF:v3=PREV(v2)", "CDEF:v4=PREV(v3)", "CDEF:v5=PREV(v4)",
#			"CDEF:avg1PMone=v1,v2,v3,3,SORT,POP,2,REV,POP",
#			"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/",
			"CDEF:avg2PMone=v1,1000,*,283,/",
#			"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"),		# Median over 5 values
#			"LINE1:PMone#FFFF00:'PM1'",
#			"LINE1:avg1PMone#FF0000:'PM1'",
			"LINE1:avg2PMone#FF0000:'PM1'"
		);
	} elsif ($particle == 2) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor.".rrd:PMtwo:AVERAGE:step=30",
			"CDEF:v1=PMtwo", "CDEF:v2=PREV(v1)", "CDEF:v3=PREV(v2)", "CDEF:v4=PREV(v3)", "CDEF:v5=PREV(v4)",
#			"CDEF:avg1PMtwo=v1,v2,v3,3,SORT,POP,2,REV,POP",
#			"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/",
			"CDEF:avg2PMtwo=v1,1000,*,283,/",
#			"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"),		# Median over 5 values
#			"LINE1:PMtwo#FFFF00:'PM2.5'",
#			"LINE1:avg1PMtwo#FF0000:'PM2.5'",
			"LINE1:avg2PMtwo#0000FF:'PM2.5'"
        	);
	} elsif ($particle == 3) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMone=data/data-sensor-".$sensor.".rrd:PMone:AVERAGE:step=86400",
			"CDEF:v1=PMone", "CDEF:v2=PREV(v1)", "CDEF:v3=PREV(v2)", "CDEF:v4=PREV(v3)", "CDEF:v5=PREV(v4)",
#			"CDEF:avg1PMone=v1,v2,v3,3,SORT,POP,2,REV,POP",
#			"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/",
			"CDEF:avg2PMone=v1,1000,*,283,/",
#			"CDEF:avg2PMone=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"),		# Median over 5 values
			"LINE1:avg2PMone#FF0000:'PM1'",
#			"LINE1:avg1PMone#FF0000:'PM1'",
#			"LINE1:avg2PMone#FF0000:'PM1'"
        	);
	} elsif ($particle == 4) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor.".rrd:PMtwo:AVERAGE:step=86400",
			"CDEF:v1=PMtwo", "CDEF:v2=PREV(v1)", "CDEF:v3=PREV(v2)", "CDEF:v4=PREV(v3)", "CDEF:v5=PREV(v4)",
#			"CDEF:avg1PMtwo=v1,v2,v3,3,SORT,POP,2,REV,POP",
#			"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,4,REV,POP,3,AVG,1000,*,283,/",
			"CDEF:avg2PMtwo=v1,1000,*,283,/",
#			"CDEF:avg2PMtwo=v1,v2,v3,v4,v5,5,SORT,POP,POP,3,REV,POP,POP"),		# Median over 5 values
			"LINE1:avg2PMtwo#0000FF:'PM2.5'",
#			"LINE1:avg1PMtwo#FF0000:'PM2.5'",
#			"LINE1:avg2PMtwo#FF0000:'PM2.5'"
        	);
	} elsif ($particle == 5) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMone=data/data-sensor-".$sensor.".rrd:PMone:AVERAGE:step=30",
			"CDEF:avg2PMone=PMone,86400,TRENDNAN",					# 24h average
			"LINE1:avg2PMone#0000FF:'PM1'",
        	);
	} elsif ($particle == 6) {
	        RRDs::graph(
		        $output, "--start", "e".$start, "--end", "$ymd", "--title=$title", "--vertical-label=Partikel / Liter", "--lower=0", "-w 500", "-h 250",
			"DEF:PMtwo=data/data-sensor-".$sensor.".rrd:PMtwo:AVERAGE:step=30",
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
		$sensor_name = $nameparts[3];

		if (! -f 'data/data-sensor-'.$sensor_name.'.rrd') {
			RRDs::create(
				"data/data-sensor-".$sensor_name.".rrd", "--step=30", "--start=946684800",
				"DS:PMone:GAUGE:300:U:U", "DS:PMtwo:GAUGE:300:U:U",
				"RRA:AVERAGE:0,99999:1:92160", "RRA:AVERAGE:0,99999:30:35136", "RRA:AVERAGE:0,99999:720:14640",
			);
			my $ERR=RRDs::error;
			die "ERROR while updating data/data-sensor-$sensor_name.rrd: $ERR\n" if $ERR;
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
#				if (($P1 < 0) || ($ratioP1 > 20)) { $P1 = ''; }
#				if (($P2 < 0) || ($ratioP2 > 20)) { $P2 = ''; }
#				print $timestamp.' - '.$dt.' - '.$y.$m.$d.$h.$min.$sec."\n";
				RRDs::update("data/data-sensor-".$sensor_name.".rrd", "$dt\@$P1\:$P2");
				my $ERR=RRDs::error;
				print "ERROR while updating data/data-sensor-$sensor_name.rrd: $ERR\n" if ($ERR && (index($ERR,"illegal attempt to update using time") == -1));
			}
		}
		if (! -d "images/$y$m$d") {
			mkdir "images/$y$m$d";
		}
	}
	my ($ynew,$mnew,$dnew) = Add_Delta_Days($y,$m,$d,1);
	my $newdate = sprintf("%04d%02d%02d",$ynew,$mnew,$dnew);
	my @data_files = <data/data-sensor-*.rrd>;
	foreach $data_sensor (@data_files) {
		@nameparts = split("-",$data_sensor);
		$sensor_name=substr($nameparts[2],0,-4);
		create_graph("images/$y$m$d/sensor-$sensor_name-1-day.png", "-1d", "Sensor data over one day", $sensor_name,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-1-week.png", "-1w", "Sensor data over one week", $sensor_name,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-1-month.png", "-1m", "Sensor data over one month", $sensor_name,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-1-year.png", "-1y", "Sensor data over one year", $sensor_name,1,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-day.png", "-1d", "Sensor data over one day", $sensor_name,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-week.png", "-1w", "Sensor data over one week", $sensor_name,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-month.png", "-1m", "Sensor data over one month", $sensor_name,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-year.png", "-1y", "Sensor data over one year", $sensor_name,2,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-1-24-hour-week.png", "-1w", "Sensor data PM1 24hours, one week", $sensor_name,3,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-24-hour-week.png", "-1w", "Sensor data PM2.5 24hours, one week", $sensor_name,4,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-1-24-hour-float.png", "-8d", "Sensor data PM1 floating 24hours, one week", $sensor_name,5,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-25-24-hour-float.png", "-8d", "Sensor data PM2.5 floating 24hours, one week", $sensor_name,6,"$newdate");
	}
}
