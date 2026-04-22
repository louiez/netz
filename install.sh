#!/bin/bash
. `pwd`/color.sh 2>/dev/null
export PATH="/sbin:/usr/sbin:/bin:/usr/bin:/usr/X11R6/bin"

AUTO=0
CONFIG_FILE="install.conf"

if [ "$1" = "--auto" ]; then
	AUTO=1
	[ -n "$2" ] && CONFIG_FILE="$2"

	if [ ! -f "$CONFIG_FILE" ]; then
		echo "Auto mode requested but config file not found: $CONFIG_FILE"
		exit 1
	fi

	. "$CONFIG_FILE"
fi

prompt_value() {
	local varname="$1"
	local prompt="$2"
	local default="$3"
	local secret="$4"
	local current="$5"
	local value=""

	if [ "$AUTO" -eq 1 ]; then
		value="$current"
		if [ -z "$value" ] && [ -n "$default" ]; then
			value="$default"
		fi
	else
		if [ "$secret" = "1" ]; then
			if [ -n "$default" ]; then
				echo -ne "$MAGENTA ${prompt} [hidden default]: $AOFF "
			else
				echo -ne "$MAGENTA ${prompt}: $AOFF "
			fi
			read -s value
			echo ""
		else
			if [ -n "$default" ]; then
				echo -ne "$MAGENTA ${prompt} [${default}]: $AOFF "
			else
				echo -ne "$MAGENTA ${prompt}: $AOFF "
			fi
			read value
		fi

		if [ -z "$value" ]; then
			if [ -n "$current" ]; then
				value="$current"
			else
				value="$default"
			fi
		fi
	fi

	eval "$varname=\"\$value\""
}

prompt_yesno() {
	local varname="$1"
	local prompt="$2"
	local default="$3"
	local current="$4"
	local ans=""

	if [ "$AUTO" -eq 1 ]; then
		ans="$current"
		[ -z "$ans" ] && ans="$default"
	else
		echo -ne "$MAGENTA ${prompt} [${default}]: $AOFF "
		read ans
		[ -z "$ans" ] && ans="$default"
	fi

	case "$ans" in
		y|Y|yes|YES) ans="y" ;;
		*) ans="n" ;;
	esac

	eval "$varname=\"\$ans\""
}

# this script needs to run in it's directory... lets check
xx="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
script_dir=$(printf '%s\n' "${xx##*/}")
cur_dir=$(printf '%s\n' "${PWD##*/}")
[ "${script_dir}" != "${cur_dir}" ] && echo "run this script from the netz directory (${xx})" && exit

# Sanity checks
type php >/dev/null 2>&1
if [ $? -ne 0 ]; then
	echo -e "$RED Unable to locate PHP....Exiting"
	exit 1
fi

type mysql >/dev/null 2>&1
if [ $? -ne 0 ]; then
	echo -e "$RED Unable to locate mysql....Exiting"
	exit 1
fi

type rrdtool >/dev/null 2>&1
if [ $? -ne 0 ]; then
	echo -e "$RED rrdtool don't seem to be installed "
	echo "NETz uses rrdtool to create charts."
	echo "Charts will not be available until rrdtool is installed"
	echo -e "\n see http://oss.oetiker.ch/rrdtool/ $AOFF "
fi

# find web server user
webuser=""
if systemctl is-active --quiet apache2; then
	webuser=`ps axho user,comm | egrep 'httpd|apache' | egrep -v "grep|root" | sort | uniq | awk '{print $1}'`
elif systemctl is-active --quiet lighttpd; then
	webuser=`ps axho user,comm | egrep 'httpd|lighttpd' | egrep -v "grep|root" | sort | uniq | awk '{print $1}'`
elif systemctl is-active --quiet nginx; then
	webuser=`ps axho user,comm | egrep 'nginx' | egrep -v "grep|root" | sort | uniq | awk '{print $1}'`
fi

if [ -z "$webuser" ]; then
	prompt_value webuser "Unable to auto-detect web server user. Enter web server user" "www-data" "0" "$WEB_USER"
fi

netzroot=`pwd`

cd "$netzroot" || exit 1

prompt_value mysqlroot     "Enter Mysql root username" "" "0" "$MYSQL_ROOT_USER"
if [ -z "$mysqlroot" ]; then
	echo -e "$RED You must enter a Root username for Mysql.... Exiting $AOFF"
	exit 1
fi

prompt_value mysqlrootpass "Enter Mysql root password" "" "1" "$MYSQL_ROOT_PASS"

prompt_value mysqlnetz     "Enter a Mysql username you want to create for NETz" "" "0" "$NETZ_DB_USER"
if [ -z "$mysqlnetz" ]; then
	echo -e "$RED You must enter a username for NETz Database.... Exiting $AOFF"
	exit 1
fi

prompt_value mysqlnetzpass "Enter a password to use for user $mysqlnetz" "" "1" "$NETZ_DB_PASS"
if [ -z "$mysqlnetzpass" ]; then
	echo -e "$RED You must enter a password for NETz user $mysqlnetz.... Exiting $AOFF"
	exit 1
