#!/bin/sh

export DATE=`/bin/date -d yesterday +%F`
export WORKPATH=$(dirname $(readlink -f $0))

echo Datum: $DATE
cd $WORKPATH/mirror
host archive.luftdaten.info
sleep 10
wget --mirror -N -np -q http://archive.luftdaten.info/$DATE/
cd $WORKPATH
./update.pl $DATE
