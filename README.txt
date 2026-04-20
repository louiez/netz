run the midnight cron script
add entry to /etc/sudoers for the user apache runs under
this allows ping-test-web.php to be run from the apache user

on one redhat install using SElinux I had to run
setsebool httpd_disable_trans 1
setsebool httpd_tty_comm 1
setsebool -P httpd_can_network_connect 1
setsebool -P httpd_builtin_scripting 1

************  Apache  ***************
Username and password are passed in clear text....
You may want to enable to force SSL if your server has SSL enabled..
There may be other ways todo this but this is how I did it

in the Alais mod section  of httpd.conf i put

Alias /netz/ "/usr/local/apache/htdocs/proedgenetworks/netz/"
	<Directory "/usr/local/apache/htdocs/proedgenetworks/netz">
                AllowOverride All
                Order allow,deny
                Allow from all
                DirectoryIndex index.php index.html napa_all.php
        </Directory>

then in the Virtual host section i put

Alias /netz/ "/usr/local/apache/htdocs/proedgenetworks/netz/"
	<Directory "/usr/local/apache/htdocs/proedgenetworks/netz">
                AllowOverride All
                Order allow,deny
                Allow from all
                DirectoryIndex index.php index.html napa_all.php
        </Directory>
