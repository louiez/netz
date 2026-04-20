#!/bin/bash
. `pwd`/color.sh 2>/dev/null
export PATH="/sbin:/usr/sbin:/bin:/usr/bin:/usr/X11R6/bin"

# this script needs to run in it's directory... lets check
xx="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
script_dir=$(printf '%s\n' "${xx##*/}")
cur_dir=$(printf '%s\n' "${PWD##*/}")
# if they don't match... exit
[ "${script_dir}" != "${cur_dir}" ] && echo "run this script from the netz directory (${xx})" && exit

# Sanity checks
type php 2>/dev/null 1>/dev/null
if [ $? -ne 0 ]; then
	php 2>/dev/null 1>/dev/null
	if [ $? -ne 0 ]; then
		echo -e "$RED Unable to locate PHP....Exiting"
		exit 1
	fi
fi
type mysql 2>/dev/null 1>/dev/null
if [ $? -ne 0 ]; then
        mysql 2>/dev/null 1>/dev/null
        if [ $? -ne 0 ]; then
                echo -e "$RED Unable to locate mysql....Exiting"
		exit 1
        fi
fi

type rrdtool 2>/dev/null 1>/dev/null
if [ $? -ne 0 ]; then
	rrdtool 2>/dev/null 1>/dev/null
	if [ $? -ne 0 ]; then
        	echo -e "$RED rrdtool don't seem to be installed "
		echo "NETz uses rrdtool to create charts. "
		echo "Charts will not be available untill rrdtool is installed "
		echo -e "\n see  http://oss.oetiker.ch/rrdtool/ $AOFF "
	fi

fi

# find web server user

if systemctl is-active --quiet apache2; then	# apache2
	webuser=`ps axho user,comm |egrep 'httpd|apache' |egrep -v "grep|root"|sort|uniq|awk '{print $1}'`	
elif  systemctl is-active --quiet lighttpd; then	# lighttpd
	webuser=`ps axho user,comm |egrep 'httpd|lighttpd' |egrep -v "grep|root"|sort|uniq|awk '{print $1}'`
elif systemctl is-active --quiet nginx; then	# nginx
	 webuser=`ps axho user,comm |egrep 'nginx' |egrep -v "grep|root"|sort|uniq|awk '{print $1}'`
fi

netzroot=`pwd`
#echo "setting permissions of $netzroot directory"
# Move below the Netz install directory and set permissions
#cd ..
#chown -R root:$webuser $netzroot
#chmod -R 750 $netzroot



#chmod -R 770 $netzroot/rrd
#chmod -R 770 $netzroot/querys
#chmod -R 770 $netzroot/service-type.txt
#chmod -R 770 $netzroot/region.txt
#chmod -R 770 $netzroot/fsr.txt
#chmod -R 770 $netzroot/site-type.txt
#chmod 770 $netzroot/plugins.ini
#chmod 770 $netzroot/site-monitor.conf.php
#chmod 770 $netzroot/plugins/*/plugin.ini
#chown -R root:$webuser $uploaddir
#chmod -R 770 $uploaddir
#chown -R root:$webuser $logdir
#chmod -R 770 $logdir


cd $netzroot

cd $netzroot
#ln -s $netzroot/main.php $netzroot/index.html
#chown $webuser:$webuser $netzroot/index.html

echo -e "$MAGENTA Enter Mysql root username: $AOFF \c "
read mysqlroot
if [ "$mysqlroot" = "" ]; then
        echo -e "$RED You must enter a Root username for Mysql.... Exiting $AOFF"
        exit 1
fi
echo -e "$MAGENTA Enter Mysql root password: $AOFF \c "
read mysqlrootpass

echo ""
echo -e "$MAGENTA Enter a Mysql username you want to create for NETz: $AOFF \c "
read mysqlnetz
if [ "$mysqlnetz" = "" ]; then
        echo "$RED You must enter a username for NETz Database.... Exiting $AOFF"
        exit 1
