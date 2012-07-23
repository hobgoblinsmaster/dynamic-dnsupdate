<?php

error_reporting('ALL');

// Include pear if isn't in include_path
// set_include_path(get_include_path() . PATH_SEPARATOR . './include');

// My Domains - Separate with ";"
define('DOMAIN','novius.com;calyce.fr;freeserver.kr;1.168.192.in-addr.arpa');

// Nameserver
define('NS1','192.168.1.253');

// signTSIG key (NULL = not use signTSIG)
define('SIGNTSIG',NULL);
// signSIG0 key (NULL = not use signSIG0 file)
define('SIGNSIGO',NULL);


// TTL default value
define('TTL_A', 3600);
define('TTL_AAAA', 3600);
define('TTL_CNAME', 10800);
define('TTL_NS', 3600);
define('TTL_TXT', 10800);
define('TTL_PTR', 10800);
define('TTL_MX', 3600);

// Version Information
define('VERSION','Ver. 2.0');

// Copyright
define('LICENSE','License is GNU GPL v3. Computer Science Laboratory Network (CSLab.net) - <a href="http://www.calyce.fr">Calyce</a> - <a href="http://www.novius.com">Novius</a>');
?>
