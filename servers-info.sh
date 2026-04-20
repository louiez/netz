#!/bin/bash
# Parts of this script was taken from http://bash.cyberciti.biz
# [1] You need to setup correct VARIABLES script:
#
# (a) Change Q_HOST to query your host to get information
# SERVERS="192.168.1.2 127.0.0.1 192.168.1.2"
#
# (b) Setup USR, who is used to connect via ssh and already setup to connect
# via ssh-keys
# USR="nixcraft"
#
# (c)Show warning if server load average is below the limit for last 5 minute.
# setup LOAD_WARN as per your need, default is 5.0
#
# LOAD_WARN=5.0
#
# (d) Setup your network title using MYNETINFO
# MYNETINFO="My Network Info"
#
# (e) Save the file
#

# SSH SERVER HOST IPS
# Change this to query your host
# use local to get his machine.... No SSH will be used
# and / or server IPs
# for example
#SERVERS="local 192.168.123.3 192.168.123.117 192.168.123.119"
SERVERS="local"

# SSH USER, change me
USER="root"

# Show warning if server load average is below the limit for last 5 minute
LOAD_WARN=3.0
DISK_WARN=75
# Your network info
MYNETINFO="Servers"
#
# if it  is run as cgi we can do reload stuff too :D
PBY='Powered by <a href="http://www.proedgenetworks.com">Proedge</a>'


# font colours
GREEN='<font color="#008800">'
RED='<font color="#ff0000">'
NOC='</font>'
LSTART='<ul><li>'
LEND='</li></ul>'
# Local path to ssh and other bins
SSH="/usr/bin/ssh"
PING="/bin/ping"
NOW="$(date)"

## functions ##
writeHead(){
 echo '<HTML><HEAD><TITLE>Network Status</TITLE></HEAD>
 <BODY alink="#0066ff" bgcolor="#000000" link="#0000ff" text="#ccddee" vlink="#0033ff">'
 echo '<CENTER><H1>'
 echo "$MYNETINFO</H1>"
 echo "Generated on $NOW"
 echo '</CENTER>'

}

writeFoot(){
 echo "<HR><center>$PBY</center>"
  echo "</BODY></HTML>"
}

## main ##

#writeHead
#echo '<TABLE WIDTH=100% BORDER=2 BORDERCOLOR="#000080" CELLPADDING=4 CELLSPACING=4 FRAME=HSIDES RULES=NONE" >'
#echo '<TR VALIGN=TOP>'
for host in $SERVERS
do
  #echo '<TD WIDTH=33% BGCOLOR="#0099ff">'
  echo '<TD BGCOLOR="#000000">'
  if [ "$host" = "local" ]; then
         CMD=""
        host="localhost"
  else
        CMD="$SSH $USER@$host"
  fi
  SERVER_HOST_NAME="$($CMD hostname)"

