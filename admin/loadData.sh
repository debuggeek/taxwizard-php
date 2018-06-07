#!/usr/bin/env bash

programname=$0
host=fivestonetcad2.cusgdaffdgw5.us-west-2.rds.amazonaws.com
user=dgDBMaster

function usage {
    echo "usage: $programname -d <database> [-h host]"
    echo "  -d database    database to upload to"
    echo "  -h host        optional host to override default"
    exit 1
}

while getopts d:h: o
do case "$o" in
    d) db="$OPTARG";;
    h) host="$OPTARG";;
    [?]) usage;;
    esac
done

if [ -z ${db+x} ]; then
    usage
fi
echo 'Attempting to connect to $host against $db'
mysql -h $host -u $user -p $db -e "SHOW DATABASES LIKE $db"

echo 'Loading SPECIAL_PROPDATA table'
mysql -h $host -u $user -p $db -e "LOAD DATA LOCAL INFILE './Special_PropData.txt' INTO TABLE SPECIAL_PROPDATA FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n';"

echo 'Loading SPECIAL_IMP table'
mysql -h $host -u $user -p $db -e "LOAD DATA LOCAL INFILE './Special_Improvement.txt' INTO TABLE SPECIAL_IMP FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n';"

echo 'Loading SPECIAL_SALE_EX_CONF table'
mysql -h $host -u $user -p $db -e "LOAD DATA LOCAL INFILE './Special_Sales.txt.fixed' INTO TABLE SPECIAL_SALE_EX_CONF FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n';"

echo 'Loading PROP table'
mysql -h $host -u $user -p $db -e "LOAD DATA LOCAL INFILE './PROP_DELIM.txt' INTO TABLE PROP FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n';"

echo 'Loading IMP_DET table'
mysql -h $host -u $user -p $db -e "LOAD DATA LOCAL INFILE './IMP_DET_DELIM.txt' INTO TABLE IMP_DET FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n';"