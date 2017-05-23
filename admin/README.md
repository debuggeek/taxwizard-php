#**Setting up a new table**
###Clone existing database
>`mysqldbcopy --source=root:root@localhost --destination=root:root@localhost --drop-first tcad_2017:tcad_2017_2`

###Get template for new table
+ Use `TCAD_TEMPLATE.sql` that is checked in OR
+ Export from recent database with command: 
>> `mysqldump -u [user] -h [host] --no-data -p [database] > TCAD_TEMPLATE.sql`
+ Create the new DB
+ Import the template:
>> `mysql -u [user] -h [host] -p [new db] < TCAD_TEMPLATE.sql`

###Create new table
+ Create the new table via mysql admin / phpMyAdmin or 

#**Populating the Data**
#####Requirements
The following source files are required
+ PROP.TXT
+ IMP_DELIM.TXT
+ Special_Improvement.txt
+ Special_PropData.txt
+ Special_Sales_Exclude_Conf.txt OR Special_Sales.txt
+ MLS:
  csv file with : `prop_id, sale_price, sale_date, address`

These can be extracted from the source expected zip files with names like
+ `2017-04-19_006051_APPRAISAL_R&P ALLJUR AS OF PERLIMINARY.zip` for PROP and IMP
+ `SPECIAL_ALL FILES EXPORT_20170419.zip` for the Special_* files

NOTE: For PROP and IMP_DELIM if you need to break into smaller files due to SQL insert 
timeout you can do so with the following command:
>> `split -l 250000 PROP_DELIM.txt prop_delim`
<br>
>> linux: `split -d -l 1000000 IMP_DET_DELIM.txt` 
<br>
>> mac : `split -l 1000000 IMP_DET_DELIM.txt imp_det_delim`

Run the following to load the data per file and table

>`LOAD DATA LOCAL INFILE '[filename]' 
INTO TABLE [tablename]
FIELDS TERMINATED BY '|'
LINES TERMINATED BY '\n';`

If you have .sql files with each of the above commands then you can run

> `mysql -h fivestonetcad2.cusgdaffdgw5.us-west-2.rds.amazonaws.com -u dgDBMaster -p tcad_2017 < specImp.sql`
#####SALES MLS Merged:
Check that the sale_date column is in the YYYY-MM-DD format and if not do this

>`UPDATE SPECIAL_SALE_EX_CONF SET sale_date = str_to_date( sale_date, '%m/%d/%Y' );`

Bring in the TCAD data first so that it will be marked that it's from TCAD not MLS
> `INSERT IGNORE INTO SALES_MLS_MERGED SELECT prop_id,sale_price,sale_date,'SPECIAL',sale_type FROM SPECIAL_SALE_EX_CONF WHERE sale_price >0`
 
MLS table might be ok and not need date change, but verify the YYYY-MM-DD format before doing the following

> `INSERT IGNORE INTO SALES_MLS_MERGED SELECT prop_id,sale_price,sale_date,'MLS',NULL FROM MLS_SALES WHERE sale_price >0`

**NOTE**: `IGNORE` is because data isn’t clean and same sale for same date sometimes 2x
###Special Notes
Starting in 2014 we began getting ths sales in the Special_Sales.txt but 
that file needs to be reformatted with the following command:
>`sed 's/^[[:digit:]]*|[[:digit:]]*|/&|/' Special_Sales.txt > Special_Sales.txt.fixed`




#**Useful Tools/Links**

####SQL Tools
UI admin for Mac
https://sequelpro.com/