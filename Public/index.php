<?php
/*************************************************
 * Titan-2 Mini Framework
 * Simple and Modern Web Application Framework
 *
 * Author 	: Turan Karatuğ
 * Web 		: http://www.titanphp.com
 * Docs 	: http://kilavuz.titanphp.com 
 * Version 	: 2.0.0
 * Github	: http://github.com/tkaratug/titan2
 * License	: MIT	
 *
 *************************************************/

// Require Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Require Starter
require_once __DIR__ . '/../System/Kernel/Starter.php';

// Run Kernel
new System\Kernel\Kernel();