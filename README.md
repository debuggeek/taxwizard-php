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

## Runtime

### Batch computation

Ran on a regular cron job as defined by [crontab](admin/crontab_settings.txt)

The file executed is [batchwatchdog](admin/batchwatchdog.sh)  which executes [batch processing](cli/batch_pdf_aws.php)

### Realtime computation

The primary API called is [massreport.php](massreport.php) 