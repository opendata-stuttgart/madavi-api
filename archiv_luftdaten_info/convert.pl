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
	
	if ($type eq 'ppd42ns') {
                $unit1 = "Partikel / Liter ";
                $unit2 = "Partikel / Liter";
                $ds1 = "PMone"; $ds1_title = "PM1";
                $ds2 = "PMtwo"; $ds2_title = "PM2.5";
		$cdef1 = "CDEF:avgDSone=DSone,1000,*,283,/";
		$cdef2 = "CDEF:avgDStwo=DStwo,1000,*,283,/";
		$cdef3 = "CDEF:avgDSone=DSone,1000,*,283,/";
		$cdef4 = "CDEF:avgDStwo=DStwo,1000,*,283,/";
		$cdef5 = "CDEF:avgDSone=DSone,86400,TRENDNAN,1000,*,283,/";
		$cdef6 = "CDEF:avgDStwo=DStwo,86400,TRENDNAN,1000,*,283,/";
	} elsif ($type eq 'sds011') {
                $unit1 = "µg / m³";
                $unit2 = "µg / m³";
                $ds1 = "PMone"; $ds1_title = "PM1";
                $ds2 = "PMtwo"; $ds2_title = "PM2.5";
		$cdef1 = "CDEF:avgDSone=DSone";
		$cdef2 = "CDEF:avgDStwo=DStwo";
		$cdef3 = "CDEF:avgDSone=DSone";
		$cdef4 = "CDEF:avgDStwo=DStwo";
		$cdef5 = "CDEF:avgDSone=DSone,86400,TRENDNAN";
		$cdef6 = "CDEF:avgDStwo=DStwo,86400,TRENDNAN";
	} elsif ($type eq 'dht22') {
                $unit1 = "° Celsius";
                $unit2 = "%";
                $ds1 = "temperature"; $ds1_title = "Temperatur";
                $ds2 = "humidity"; $ds2_title = "rel. Luftfeuchte";
		$cdef1 = "CDEF:avgDSone=DSone";
		$cdef2 = "CDEF:avgDStwo=DStwo";
		$cdef3 = "CDEF:avgDSone=DSone";
		$cdef4 = "CDEF:avgDStwo=DStwo";
		$cdef5 = "CDEF:avgDSone=DSone,86400,TRENDNAN";
		$cdef6 = "CDEF:avgDStwo=DStwo,86400,TRENDNAN";
	} elsif ($type eq 'bmp180') {
                $unit1 = "° Celsius";
                $unit2 = "Pascal";
                $ds1 = "temperature"; $ds1_title = "Temperatur";
                $ds2 = "pressure"; $ds2_title = "Luftdruck";
		$cdef1 = "CDEF:avgDSone=DSone";
		$cdef2 = "CDEF:avgDStwo=DStwo";
		$cdef3 = "CDEF:avgDSone=DSone";
		$cdef4 = "CDEF:avgDStwo=DStwo";
		$cdef5 = "CDEF:avgDSone=DSone,86400,TRENDNAN";
		$cdef6 = "CDEF:avgDStwo=DStwo,86400,TRENDNAN";
	}

	@options = ();

	@options = (
		"--start", "e".$start,
		"--end", "$ymd",
		"--title=$title",
		"--lower=0",
		"-w 500",
		"-h 250"
	);

	if ($particle == 1) {
		push @options, "--vertical-label=$unit1";
		push @options, "DEF:DSone=data/data-sensor-".$sensor."-".$type.".rrd:$ds1:AVERAGE:step=30";
		push @options, $cdef1;
		push @options, "LINE1:avgDSone#FF0000:$ds1_title";
	} elsif ($particle == 2) {
		push @options, "--vertical-label=$unit2";
		push @options, "DEF:DStwo=data/data-sensor-".$sensor."-".$type.".rrd:$ds2:AVERAGE:step=30";
		push @options, $cdef2;
		push @options, "LINE1:avgDStwo#0000FF:$ds2_title";
	} elsif ($particle == 3) {
		push @options, "--vertical-label=$unit1";
		push @options, "DEF:DSone=data/data-sensor-".$sensor."-".$type.".rrd:$ds1:AVERAGE:step=86400";
		push @options, $cdef3;
		push @options, "LINE1:avgDSone#FF0000:$ds1_title";
	} elsif ($particle == 4) {
		push @options, "--vertical-label=$unit2";
		push @options, "DEF:DStwo=data/data-sensor-".$sensor."-".$type.".rrd:$ds2:AVERAGE:step=86400";
		push @options, $cdef4;
		push @options, "LINE1:avgDStwo#0000FF:$ds2_title";
	} elsif ($particle == 5) {
		push @options, "--vertical-label=$unit1";
		push @options, "DEF:DSone=data/data-sensor-".$sensor."-".$type.".rrd:$ds1:AVERAGE:step=30";
		push @options, $cdef5;
		push @options, "LINE1:avgDSone#0000FF:$ds1_title";
	} elsif ($particle == 6) {
		push @options, "--vertical-label=$unit2";
		push @options, "DEF:DStwo=data/data-sensor-".$sensor."-".$type.".rrd:$ds2:AVERAGE:step=30";
		push @options, $cdef6;
		push @options, "LINE1:avgDStwo#0000FF:$ds2_title";
	}
	RRDs::graph($output,@options);
        my $ERR=RRDs::error;
        print "ERROR while creating graph: $ERR\n" if $ERR;
}

