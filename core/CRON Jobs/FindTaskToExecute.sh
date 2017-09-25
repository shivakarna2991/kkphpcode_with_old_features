#!/bin/bash
#
#  Bash script for slave servers, checks to see if it should run based on server Instance-Type.
#  By definition, slave servers will be of AWS instance type c4.4xlarge - a fast, server for doing the vid processing.
#  While the master server will be a lower-end, always on (less-expensive) server like an m4.xlarge or something.
#  The below checks to make sure this script is approproate based on the server instance type.
#
instancetype=`GET http://169.254.169.254/latest/meta-data/instance-type`
#
if [ $instancetype != 'c4.4xlarge' ]
    then return 0
    else
        # retrieve the instance-id of this server and pass it to the FindTaskToExecute.php script
        instanceid=`GET http://169.254.169.254/latest/meta-data/instance-id`
        cd /home
        php core/"CRON Jobs"/FindTaskToExecute.php -i $instanceid
fi