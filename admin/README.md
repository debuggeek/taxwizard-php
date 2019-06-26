# Setting up a new table
### Clone existing database
```bash
mysqldbcopy --source=root:root@localhost --destination=root:root@localhost --drop-first <src>:<dst> --skip=data
```

NOTE: Currently this won't work on AWS
### Get template for new table
+ Use `TCAD_TEMPLATE.sql` that is checked in OR
+ Export from recent database with command: 

  ```bash 
  mysqldump -u [user] -h [host] --no-data -p [database] > TCAD_TEMPLATE.sql
  ```
+ Create the new DB
+ Import the template:

  ```bash
  mysql -u [user] -h [host] -p [new db] < TCAD_TEMPLATE.sql
  ```

#### If you are updating to a refreshed table for the same year
Make sure to copy over MLS_SALES and BATCH_PROP_SETTINGS with this command
`INSERT INTO database2.table2 SELECT * from database1.table1;`
### Create new table
+ Create the new table via mysql admin / phpMyAdmin or Sequal Pro

# Populating the Data
#### Requirements
The following source files are required
+ PROP.TXT
+ IMP.TXT
+ Special_Improvement.txt
+ Special_PropData.txt
+ Special_Sales_Exclude_Conf.txt OR Special_Sales.txt
+ MLS:

  csv file with : `prop_id, sale_price, sale_date, address`

The Batch Settings table should be initialized with a new row as the initial default.

#### Copying from S3
To get the file from an s3 bucket you can use the aws cli with the following command
```shell 
aws s3 cp s3://fivestone-tcad-public/ ./ --recursive
```

#### Extraction
These can be extracted from the source expected zip files with names like
+ `2017-04-19_006051_APPRAISAL_R&P ALLJUR AS OF PERLIMINARY.zip` for PROP and IMP
+ `SPECIAL_ALL FILES EXPORT_20170419.zip` for the Special_* files

IMP_DET and PROP are the 2 files that are not in a good format to import, and must be converted

*Most of the above and below can be done with the [extract.sh](./extract.sh) script in ./admin*

convert non-deliminated files to '|' deliminated:
------------------------------------------------
```shell
cat IMP_DET.TXT | awk '{print substr($0,1,12) "|" substr($0,13,4) "|" substr($0,17,12) "|" substr($0,29,12) "|" substr($0,41,10) "|" substr($0,51,25) "|" substr($0,76,10) "|" substr($0,86,4)"|" substr($0,90,4)"|"substr($0,94,15)"|"substr($0,109,14)"|"substr($0,500)}' > IMP_DET_DELIM.txt
```