fi

prompt_value server      "Enter Mysql server" "localhost" "0" "$MYSQL_SERVER"
prompt_value database    "Enter Database name" "NETz" "0" "$NETZ_DB_NAME"
prompt_value emailserver "Enter Email server" "localhost" "0" "$EMAIL_SERVER"
prompt_value emailport   "Enter email server port" "25" "0" "$EMAIL_PORT"
prompt_value adminemail  "Enter email for Admin" "" "0" "$ADMIN_EMAIL"

if [ -z "$adminemail" ]; then
	echo -e "$RED You must enter an Admin email.... Exiting $AOFF"
	exit 1
fi

dbsql_tmp="${netzroot}/netz_db.sql.tmp"
cp "${netzroot}/netz_db.sql" "$dbsql_tmp" || exit 1
sed -i "s/%ADMIN_USER%/${adminemail}/g" "$dbsql_tmp"
sed -i "s/NETz/${database}/g" "$dbsql_tmp"

echo -e "$BLUE Initializing database..... $AOFF"
echo "quit" | mysql -u "$mysqlroot" -p"$mysqlrootpass" -s "$database" 2>/dev/null
if [ $? -eq 0 ]; then
	echo -e "$RED $database already exists $AOFF"
else
	echo -e "$BLUE creating database $database $AOFF"
	echo "create database $database;" | mysql -u "$mysqlroot" -p"$mysqlrootpass" || exit 1
	mysql -u "$mysqlroot" -p"$mysqlrootpass" "$database" < "$dbsql_tmp" || exit 1
fi
rm -f "$dbsql_tmp"

echo ""
echo -e "$BLUE Creating NETz user $mysqlnetz with grant select,insert,update,delete on $database $AOFF"
grant_tmp="${netzroot}/grant.sql"
echo "grant select,insert,update,delete,create,drop on ${database}.* to '${mysqlnetz}'@'localhost' identified by '${mysqlnetzpass}';" > "$grant_tmp"
mysql -u "$mysqlroot" -p"$mysqlrootpass" "$database" < "$grant_tmp"
rm -f "$grant_tmp"

prompt_value netzlog    "Enter Directory to write Log files to" "/usr/netz/logs/" "0" "$LOG_DIR"
prompt_value netzupload "Enter Directory to save Uploaded files to" "/usr/netz/uploads/" "0" "$UPLOAD_DIR"

if [ -d "$netzlog" ]; then
	chmod -R 744 "$netzlog"
	chown -R "$webuser:$webuser" "$netzlog"
else
	prompt_yesno ans "$netzlog doesn't exist.. Create it ?" "n" "$CREATE_LOG_DIR"
	if [ "$ans" = "y" ]; then
		mkdir -p "$netzlog"
		chmod -R 744 "$netzlog"
		chown -R "$webuser:$webuser" "$netzlog"
	else
		echo "Install aborted"
		exit 2
	fi
fi

if [ -d "$netzupload" ]; then
	chmod -R 744 "$netzupload"
	chown -R "$webuser:$webuser" "$netzupload"
else
	prompt_yesno ans "$netzupload doesn't exist.. Create it ?" "n" "$CREATE_UPLOAD_DIR"
	if [ "$ans" = "y" ]; then
		mkdir -p "$netzupload"
		chmod -R 744 "$netzupload"
		chown -R "$webuser:$webuser" "$netzupload"
	else
		echo "Install aborted"
		exit 2
	fi
fi

echo ""
echo -e "$BLUE Creating config file site-monitor.conf.php $AOFF"
cat > "$netzroot/site-monitor.conf.php" <<EOF
<?php
define("SITE_INFO_TABLE","SITEDATA");                      // Main table with site info
define("SITE_MON_TABLE","MONLOGS");                        // table used to store monitor data
define("SITE_ID_DEFAULT","SITE_ID");                       // site id field must be unique
define("SITE_IP_DEFAULT","LAN_GATEWAY");                   // Default field name in main table that stores IP to monitor

define("NETZ_DB_SERVER","$server");
define("NETZ_DB_USERNAME","$mysqlnetz");
define("NETZ_DB_PASSWORD","$mysqlnetzpass");
define("NETZ_DATABASE","$database");

define("ALLOW_DOCUMENT_UPLOADS",1);                        // allow site and group image uploads to server
\$site_down_tb ="DOWNSITES";                               // table to store down and or chronic sites
\$netzlogs = "$netzlog/";                                  // Directory where netz logs
\$basedir = "$netzroot/";                                  // Directory where netz lives
\$uploadDir = "$netzupload/";                              // Directory to save uploaded images
define("SITE_ADMIN_EMAIL","$adminemail");                  // Site admin email
\$montype="icmp";                                          // connect with ICMP ping
\$icmpcount = 4;

\$monitor_timeout = 2;                                     // seconds to timeout each connection try
\$alert_cycles = "3";                                      // number of ping sets to fail before alert is sent

