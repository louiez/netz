#!/bin/sh 
         COUNTER=1
         #while [  $COUNTER -lt 25 ]; do
	for COUNTER in `seq 1 30`
	do
             RTN=`ping -n -t $COUNTER -W 1 -c 1 $1 | egrep '^From|^64'`
#echo $RTN 
RRtn=`echo -n $RTN | egrep From`
NRnt=`echo -n $RTN | egrep 64`
Urtn=`echo -n $RTN | egrep Unreachable`
                if [ -n "$Urtn" ]; then
                        echo "Unreachable"
                        #echo "$NRnt" | awk '{print $4}'
                        exit

		elif [ -n "$RRtn" ]; then
			#echo -e "Rely \c"
			echo "$RRtn" | awk '{print $2}' | sed 's/\://g'
		elif [ -n "$NRnt" ]; then
			#echo -e "last \c"
			echo "$NRnt" | awk '{print $4}' | sed 's/\://g'
			exit
		else
			echo "unknown"
			echo $RTN
		fi
        #     let COUNTER=COUNTER+1 
         done
	#echo "done"
#for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17; do ping -t $i -c 1 208.254.241.12 | egrep '^From|^64' ; done
