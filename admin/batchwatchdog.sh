#!/bin/sh
if ps -ef | grep -v grep | grep batch_pdf.php ; then
        echo "batch_pdf is already running" | mutt -s "Batch_pdf still running" debuggeek@gmail.com
	exit 0
else
        d=$(date +%y-%m-%d)
	/usr/bin/php /var/www/html/cli/batch_pdf.php >> ~/phpcli.$d.log 2> ~/phpcli_error.$d.log

	#mailing program
        echo "restarted batch_pdf, not found in process list" | mutt -s "Restarted batch_pdf" debuggeek@gmail.comdd
	#/home/user/bin/simplemail.php "Print spooler was not running...  Restarted."
        exit 0
fi