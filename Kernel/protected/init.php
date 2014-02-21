<?php
// Module to protect data from other scripts

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