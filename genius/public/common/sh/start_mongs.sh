#!/bin/sh
mongs_home="/opt/lampp/htdocs/genius/"
log="${mongs_home}""public/common/files/monitor"$1".txt"
if [ $3 = "ctrsystem" ];then
outputDir="/opt/mongs/output/$1/"
mkdir $outputDir

cd /opt/mongs/mongs_all_loader/loader/scripts/
./loader.sh -t ctr -d $1 -r $outputDir &

sleep 10

# cd /opt/mongs/mongs_ctr_parser/scripts/
cd /opt/mongs/mongs_all_parser/scripts/

 cnf="/opt/mongs/mongs_all_parser/etc/ctr/decode_"$1".conf"
# cnf="/opt/mongs/mongs_all_parser/etc/ctr/decode_test111101.conf"

# ./mongs_parser.sh -r $2 -c $cnf -d $outputDir 2>$log
 ./parser.sh -t ctr -r $2 -d $outputDir -c $cnf  2>$log

# ./parser.sh -t ctr -r /data/trace/ctr/LE3AC87B/ -d $outputDir -c $cnf  2>&$log
fi
if [ $3 = "parameter" ];then
cd /opt/mongs/mongs_all_parser/scripts/
./parser.sh -t kget -r $2 -d $1 2>$log
fi
if [ $3 = "cdrsystem" ];then
outputDir="/opt/mongs/output/$1/"
mkdir $outputDir

cd /opt/mongs/mongs_all_loader/loader/scripts/
./loader.sh -t cdr -d $1 -r $outputDir &

sleep 10

# cd /opt/mongs/mongs_cdr_parser/scripts/
cd /opt/mongs/mongs_all_parser/scripts/

 cnf="/opt/mongs/mongs_all_parser/etc/cdr/cdr_parser_"$1".conf"
# cnf="/opt/mongs/mongs_all_parser/etc/cdr/decode_test111101.conf"

# ./mongs_parser.sh -r $2 -c $cnf -d $outputDir 2>$log
 ./parser.sh -t cdr -r $2 -d $outputDir -c $cnf  2>$log

# ./parser.sh -t cdr -r /data/trace/cdr/LE3AC87B/ -d $outputDir -c $cnf  2>&$log
fi	
if [ $3 = "ebmsystem" ];then
outputDir="/opt/mongs/output/$1/"
mkdir $outputDir

cd /opt/mongs/mongs_all_loader/loader/scripts/
./loader.sh -t ebm -d $1 -r $outputDir &

sleep 10

# cd /opt/mongs/mongs_ebm_parser/scripts/
cd /opt/mongs/mongs_all_parser/scripts/

 cnf="/opt/mongs/mongs_all_parser/etc/ebm/ebm_parser_"$1".conf"
# cnf="/opt/mongs/mongs_all_parser/etc/ebm/decode_test111101.conf"

# ./mongs_parser.sh -r $2 -c $cnf -d $outputDir 2>$log
 ./parser.sh -t ebm -r $2 -d $outputDir -c $cnf  2>$log

# ./parser.sh -t ebm -r /data/trace/ebm/LE3AC87B/ -d $outputDir -c $cnf  2>&$log
fi