<?php
// Kernel "Protected" module
/* Small library for protecting data from other scripts.

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
if (! DEFINED('INSCRIPT')) {echo 'Direct access denied'; exit(1);}

function kernel_protected_var($var1,$var2 = null,$var3 = null) {
	if ($var1 == "") {kernel_log("No argument or empty value when calling 'kernel_protected_var'",3); return;}
	$return = null;
	static $DATA = array();
	$callinfo = debug_backtrace();
	$file = $callinfo[0]['file'];
	
	if ($var2 === null) {
		if (isset ($DATA["$file"]["$var1"])) {$return = $DATA["$file"]["$var1"];
		} else { kernel_log("Undefined variable: $var1 in $file on line ".$callinfo[0]['line'],4);}
	} else {
		if ($var3 === true) {unset ($DATA["$file"]["$var1"]);
		} else { $DATA["$file"]["$var1"] = $var2;}
	}
	
	return $return;
}
kernel_log("Module ready");
?>