fi
echo -e "$MAGENTA Enter a password to use for user $mysqlnetz: $AOFF \c "
read mysqlnetzpass
if [ "$mysqlnetzpass" = "" ]; then
        echo "$RED You must enter a password for NETz user $mysqlnetz.... Exiting $AOFF"
        exit 1
fi
echo ""
echo -e "$MAGENTA Enter Mysql server [localhost]: $AOFF \c "
read server
if [ "$server" = "" ]; then
        server="localhost"
        echo "Using $server"
fi

echo -e "$MAGENTA Enter Database name [NETz]: $AOFF \c "
read database
if [ "$database" = "" ]; then
        database="NETz"
	echo "Using $database"
fi


echo -e "$MAGENTA Enter Email server [localhost]: $AOFF \c "
read emailserver
if [ "$emailserver" = "" ]; then
        emailserver="localhost"
        echo "Using $emailserver"
fi

echo -e "$MAGENTA Enter email server port [25]: $AOFF \c "
read emailport
if [ "$emailport" = "" ]; then
        emailport="25"
        echo "Using Standard port $emailport"
fi

echo -e "$MAGENTA Enter email for Admin : $AOFF \c "
read adminemail

# Add Email user to the netz_db.sql file before importing it
sed --in-place  "s/%ADMIN_USER%/${adminemail}/" ${netzroot}/netz_db.sql
sed --in-place  "s/NETz/${database}/" ${netzroot}/netz_db.sql

echo -e "$BLUE Initializing database..... $AOFF"
echo "quit" | mysql  -u $mysqlroot -p$mysqlrootpass -s $database 2>/dev/null	
if [ $? -eq 0 ]; then
        echo -e "$RED $database already exists $AOFF"
else
        echo -e "$BLUE creating database $database $AOFF"
        echo "create database $database;" | mysql -u $mysqlroot -p$mysqlrootpass
        mysql -u $mysqlroot -p$mysqlrootpass $database < $netzroot/netz_db.sql

fi
# Grant permissions
echo ""
echo -e "$BLUE Creating NETz user $mysqlnetz with grant select,insert,update,delete on $database $AOFF"
echo -e "grant select,insert,update,delete,create,drop on $database.* to '$mysqlnetz'@localhost identified by '$mysqlnetzpass';" > $netzroot/grant.sql
mysql -u $mysqlroot -p$mysqlrootpass $database < $netzroot/grant.sql

# Create Log directory if dosen't exist
echo -e "$MAGENTA Enter Directory to write Log files to [/usr/netz/logs/]: $AOFF \c "
read netzlog
if [ "$netzlog" = "" ]; then
        netzlog="/usr/netz/logs/"
        echo "Using default log directory $netzlog"
fi
# lets see if it is already there
if [ -d "$netzlog" ]; then
        chmod -R 744 $netzlog
        chown -R $webuser:$webuser $netzlog
else
	# Ok it is not there ....lets be nice...ask user if we should create
	echo -e "$MAGENTA $netzlog dosen't exist.. Create it ? (y/n)  [n]$AOFF \c "
	read ans
	if [ "$ans" = "yes" ] || [ "$ans" = "y" ]; then # nice user said yes
		mkdir -p $netzlog
		chmod -R 744 $netzlog
		chown -R $webuser:$webuser $netzlog
	else
		echo "Install aborted"
		exit 2
	fi
fi
# Create Upload directory if dosen't exist
echo -e "$MAGENTA Enter Directory to save Uploaded files to [/usr/netz/uploads/]: $AOFF \c "
read netzupload
if [ "$netzupload" = "" ]; then
        netzupload="/usr/netz/uploads/"
        echo "Using default upload directory $netzupload"
fi
# lets see if Upload directory is already there
if [ -d "$netzupload" ]; then
        chmod -R 744 $netzupload
        chown -R $webuser:$webuser $netzupload
else
        # Ok it is not there ....lets be nice...ask user if we should create
        echo -e "$MAGENTA $netzupload dosen't exist.. Create it ? (y/n)  [n]$AOFF \c "
        read ans
        if [ "$ans" = "yes" ] || [ "$ans" = "y" ]; then # nice user said yes
                mkdir -p $netzupload
                chmod -R 744 $netzupload
                chown -R $webuser:$webuser $netzupload
        else
                echo "Install aborted"
                exit 2
        fi
