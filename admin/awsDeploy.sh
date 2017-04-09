#!/bin/sh
src=../
target=~/fivestone/

echo "src set to $src"
echo "target set to $target"

echo "removing existing $target"
rm -rf $target

echo "recreating $target"
mkdir $target

echo "copying base dir over"
cp $src/*.php $target
cp $src/*.css $target
cp $src/*.html $target

echo "copying library over"
cp -r $src/library $target/library


cp -r $src/resources $target/resources
cp -r $src/library/mpdf6 $target/library/mpdf6
cp -r $src/services $target/services

mkdir $target/upload

cp ~/accountinfo.php $target/library/

cp -r $src/cli $target/cli:wq
