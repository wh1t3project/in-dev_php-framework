<?php
// Kernel "event_handler " module.
/* Handle all the system events in the framework.
 
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
// Event Handler v0.01
/* Depencies:
- Log
*/
$EVENTS['STARTUP'] 		= array(); // On boot after init
$EVENTS['SHOWHEADER'] 	= array(); // Header loaded
$EVENTS['SHOWCONTENT'] 	= array(); // Content loaded
$EVENTS['SHOWFOOTER']	= array(); // Footer loaded
$EVENTS['SHUTDOWN'] 	= array(); // Shutting down, useful when logging

kernel_protected_var('kernel_event_ignored_functions',array(
'return',
'exit',
'shell',
'kernel_log'));

function kernel_event_trigger($var1) {
	if ($var1 == "") {kernel_log("Call to 'kernel_event_trigger' without required argument!",3); return;}
	if (array_key_exists($var1,$GLOBALS['EVENTS'])) {
		kernel_log("Event '$var1' triggered",5);
		foreach ($GLOBALS['EVENTS'][$var1] as $cmd) {
			$ignore = false;
			foreach (kernel_protected_var("kernel_event_ignored_functions") as $find) {
				if (preg_match("/".$find."[ \(|\(]/","$cmd") == 1 OR $cmd == $find) {
					kernel_log("Call to unauthorized function '$cmd'. Skipping it.",4);
					$ignore = true;
				}
			}
			if ($ignore === false){ kernel_log("Executing '$cmd'...",5); eval($cmd.";");}
		}
	} else { kernel_log("Unknown event '$var1' triggered", 4); }
}

kernel_log("Module ready");
?>