```shell
cat PROP.TXT | awk '{print substr($0,1,12)"|"substr($0,13,5)"|"substr($0,18,5)"|"substr($0,23,12)"|"substr($0,35,2)"|"substr($0,37,10)"|"substr($0,47,500) "|" substr($0,547,50)"|"substr($0,597,12)"|"substr($0,609,70)"|"substr($0,679,1)"|"substr($0,680,12)"|"substr($0,692,2)"|"substr($0,694,60)"|"substr($0,754,60)"|"substr($0,814,60)"|"substr($0,874,50)"|"substr($0,924,50)"|"substr($0,974,5)"|"substr($0,979,5)"|"substr($0,984,4)"|"substr($0,988,2)"|"substr($0,990,1)"|"substr($0,991,1)"|"substr($0,992,20)"|"substr($0,1012,1)"|"substr($0,1013,27)"|"substr($0,1040,10)"|"substr($0,1050,255)"|"substr($0,1100,10)"|"substr($0,1110,30)"|"substr($0,1140,10)"|"substr($0,1150,255)"|"substr($0,1405,255)"|"substr($0,1660,16)"|"substr($0,1676,10)"|"substr($0,1686,10)"|"substr($0,1696,50)"|"substr($0,1746,50)"|"substr($0,1796,15)"|"substr($0,1811,15)"|"substr($0,1826,15)"|"substr($0,1841,15)"|"substr($0,1856,15)"|"substr($0,1871,15)"|"substr($0,1886,15)"|"substr($0,1901,15)"|"substr($0,1916,15)"|"substr($0,1931,15)"|"substr($0,1946,15)"|"substr($0,1961,20)"|"substr($0,1981,1)"|"substr($0,1982,12)"|"substr($0,1994,20)"|"substr($0,2014,20)"|"substr($0,2034,25)"|"substr($0,2059,12)"|"substr($0,2071,70)"|"substr($0,2141,50)"|"substr($0,2191,12)"|"substr($0,2203,70)"|"substr($0,2273,60)"|"substr($0,2333,60)"|"substr($0,2393,60)"|"substr($0,2453,50)"|"substr($0,2503,50)"|"substr($0,2553,5)"|"substr($0,2558,5)"|"substr($0,2563,4)"|"substr($0,2567,2)"|"substr($0,2569,1)"|"substr($0,2570,1)"|"substr($0,2571,37)"|"substr($0,2608,1)"|"substr($0,2609,1)"|"substr($0,2610,1)"|"substr($0,2611,25)"|"substr($0,2636,25)"|"substr($0,2661,1)"|"substr($0,2662,1)"|"substr($0,2663,1)"|"substr($0,2664,1)"|"substr($0,2665,1)"|"substr($0,2666,1)"|"substr($0,2667,1)"|"substr($0,2668,1)"|"substr($0,2669,1)"|"substr($0,2670,1)"|"substr($0,2671,1)"|"substr($0,2672,25)"|"substr($0,2697,25)"|"substr($0,2722,1)"|"substr($0,2723,1)"|"substr($0,2724,1)"|"substr($0,2725,1)"|"substr($0,2726,1)"|"substr($0,2727,1)"|"substr($0,2728,1)"|"substr($0,2729,1)"|"substr($0,2730,1)"|"substr($0,2731,1)"|"substr($0,2732,10)"|"substr($0,2742,10)"|"substr($0,2752,10)"|"substr($0,2762,10)"|"substr($0,2772,20)"|"substr($0,2792,12)"|"substr($0,2804,70)"|"substr($0,2874,60)"|"substr($0,2934,60)"|"substr($0,2994,60)"|"substr($0,3054,50)"|"substr($0,3104,50)"|"substr($0,3154,5)"|"substr($0,3159,5)"|"substr($0,3164,4)"|"substr($0,3168,2)"|"substr($0,3170,34)"|"substr($0, 3204,12)"|"substr($0,3216,70)"|"substr($0,3286,60)"|"substr($0,3346,60)"|"substr($0,3406,60)"|"substr($0,3466,50)"|"substr($0,3516,50)"|"substr($0,3566,5)"|"substr($0,3571,5)"|"substr($0,3576,4)"|"substr($0,3580,2)"|"substr($0,3582,34)"|"substr($0,3616,12)"|"substr($0,3628,70)"|"substr($0,3698,60)"|"substr($0,3758,50)"|"substr($0,3818,60)"|"substr($0,3878,50)"|"substr($0,3928,50)"|"substr($0,3978,5)"|"substr($0,3983,5)"|"substr($0,3988,4)"|"substr($0,3992,2)"|"substr($0,3994,34)"|"substr($0,4028,5)"|"substr($0,4033,15)"|"substr($0,4048,3)"|"substr($0,4051,40)"|"substr($0,4091,12)"|"substr($0,4103,12)"|"substr($0,4115,12)"|"substr($0,4127,2)"|"substr($0,4129,5)"|"substr($0,4134,2)"|"substr($0,4136,40)"|"substr($0,4176,38)"|"substr($0,4214,14)"|"substr($0,4228,20)"|"substr($0,4248,20)"|"substr($0,4268,20)"|"substr($0,4288,1)"|"substr($0,4289,1)"|"substr($0,4290,70)"|"substr($0,4360,25)"|"substr($0,4385,25)"|"substr($0,4410,25)"|"substr($0,4435,25)"|"substr($0,4460,15)"|"substr($0,4475,5)"|"substr($0,4480,12)"|"substr($0,4492,70)"|"substr($0,4562,60)"|"substr($0,4622,60)"|"substr($0,4682,60)"|"substr($0,4742,50)"|"substr($0,4792,50)"|"substr($0,4842,5)"|"substr($0,4847,5)"|"substr($0,4852,4)"|"substr($0,4856,2)"|"substr($0,4858,1)"|"substr($0,4859,1)"|"substr($0,4860,1)"|"substr($0,4861,70)"|"substr($0,4931,70)"|"substr($0,5001,70)"|"substr($0,5071,5)"|"substr($0,5076,1)"|"substr($0,5077,25)"|"substr($0,5102,15)"|"substr($0,5117,25)"|"substr($0,5142,15)"|"substr($0,5157,25)"|"substr($0,5182,20)"|"substr($0,5202,140)"|"substr($0,5342,1)"|"substr($0,5343,15)"|"substr($0,5358,50)"|"substr($0,5408,1)"|"substr($0,5409,1)"|"substr($0,5410,1)"|"substr($0,5411,1)"|"substr($0,5412,1)"|"substr($0,5413,1)"|"substr($0,5414,1)"|"substr($0,5415,1)"|"substr($0,5416,1)"|"substr($0,5417,15)"|"substr($0,5432,1)"|"substr($0,5433,1)"|"substr($0,5434,1)"|"substr($0,5435,1)"|"substr($0,5436,25)"|"substr($0,2461,1)"|"substr($0,5462,1)"|"substr($0,5463,1)}' > PROP_DELIM.txt
```

