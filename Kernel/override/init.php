<?php
// Kernel "override" module
/* Small library for overriding how the framework work.

 Copyright (C) 2014  Gaël Stébenne (alias Wh1t3c0d3r)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
if (! DEFINED("INSCRIPT")) {echo "Direct access denied";exit(1);}



function kernel_override_url ($var1 = null,$var2 = null,$var3 = 1){
	// Var1: URL
	// Var2: Full path to script from the root of the framework
	// Var3: Normal(2) OR explicit(1) (Optional, Explicit by default)
	$callinfo = debug_backtrace();
	$file = $callinfo[0]['file'];
	$file = str_replace ("\\","/",$file); 
	static $DATA = array();
	$script = $GLOBALS['CONFIG']['app_real_location'] .$var2;
	if (! isset ($DATA['URL'])) { $DATA['URL'] = array(); }
	if (! isset ($DATA['SCRIPT'])) { $DATA['SCRIPT'] = array(); }
	if (! isset ($DATA['SCRIPTNAME'])) { $DATA['SCRIPTNAME'] = array(); }
	if (! isset ($DATA['TYPE'])) { $DATA['TYPE'] = array(); }
	
	if ($file === $GLOBALS['CONFIG']['app_real_location']."/index.php") { return $DATA;}
	if ($var1 == null or $var2 == null) {kernel_log ("Missing parameters when calling 'kernel_override_url'",3); return;}
	

	if (! is_readable($script)) {kernel_log("File '$var2' does not exist or is inaccessible",3); return;} // Check if script can be read
	if ($var3 != 1 and $var3 != 2) {kernel_log("Invalid type '$var3'",3); return;}
	if (stripos($var1,'/') !== 0) { $var1 = "/".$var1; }
	if ($var1[strlen($var1) - 1] == "/") { $var1 = substr($var1,0,-1);} // Remove the last slash if there is one.
	foreach ($DATA['URL'] as $i) { if ($var1 == $i) {kernel_log("Attempt to register an already registered URL '$i'. Request ignored.",3); return;} } // Check if URL is already registered
	// Push the informations to the arrays
	array_push($DATA['URL'],$var1);
	array_push($DATA['SCRIPT'],$script);
	array_push($DATA['TYPE'],$var3);
	array_push($DATA['SCRIPTNAME'],$var2);
	$log = "Script '$var2' registered with URL '$var1' using ";
	switch ($var3) {
		case 1:
			$log .= "normal mode";
			break;
		case 2:
			$log .= "explicit mode";
			break;
	}
	kernel_log($log);
	return true;
}

	
kernel_log("Module ready");
?>