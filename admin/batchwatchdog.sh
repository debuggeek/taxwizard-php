#!/bin/sh
LOCKFILE=~/batchwatchdog.lock
d=$(date +%y-%m-%d)
if [ -f "$LOCKFILE" ] ; then
    timestamp=$(date +%T)
	echo "$timestamp > batch_pdf is already running" >> ~/phpcli_running.$d.log
	exit 0
else
    touch $LOCKFILE
	/usr/bin/php ~/fivestone/cli/batch_pdf_aws.php >> ~/phpcli.$d.log 2> ~/phpcli_error.$d.log
    rm $LOCKFILE
	exit 0
fi