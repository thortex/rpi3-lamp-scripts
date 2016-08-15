#!/bin/sh -x

cat test.csv | perl -pe 's/^/INSERT INTO i2c_env_sensors.basics (`date`, `temp`,`pres`,`humi`,`lumi`,`cput`) VALUES("/; s/00,/00",/;s/$/,0,0);/;' > test.sql
