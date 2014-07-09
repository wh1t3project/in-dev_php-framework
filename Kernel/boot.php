<?php
// Kernel bootloader. 
/* Load all kernel modules

Copyright 2014 Gaël Stébenne (alias Wh1t3c0d3r)

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

// Note about how the kernel works:
// Since there are critical dependencies, we cannot load all the kernel modules using a generic listing.
// Instead, we load each module separately with a manual 'include'. This also prevent problems if 
// someone put a module in the directory by error because it won't be loaded at all.

// Core functions. DO NOT ALTER!
if (! DEFINED('INSCRIPT')) {echo 'Direct access denied'; exit(1);}

if ($CONFIG['debug_panic'] === FALSE and file_exists($GLOBALS['CONFIG']['app_real_location']."/panic.log")) {
	echo "Kernel panic as been raised and 'debug_panic' is disabled. Cannot execute.";
	exit(9999);}
	
$TEMP = array();
date_default_timezone_set($CONFIG['timezone']) or die ("Error while setting timezone. Please check config.");

function kernel_date($var1 = null) {
	if ($var1 == "") {return (date('Y-m-d H:i:s'));
	} else { return (date($var1));}
}
function kernel_panic() {
	kernel_log("(PANIC) KERNEL PANIC RAISED!!! WRITING LOG AND EXITING!");
	ob_start();
	debug_print_backtrace();
	$stack = ob_get_clean();
	kernel_log ("CALL STACK:\r\n".$stack);
	$LOG = kernel_log();
	$file = $GLOBALS['CONFIG']['app_real_location']."/panic.log";
	if (is_writable($file) or ! file_exists($file)) {
		file_put_contents("$file",$LOG);
	} else {
		echo "PANIC: CANNOT WRITE TO LOG! PLEASE CHECK FILE PERMISSIONS!"; }
	exit(9999);
}
function kernel_vartemp_clear() {
	unset ($GLOBALS['TEMP']);
	$GLOBALS['TEMP'] = array();
}

function kernel_log($var1 = null,$var2 = null) {
/* LOG LEVEL /*
	5: Normal
	4: Warning
	3: Critical
	2: Fatal (module may crash)
	1: Panic (Kernel will crash, log will be written to panic.log even if debug is disabled.)
*/
	$callinfo = debug_backtrace();
    
	$file = $callinfo[0]['file'];
	$prefix = null;
	$prefix2 = null;
	$file = str_replace ("\\","/",$file); // Convert Windows-style path to Unix-style so we can use it
	static $LOG = array();
	
	
	// Determine who is calling the function and set its name as a prefix
	// Third-party Module
	if (isset ($callinfo['function']) and $callinfo['function'] === "kernel_log") {$prefix = "kernel_log";} else {
		if (strpos ($file,$GLOBALS['CONFIG']['app_real_location']."/".$GLOBALS['CONFIG']['modules']) === 0){
			preg_match('/(?<='.$GLOBALS['CONFIG']['modules'].'\/).*?(?=\/)/',$file,$module); // Find the module name by its folder name
			$prefix = strtoupper($module[0]);
		} elseif ($file === $GLOBALS['CONFIG']['app_real_location']."/index.php") {
		// The main index.php file
			$prefix = "SYSTEM";
		} elseif ($file === $GLOBALS['CONFIG']['app_real_location']."/Kernel/boot.php") {
		// Kernel itself
			$prefix = "KERNEL";
		} elseif (strpos($file,$GLOBALS['CONFIG']['app_real_location'].'/Kernel') === 0) {
		// Kernel Module
			preg_match('/(?<=Kernel\/).*?(?=\/)/',$file,$module);
			$prefix = "KERNEL_".strtoupper($module[0]);
		} elseif (strpos ($file,$GLOBALS['CONFIG']['app_real_location']."/".$GLOBALS['CONFIG']['themes']) === 0) {
			$prefix = $GLOBALS['CONFIG']['theme']." THEME";
		} else {
		// At this point, only the WebRoot folder remains.
			$prefix = DOCPATH .".html";
		}
	}
	$prefix .= ":";
	
	if (!$var1 == "") {
		if ($var2 == "") {$var2 = 5; }
		if (!is_string($var1) or !is_numeric($var2)) {kernel_log ("Invalid argument type when calling 'kernel_log'. 1: $var1, 2: $var2.",4); return "Bad arguments type. 1st argument must be a string and 2d argument must be a number"; }
		if ($var2 > 5) {kernel_log("Log level is greater than 5. 1: $var1, 2: $var2",4); $var2 = 5;}
		if ($var2 < 1) {kernel_log("Log level is lower than 1. Ignoring the request",3); return;}
		switch ($var2) {
			case 1:
				$prefix2 = "(PANIC) ";
				break;
			case 2:
				$prefix2 = "(FATAL) ";
				break;
			case 3:
				$prefix2 = "(CRITICAL) ";
				break;
			case 4:
				$prefix2 = "(WARNING) ";
				break;
		}
		$text = "[".kernel_date()."] $prefix $prefix2$var1\r\n";
		array_push($LOG,$text);
		if ($GLOBALS['CONFIG']['debug'] == TRUE){ // What are we going to send to the browser?
		
			switch ($var2) {
				case 5:
					if ($GLOBALS['CONFIG']['debug_level'] >= 4) {echo $text;}
					break;
				case 4:
					if ($GLOBALS['CONFIG']['debug_level'] >= 3) {echo $text;}
					break;
				case 3:
					if ($GLOBALS['CONFIG']['debug_level'] >= 2) {echo $text;}
					break;
				case 2:
					if ($GLOBALS['CONFIG']['debug_level'] >= 1) {echo $text;}
					break;
			}
		}
	} else {		
		return $LOG;
	}
	if ($var2 == 1) {$GLOBALS['CONFIG']['debug_level'] = 4; echo $text; kernel_panic();}
	
}

kernel_log(framework_version." booting");
require_once 'protected/init.php';
require_once 'event_handler/init.php';
require_once 'override/init.php';
require_once 'sql/init.php';
kernel_vartemp_clear();


?>