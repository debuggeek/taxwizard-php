To setup a new db run the enclosed sql file or export from most current

# start MySQL. Will create an empty database on first start
$ mysql-ctl start

# run the MySQL interactive shell
$ mysql-ctl cli

select @@hostname;

source admin/TCAD_TABLEImport.sql