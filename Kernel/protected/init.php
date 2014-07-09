<?php
// Kernel "Protected" module
/* Small library for protecting data from other scripts.

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
if (! DEFINED('INSCRIPT')) {echo 'Direct access denied'; exit(1);}

function kernel_protected_var($var1,$var2 = "") {
	if ($var1 == "") {kernel_log("No argument or empty value when calling 'kernel_protected_var'",3); return;}
	$return = null;
	static $DATA = array();
	$callinfo = debug_backtrace();
	$file = $callinfo[0]['file'];
	
	if ($var2 === "") {
		if (isset ($DATA["$file"]["$var1"])) {$return = $DATA["$file"]["$var1"];
		} else { kernel_log("Undefined variable: $var1 in $file on line ".$callinfo[0]['line'],4);}
	} else {
		if ($var2 === null) {unset ($DATA["$file"]["$var1"]);
		} else { $DATA["$file"]["$var1"] = $var2;}
	}
	
	return $return;
}
kernel_log("Module ready");
?>