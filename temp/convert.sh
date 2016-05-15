#!/bin/sh
while read LINE; do
    ts=$(date -d "`echo $LINE | awk '{print $1, $2, $3, $4, $5, $6 }' | sed s/;//`" +%s)
+%s | sed -r s/$/:/
    temp=`echo "$LINE" | awk '{print $7}'`
    echo "rrdtool update temps_take_2.rrd ${ts}:${temp}"
done < 2015-12-21_ppd42ns_sensor_36.csv
