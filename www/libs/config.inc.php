<?php
 // optimalisatie
 ini_set('output_buffering','1');

 setlocale(LC_MONETARY, 'nl_NL.UTF-8@euro');

 class object {};
 $CFG = new object;

 $CFG->debug       = false;

 $CFG->dbhost      = 'localhost';     // hostname of db server
 $CFG->dbname      = 'domotica';           // database name
 $CFG->dbuser      = 'domotica';           // database user
 $CFG->dbpass      = '****';             // password for this user

 $CFG->hostname      = 'domotica.i76.nl';  // without ending slash
 $CFG->wwwroot       = 'http://'.$CFG->hostname;
 $CFG->dirroot       = '/home/i76/domotica/www';     
 $CFG->libroot       = $CFG->dirroot.'/libs';     
 
 // qbus stuff
 // ip and port
 $CFG->qbus_url = "http://192.168.1.3:8444/index.cgi";
 // menus to get module stati from
 $CFG->qbus_menus = array("M000");
 
?>
