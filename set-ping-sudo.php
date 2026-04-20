#!/usr/bin/php -q
<?php
/*###############################################################
        NETz Network Management system                          #
        http://www.proedgenetworks.com/netz                     #
                                                                #
                                                                #
        Copyright (C) 2005-2006 Louie Zarrella                  #
        louiez@proedgenetworks.com                              #
                                                                #
        Released under the GNU General Public License           #
        Copy of License available at :                          #
        http://www.gnu.org/copyleft/gpl.html                    #
###############################################################*/
include_once('site-monitor.conf.php');

system('echo "nobody ALL=(root) NOPASSWD: ' . $basedir . 'ping-test-web.php" >> /etc/sudoers');

?>
