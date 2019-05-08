# Taxwizard-PHP
This is the legacy application developed for finding property tax information

At this time this application is used only for it's api's and none of the UI rendering is done here.

It is expected in our EC2 instances that this application resides in a `fivestone` folder under a hosted apache instance

## Data
Data is loaded into the system by following the [Admin Readme](admin/README.md)

## Application Setup

### System Requirements
+ Ubuntu 18.04
+ Apache/2.4.29+
+ PHP 7.2+
+ MySql 5.6+
In general the files are expected to be deployed into a `fivestone` folder under the base apache2 html directory.  In 
the current aws ec2 instances the `fivestone` directory is in the home folder and symlinked from `/var/www/html`.

For specifics review the [Deployment script](admin/awsDeploy.sh).  Reading this file will give an overview of where it 
copies files from this project into the apache filestructure

## Docker Deployment

Still under development, but should work in some local environments  [Dockerfile](Dockerfile)

#### Steps
From the base project directory 
```bash
docker build --tag=taxwizard-php .
```
Should result in message like
```bash
Successfully built 52fcccd30a42
```
And you should see it in your images
```bash
$ docker image ls
REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
taxwizard-php       latest              52fcccd30a42        2 minutes ago       594MB
```
Then you should be able to launch with 
```bash
docker run -p 8080:80 taxwizard-php
```
Validate by going to http://localhost:8080/fivestone/ and you should be seeing the legacy UI which is no longer used

The UI probably won't work but you can use the below cURL once you have the PHP server wired to a database
```bash
curl -X POST \
  http://localhost:8080/fivestone/massreport.php \
  -H 'Postman-Token: 70edd544-553a-4337-be62-a8d17d52cc7a' \
  -H 'cache-control: no-cache' \
  -d '{"onlyLowerComps":false,"includeMLS":true,"mlsMultiYear":1,"multiHood":true,"limitImps":false,"includeVU":true,"grossAdjEnabled":false,"netAdjEnabled":false,"netAdjustAmt":null,"subClassRangeEnabled":false,"subClassRange":0,"pctGoodRangeEnabled":false,"disablePctGoodRange_Text":true,"pctGoodRange":null,"pctGoodMin":null,"pctGoodMax":null,"tcadScoreLimitEnabled":false,"ratiosEnabled":false,"saleRatioMin":null,"saleRatioMax":null,"useTcadScoreLimitPct":true,"tcadScoreLimitPct":null,"tcadScoreLimitMin":null,"tcadScoreLimitMax":null,"isEquity":false,"useSqftRangePct":true,"sqftRangePct":200,"sqftRangeMin":1500,"sqftRangeMax":3000,"rankByIndicated":true,"showTcadScores":false,"showRatios":false,"maxDisplay":10,"singlePropSubmit":"","files":[],"fileResult":"","propId":"303618","filterProps":null,"filterTypeExclude":true,"traceComps":true}'
```

## Runtime

### Batch computation

Ran on a regular cron job as defined by [crontab](admin/crontab_settings.txt)

The file executed is [batchwatchdog](admin/batchwatchdog.sh)  which executes [batch processing](cli/batch_pdf_aws.php)

### Realtime computation

The primary API called is [massreport.php](massreport.php) 