#########################
#       Uptime          #
#########################
  SERVER_UPTIME="$($CMD uptime)"
  if $(echo $SERVER_UPTIME | grep -E "min|day" >/dev/null); then
    x=$(echo $SERVER_UPTIME | awk '{ print $3" " $4 " "$5}')
  else
    x=$(echo $SERVER_UPTIME | sed s/,//g| awk '{ print $3 " (hh:mm)"}')
  fi
  SERVER_UPTIME="$x"
#########################
#        Load           #
#########################
  SERVER_LOAD="$($CMD uptime |awk -F'average:' '{ print $2}')"
  x="$(echo $SERVER_LOAD | sed s/,//g | awk '{ print $2}')"
  y="$(echo "$x >= $LOAD_WARN" | bc)"
  [ "$y" == "1" ] && SERVER_LOAD="$RED $SERVER_LOAD (High) $NOC" || SERVER_LOAD="$GREEN $SERVER_LOAD (Ok) $NOC"

	# to get the date format string to pass with ssh
	# had to replace the spaces with dash then sed replace back to space
	SERVER_TIME="$($CMD date +%A--%b-%d-%Y--%r | sed 's/-/\ /g' )"
#########################
#   Total Processes     #
#########################
	SERVER_TOTAL_PROC="$($CMD ps axue | grep -vE "^USER|grep|ps" | wc -l)"
	# Set color to red if total processes over 1000
	[ ${SERVER_TOTAL_PROC} -gt 200 ] && SERVER_TOTAL_PROC="${RED}${SERVER_TOTAL_PROC}${NOC}" || SERVER_TOTAL_PROC="${GREEN}${SERVER_TOTAL_PROC}${NOC}"

###############################################
#   Total Established Network connections     #
###############################################
   SERVER_ESTAB_NET_CONNECTIONS="$($CMD netstat -t -u -n | egrep -v "Active|Proto" | egrep "ESTAB" | wc -l )"

#########################
#   Disk Usage          #
#########################

  $CMD df -hTP | grep -vE "^Filesystem|shm|tmpfs"  > /root/diskinfo
  rfs=""
  while read dsk
  do
	filesystype=$(echo $dsk  | awk '{print $2}') >/dev/null
	percentused=$(echo $dsk  | awk '{print $6}') >/dev/null
	totalandused=$(echo $dsk  | awk '{print $4" of "$3" Used"}') >/dev/null
	indicatorwidth=$(echo $dsk  | awk '{print $6}' | cut -d '%' -f1) >/dev/null
	mountpoint=$(echo $dsk  | awk '{print $7}') >/dev/null
	tst=`echo $percentused | cut -d % -f1`
	zz="$(echo "$tst >= $DISK_WARN" | bc)"
	[ "$zz" == "1" ] && indicatorimage="indicatorred.gif" || indicatorimage="indicator.gif"

	rfs=$(echo $rfs "<ul><li>" $mountpoint "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"$percentused \
        	"("$filesystype")<br><img src=\""$indicatorimage"\" height=\"4\" width=\"" $indicatorwidth \""><br><img src=\"graph.gif\" width=\"100\"> \
        	<br>" $totalandused "</li></ul>")
  done < /root/diskinfo

#########################
#   Memory/Swap         #
#########################
  SERVER_RAM="$($CMD free -mt | grep Mem: | awk '{ print $3 " MB" }')"
  SERVER_FREE_RAM="$($CMD free -mt | grep Mem: | awk '{ print $4 " MB" }')"
  rtotalram="$($CMD free -mt | grep Mem: | awk '{ print $2 " MB" }')"
# Get Total swap used
	SERVER_FREE_SWAP=$($CMD  cat /proc/meminfo | grep -i SwapFree | awk '{print $2}')
	SERVER_TOTAL_SWAP=$($CMD cat /proc/meminfo | grep -i SwapTotal | awk '{print $2}')
	SERVER_USED_SWAP=$(echo "$SERVER_TOTAL_SWAP-$SERVER_FREE_SWAP" | bc)
#   s1=`$CMD  cat /proc/meminfo | grep -i SwapFree | awk '{print $2}'` ; \
#        s2=`$CMD cat /proc/meminfo | grep -i SwapTotal | awk '{print $2}'` ; \
#        echo $s2 $s1 | awk '{print $1-$2}' >/dev/null

#########################
#       Ping Host       #
#########################
  $PING -c1  $host>/dev/null
  if [ "$?" != "0" ] ; then
    PING_RPLY="$RED Failed $NOC"
  else
    PING_RPLY="$GREEN Ok $NOC"
    echo "<b><u>${SERVER_HOST_NAME}</u></b><BR><br>"
    echo "Ping status: ${PING_RPLY}<BR>"
    echo "Time: ${SERVER_TIME}<BR>"
    echo "Uptime: ${SERVER_UPTIME} <BR>"
    echo "Load avarage: ${LSTART} ${SERVER_LOAD} ${LEND}"
    #echo "Total running process: $LSTART $SERVER_TOTAL_PROC $LEND"
    echo "Total running process: ${SERVER_TOTAL_PROC} <br><br>"
    echo "Disk status:"
    echo "$rfs"
    echo "Ram/swap status:<ul>"
    echo "<li>Used RAM: $SERVER_RAM</li>"
    echo "<li>Free RAM: $SERVER_FREE_RAM</li>"
    echo "<li>Total RAM: $rtotalram </li>"
	echo "<li>Total Swap: $SERVER_TOTAL_SWAP </li>"
   echo "<li>Total Swap used: $SERVER_USED_SWAP </li></ul>"
  fi

  echo "</td>"
done
#echo "</tr>"
#  echo "</tr></table>"
#writeFoot
