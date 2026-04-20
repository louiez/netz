#!/bin/bash
#########################################################
# go through list and check for unaltered files 	#
# if we find a file that has been changed we leave it	#
# just in case they made a special change		#
#########################################################
for n in `cat netz_root/install.lst`
        do
                grep `cat $n 2>/dev/null | md5sum | cut -f1 -d -` netz_root/rev_hash.txt 1>&2>/dev/null
                if [ $? -ne 0 ]; then
                        echo $n " no match"
                        echo $n >> exempt.tmp
                fi
        done
#################################################
# add standard config files to exempt list	#
#################################################
echo 'tools/oui.txt
plugins.ini
region.txt
site-type.txt
site-monitor.conf.php
fsr.txt' >> exempt.tmp
#########################
# get rid of duplicates	#
#########################
cat exempt.tmp | sort | uniq > exempt
#################
# clean up	#
#################
rm -f exempt.tmp
exit
#################################################################
# we need to install from one level below the netz directory	#
# looks like alot of work to get there... but here it is	#
#################################################################
IFS="/"
lou=`pwd`
declare -i c
bar=( $lou )
IFS=""
c=${#bar[@]}
let c=c-1
netzdir=${bar[$c]}
netzdirpath=`pwd`
netzcopyto=""
COUNTER=1
while [  $COUNTER -le $c ]; do
	netzcopyto+=${bar[$COUNTER]}"/"
        let COUNTER=COUNTER+1 
done
#################################################################################
# now move the netz_root with new files to the same name as the base directory	#
# this way when we move one level below we copy into the correct netz directory	#
#################################################################################
mv ${netzdirpath}/netz_root ${netzdirpath}/${netzdir}
#########################################################################################
# now for the copy... sht I hate this part... if it goes wrong it hoses everything	#
#########################################################################################
find  ${netzdirpath}/${netzdir} -depth -print || grep -v -f exempt | cpio -pamvd ${netzcopyto}
#########################################
# set all the permissions to defaults	#
#########################################
./set_permissions.sh


