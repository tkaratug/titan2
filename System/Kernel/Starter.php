<?php
/*************************************************
 * Titan-2 Mini Framework
 * System Starter
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/

// Constants
require_once 'Constants.php';

// Helpers
require_once 'Helpers.php';

// Error Reporting
if (ENV == 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Set default timezone
date_default_timezone_set(TIMEZONE);