fi
# Write config file
echo ""
echo -e "$BLUE Creating config file site-monitor.conf.php $AOFF"
echo "<?php" > $netzroot/site-monitor.conf.php
echo -e "define(\"SITE_INFO_TABLE\",\"SITEDATA\");                      // Main table with site info " >> $netzroot/site-monitor.conf.php
echo -e "define(\"SITE_MON_TABLE\",\"MONLOGS\");                        // table used to store monitor data " >> $netzroot/site-monitor.conf.php
echo -e "define(\"SITE_ID_DEFAULT\",\"SITE_ID\");                       // site id field must be unique " >> $netzroot/site-monitor.conf.php

echo -e "define(\"SITE_IP_DEFAULT\",\"LAN_GATEWAY\");           // Default feild name in main table that stores IP to monitor " >> $netzroot/site-monitor.conf.php

echo -e "define(\"NETZ_DB_SERVER\",\"$server\"); " >> $netzroot/site-monitor.conf.php
echo -e "define(\"NETZ_DB_USERNAME\",\"$mysqlnetz\"); " >> $netzroot/site-monitor.conf.php
echo -e "define(\"NETZ_DB_PASSWORD\",\"$mysqlnetzpass\"); " >> $netzroot/site-monitor.conf.php

echo -e "define(\"NETZ_DATABASE\",\"$database\"); " >> $netzroot/site-monitor.conf.php


echo -e "define(\"ALLOW_DOCUMENT_UPLOADS\",1);                     // allow site and group image uploads to server " >> $netzroot/site-monitor.conf.php
echo -e "\$site_down_tb =\"DOWNSITES\";                              // table to store down and or cronic sites " >> $netzroot/site-monitor.conf.php
echo -e "\$netzlogs = \"$netzlog/\";            // Directory where netz logs " >> $netzroot/site-monitor.conf.php
echo -e "\$basedir = \"$netzroot/\";            // Directory where netz lives " >> $netzroot/site-monitor.conf.php
echo -e "\$uploadDir = \"$netzupload/\";                                    // Directory to same uploaded images " >> $netzroot/site-monitor.conf.php
echo -e "define(\"SITE_ADMIN_EMAIL\",\"$adminemail \");        // Site admin email" >> $netzroot/site-monitor.conf.php
echo -e "\$montype=\"icmp\";                                        // connect with ICMP ping " >> $netzroot/site-monitor.conf.php
echo -e "\$icmpcount = 4; \n" >> $netzroot/site-monitor.conf.php

echo -e "\$monitor_timeout = 2;                                     // seconds to timeout each connection try " >> $netzroot/site-monitor.conf.php
echo -e "\$alert_cycles = \"3\";                // number of ping sets to fail before alert is sent " >> $netzroot/site-monitor.conf.php

echo -e "\$moncycleinterval = \"10\"; \n" >> $netzroot/site-monitor.conf.php
echo -e "\$logdays=\"30\";                                            // Number of days to keep monitor logs " >> $netzroot/site-monitor.conf.php
echo -e "\$email_server = \"$emailserver\";                         // email server to forward alerts " >> $netzroot/site-monitor.conf.php
echo -e "\$email_server_port = \"$emailport\";                                // email server port " >> $netzroot/site-monitor.conf.php