NOTE: For PROP and IMP_DELIM if you need to break into smaller files due to SQL insert 
timeout you can do so with the following command:
  ```shell
  split -l 250000 PROP_DELIM.txt prop_delim
  ```
  
+ linux: 

  ```shell 
  split -d -l 1000000 IMP_DET_DELIM.txt
  ``` 

+ mac : 

  ```shell
  split -l 1000000 IMP_DET_DELIM.txt imp_det_delim
  ```

Run the following to load the data per file and table

```sql
LOAD DATA LOCAL INFILE "[filename]" 
INTO TABLE [tablename]
FIELDS TERMINATED BY '|'
LINES TERMINATED BY '\n';
```

If you have .sql files with each of the above commands then you can run

```shell
mysql -h fivestonetcad2.cusgdaffdgw5.us-west-2.rds.amazonaws.com -u dgDBMaster -p tcad_2017 < specImp.sql
```

##### SALES MLS Merged:
Check that the sale_date column is in the YYYY-MM-DD format and if not do this

```sql
UPDATE SPECIAL_SALE_EX_CONF SET sale_date = str_to_date( sale_date, '%m/%d/%Y' );
```

Bring in the TCAD data first so that it will be marked that it's from TCAD not MLS
```sql
INSERT IGNORE INTO SALES_MLS_MERGED SELECT prop_id,sale_price,sale_date,'SPECIAL',sale_type 
FROM SPECIAL_SALE_EX_CONF WHERE sale_price >0;
````
 
MLS table might be ok and not need date change, but verify the YYYY-MM-DD format before doing the following

```sql
INSERT IGNORE INTO SALES_MLS_MERGED SELECT prop_id,sale_price,sale_date,'MLS',NULL 
FROM MLS_SALES WHERE sale_price >0;
```

**NOTE**: `IGNORE` is because data isn�t clean and same sale for same date sometimes 2x
### Special Notes
Starting in 2014 we began getting ths sales in the Special_Sales.txt but 
that file needs to be reformatted with the following command:

```shell
sed 's/^[[:digit:]]*|[[:digit:]]*|/&|/' Special_Sales.txt > Special_Sales.txt.fixed
```

### Within Year Updates
Copy existing MLS_SALES and BATCH_PROP, BATCH_PROP_SETTINGS

```sql
INSERT INTO tcad_2018_2.MLS_SALES SELECT * FROM tcad_2018.MLS_SALES;
```


#**Useful Tools/Links**

#### SQL Tools
UI admin for Mac
[Sequel Pro](https://sequelpro.com/)