\$moncycleinterval = "10";
\$logdays="30";                                            // Number of days to keep monitor logs
\$email_server = "$emailserver";                           // email server to forward alerts
\$email_server_port = "$emailport";                        // email server port

define("STYLESHEET",0);
define("SUPPORT",1);
?>
EOF

echo "Changing Ownership on netz config file to $webuser"
chown "$webuser:$webuser" "$netzroot/site-monitor.conf.php"
echo "changing permissions on $netzroot/site-monitor.conf.php to 755"
chmod 755 "$netzroot/site-monitor.conf.php"

echo "Running $netzroot/run-site-cron.php"
"$netzroot/run-site-cron.php"

echo ""
echo -e "$BGYELLOW $BLACK I am going to try to add permission to allow NETz to run ping-test-web.php"
echo -e "Using sudo..... by adding $webuser to run ping-test-web.php in /etc/sudoers $AOFF"
echo ""

prompt_yesno ans "Is this ok ?" "Y" "$ADD_SUDOERS"
if [ "$ans" = "y" ]; then
	sudotest=`grep 'ping-test-web.php' /etc/sudoers`
	if [ -z "$sudotest" ]; then
		echo "$webuser ALL=(root) NOPASSWD:  $netzroot/ping-test-web.php" >> /etc/sudoers
	else
		echo "Permission was already added....nothing changed"
	fi
fi
echo -e "$AOFF"

echo -e "$MAGENTA I need to modify the crontab and add daily maint and alerting $AOFF"
prompt_yesno ans "Is this ok ?" "Y" "$ADD_CRON"
if [ "$ans" = "y" ]; then
	rm -f /tmp/cron.tmp
	crontest=`crontab -l 2>/dev/null | grep run-site-cron.php`
	if [ -z "${crontest}" ]; then
		echo "@daily $netzroot/run-site-cron.php  1>/dev/null 2>/dev/null" > /tmp/cron.tmp
	fi

	crontest=`crontab -l 2>/dev/null | grep site-alert.php`
	if [ -z "${crontest}" ]; then
		echo "0,5,10,15,20,25,30,35,40,45,50,55 * * * *  $netzroot/site-alert.php  1>/dev/null 2>/dev/null" >> /tmp/cron.tmp
	fi

	crontab -l 2>/dev/null >> /tmp/cron.tmp
	crontab /tmp/cron.tmp
	rm -f /tmp/cron.tmp
fi

echo "setting permissions of $netzroot directory"
cd "$netzroot"
cd ..
chown -R "root:$webuser" "$netzroot"
chmod -R 750 "$netzroot"

echo "setting permissions of $netzroot/rrd directory"
chmod -R 770 "$netzroot/rrd" 2>/dev/null
echo "setting permissions of $netzroot/querys directory"
chmod -R 770 "$netzroot/querys" 2>/dev/null
echo "setting permissions of $netzroot/service-type.txt"
chmod -R 770 "$netzroot/service-type.txt" 2>/dev/null
echo "setting permissions of $netzroot/region.txt"
chmod -R 770 "$netzroot/region.txt" 2>/dev/null
echo "setting permissions of $netzroot/fsr.txt"
chmod -R 770 "$netzroot/fsr.txt" 2>/dev/null
echo "setting permissions of $netzroot/site-type.txt"
chmod -R 770 "$netzroot/site-type.txt" 2>/dev/null
echo "setting permissions of $netzroot/plugins.ini"
chmod 770 "$netzroot/plugins.ini" 2>/dev/null
echo "setting permissions of $netzroot/site-monitor.conf.php"
chmod 770 "$netzroot/site-monitor.conf.php" 2>/dev/null
echo "setting permissions of $netzroot/*/plugin.ini"
chmod 770 "$netzroot/plugins/*/plugin.ini" 2>/dev/null
echo "setting permissions of $netzupload directory"
chown -R "root:$webuser" "$netzupload"
chmod -R 770 "$netzupload"
echo "setting permissions of $netzlog directory"
chown -R "root:$webuser" "$netzlog"
chmod -R 770 "$netzlog"

echo "Install complete !"
echo -e "default user: admin \nDefault Password: password"

echo "just a few tips"
echo "php.ini should have "
echo "file_uploads = On"
echo "upload_max_filesize = 10M   this depends on how big you will have attachments"
echo "post_max_size = 10M         I make this the same as the upload size... but your choice"
echo ""
echo "*** Apache config ****"
echo "Apache needs to have \"AllowOverride all\" for the netz directory($(printf '%s\n' "${PWD##*/}"))"
echo "this is to read the .htaccess file..... "
echo "if you do not want to allowoverride add to the httpd.conf or apache2.conf"
echo "<Directory \"/var/www/netz/\">
        DirectoryIndex main.php
        php_flag register_globals off
        SetEnv NETZ_ROOT_PATH $(printf '%s\n' "${PWD##*/}")
</Directory>"