foreach $daydir (@dirs_sorted) {

	print $daydir."\n";

	my @files_per_day = <$daydir/*.csv>;

	foreach my $file (@files_per_day) {

		@dirparts = explode("/",$file);
		$filename = $dirparts[3];
		$filename = substr($filename,0,-4);
		@nameparts = explode("_",$filename);
		$sensor_date = $nameparts[0];
		$sensor_type = $nameparts[1];
		$sensor_name = $nameparts[3];

		if ($sensor_type eq "ppd42ns") {
			$dataset_1 = "DS:PMone:GAUGE:300:U:U";
			$dataset_2 = "DS:PMtwo:GAUGE:300:U:U";
		} elsif ($sensor_type eq "ssd011") {
			$dataset_1 = "DS:PMone:GAUGE:300:U:U";
			$dataset_2 = "DS:PMtwo:GAUGE:300:U:U";
		} elsif ($sensor_type eq "dht22") {
			$dataset_1 = "DS:temperature:GAUGE:300:U:U";
			$dataset_2 = "DS:humidity:GAUGE:300:U:U";
		} elsif ($sensor_type eq "bmp180") {
			$dataset_1 = "DS:temperature:GAUGE:300:U:U";
			$dataset_2 = "DS:pressure:GAUGE:300:U:U";
		}

		if (! -f 'data/data-sensor-'.$sensor_name.'-'.$sensor_type.'.rrd') {
			RRDs::create(
				"data/data-sensor-".$sensor_name."-".$sensor_type.".rrd", "--step=30", "--start=946684800",
				$dataset_1, $dataset_2,
				"RRA:AVERAGE:0,99999:1:92160", "RRA:AVERAGE:0,99999:30:35136", "RRA:AVERAGE:0,99999:720:14640",
			);
			my $ERR=RRDs::error;
			die "ERROR while updating data/data-sensor-$sensor_name-$sensor_type.rrd: $ERR\n" if $ERR;
		}

		open (SENSORDATA, $file);

		while (<SENSORDATA>) {
			if (index($_,'sensor_id') == -1) {
				chomp($_);	
				@fields = explode(';',$_);
#				($sensor_id,$sensor_type,$location,$lat,$lon,$timestamp,$P1,$durP1,$ratioP1,$P2,$durP2,$ratioP2) = explode(';',$_);
				$timestamp = substr($fields[5],0,19);
				($y,$m,$d,$h,$min,$sec) = ($timestamp =~ m/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})/);
				$dt = timegm($sec,$min,$h,$d,($m-1),$y);
				$data1 = ''; $data2 = '';
				if ($sensor_type eq "ppd42ns") {
		                        if (($fields[6] > 0) && ($fields[8] < 15)) { $data1 = $fields[6]; }
		                        if (($fields[9] > 0) && ($fields[11] < 15)) { $data2 = $fields[9]; }
				} elsif ($sensor_type eq "sds011") {
				} elsif ($sensor_type eq "dht22") {
		                        $data1 = $fields[6];
		                        $data2 = $fields[7];
				} elsif ($sensor_type eq "bmp180") {
				}
				if (($data1 ne '') && ($data2 ne '')) { 
#					print "data/data-sensor-".$sensor_name."-".$sensor_type.".rrd"." - "."$dt\@$data1\:$data2"."\n";
					RRDs::update("data/data-sensor-".$sensor_name."-".$sensor_type.".rrd", "$dt\@$data1\:$data2");
					my $ERR=RRDs::error;
					print "ERROR while updating data/data-sensor-$sensor_name-$sensor_type.rrd: $ERR\n" if ($ERR && (index($ERR,"illegal attempt to update using time") == -1));
				}
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
		@nameparts = explode("-",$data_sensor);
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
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-24-hour-week.png", "-1w", "Sensor data 24hour average, one week", $sensor_name,$sensor_type,3,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-24-hour-week.png", "-1w", "Sensor data 24hour average, one week", $sensor_name,$sensor_type,4,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-1-24-hour-float.png", "-8d", "Sensor data floating 24hour average, one week", $sensor_name,$sensor_type,5,"$newdate");
		create_graph("images/$y$m$d/sensor-$sensor_name-$sensor_type-25-24-hour-float.png", "-8d", "Sensor data floating 24hour average, one week", $sensor_name,$sensor_type,6,"$newdate");
	}
}
