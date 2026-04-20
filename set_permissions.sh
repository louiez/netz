apacheuser=`ps axho user,comm|grep -E "httpd|apache"|uniq|awk 'END {print $1}'`
netzroot=`pwd`
uploaddir=`grep uploadDir site-monitor.conf.php | cut -d '"' -f 2`
if [ ${uploaddir} = "/" ] || [ ${netzroot} = "/" ]; then
	echo -e "A directory is set for / \n Exiting"
	exit 1
fi
logdir=`grep netzlogs site-monitor.conf.php | cut -d '"' -f 2`
if [ "$apacheuser" != "" ] || [ "$netzroot" != "" ] || [ "$uploaddir" != "" ] || [ "$logdir" != "" ]; then
echo root:$apacheuser $netzroot
        chown -R root:$apacheuser $netzroot
        chmod -R 750 $netzroot/*.php
	chmod -R 750 $netzroot/*.sh
        chmod -R 750 $netzroot/*.js
        chmod -R 760 $netzroot/rrd
        chmod -R 760 $netzroot/querys
        chmod 760 $netzroot/service-type.txt
        chmod 760 $netzroot/region.txt
        chmod 760 $netzroot/fsr.txt
        chmod 760 $netzroot/site-type.txt
        chmod 760 $netzroot/plugins.ini
        chmod 770 $netzroot/site-monitor.conf.php
	chmod 760 $netzroot/plugins/*/*
	chmod 750 $netzroot/plugins/*/*.php
	chmod 750 $netzroot/plugins/*/*.sh
        chmod 750 $netzroot/plugins/*/*.js
echo root:$apacheuser $uploaddir
        chown -R root:$apacheuser $uploaddir
        chmod -R 760 $uploaddir
echo root:$apacheuser $logdir
        chown -R root:$apacheuser $logdir
        chmod -R 760 $logdir
fi