echo -e "define(\"STYLESHEET\",0); " >> $netzroot/site-monitor.conf.php
echo -e "define(\"SUPPORT\",1); " >> $netzroot/site-monitor.conf.php
echo -e "?> " >> $netzroot/site-monitor.conf.php
echo "Changing Ownership on netz directory to $webuser"
chown -R $webuser:$webuser $netzroot/site-monitor.conf.php
echo "changing permissions on $netzroot/site-monitor.conf.php to 755"
chmod 755 $netzroot/site-monitor.conf.php
echo " Running $netzroot/run-site-cron.php"
$netzroot/run-site-cron.php
echo ""
echo -e "$BGYELLOW $BLACK I am going to try to add permission to allow NETz to run ping-test-web.php"
echo -e " Using sudo..... by adding $webuser to run ping-test-web.php in /etc/sudoers $AOFF"
echo ""
echo -e "$MAGENTA Is this ok ? Y/N [Y]: $AOFF \c "
read ans
if [ $ans = "y" ] || [ $ans = "Y" ]; then
	sudotest=`grep 'ping-test-web.php' /etc/sudoers`
	if [ "$sudotest" = "" ]; then
		echo -e "$webuser ALL=(root) NOPASSWD:  $netzroot/ping-test-web.php" >> /etc/sudoers
	else
		echo "Permission was already added....nothing changed"
	fi
fi
echo -e "$AOFF"

echo -e "$MAGENTA I need to modify the crontab and add daily maint and alerting $AOFF  "
echo -e "$MAGENTA Is this ok ? Y/N [Y]: $AOFF \c "
read ans
if [ $ans = "y" ] || [ $ans = "Y" ]; then
	crontest=`crontab -l | grep run-site-cron.php`
	if [ "${crontest}" = "" ]; then
		echo "@daily $netzroot/run-site-cron.php  1>/dev/null 2>/dev/null" > /tmp/cron.tmp	
	fi
	crontest=""
	crontest=`crontab -l | grep site-alert.php`
	if [ "${crontest}" = "" ]; then
		echo "0,5,10,15,20,25,30,35,40,45,50,55 * * * *  $netzroot/site-alert.php  1>/dev/null 2>/dev/null" >> /tmp/cron.tmp
	fi
	crontab -l >> /tmp/cron.tmp
	crontab /tmp/cron.tmp
fi

echo "setting permissions of $netzroot directory"
# Move below the Netz install directory and set permissions
cd $netzroot
cd ..
chown -R root:$webuser $netzroot
chmod -R 750 $netzroot


echo "setting permissions of $netzroot/rrd directory"
chmod -R 770 $netzroot/rrd
echo "setting permissions of $netzroot/querys directory"
chmod -R 770 $netzroot/querys
echo "setting permissions of $netzroot/service-type.txt"
chmod -R 770 $netzroot/service-type.txt
echo "setting permissions of $netzroot/region.txt"
chmod -R 770 $netzroot/region.txt
echo "setting permissions of $netzroot/fsr.txt"
chmod -R 770 $netzroot/fsr.txt
echo "setting permissions of $netzroot/site-type.txt"
chmod -R 770 $netzroot/site-type.txt
echo "setting permissions of $netzroot/plugins.ini"
chmod 770 $netzroot/plugins.ini
echo "setting permissions of $netzroot/site-monitor.conf.php"
chmod 770 $netzroot/site-monitor.conf.php
echo "setting permissions of $netzroot/*/plugin.ini"
chmod 770 $netzroot/plugins/*/plugin.ini
echo "setting permissions of $netzupload directory"
chown -R root:$webuser $netzupload
chmod -R 770 $netzupload
echo "setting permissions of $netzlog directory"
chown -R root:$webuser $netzlog
chmod -R 770 $netzlog

echo "Install complete !"
echo -e "default user: admin \nDefault Password: password"

echo "just a few tips"
echo "php.ini should have "
echo "file_uploads = On"
echo "upload_max_filesize = 10M   this dependes on how big you will have attachments"
echo "post_max_size = 10M         I make this thae same as the upload size... but your choice"
echo ""
echo "*** Apache config ****"
echo "Apache needs to have \"AllowOverride all\" for the netz directory($(printf '%s\n' "${PWD##*/}"))"
echo "this is to read the .htaccess file..... "
echo "if you do not want to allowoverride add to the httpd.conf or apache2.conf"
echo "<Directory "/var/www/netz/">
        DirectoryIndex main.php
        php_flag register_globals off
        SetEnv NETZ_ROOT_PATH $(printf '%s\n' "${PWD##*/}")
</Directory>"

