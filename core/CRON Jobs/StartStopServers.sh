#!/bin/bash
#
#  Bash script for Master server, checks to see if it should run based on server Instance-Type.
#  By definition, slave servers will be of AWS instance type c4.4xlarge - a fast, server for doing the vid processing.
#  While the master server will be a lower-end, always on (less-expensive) server like an m4.xlarge or something.
#  The below checks to make sure this script is approproate based on the server instance type.
#
instancetype=`GET http://169.254.169.254/latest/meta-data/instance-type`
#
if [ $instancetype = 'c4.4xlarge' ]
    # if a slave server, just return immediately
    then 
        return 0
    # else run the server start/stop script
    else
        cd /home
        php core/"CRON Jobs"/StartStopServers.php
fi