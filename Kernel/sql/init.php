<?php
// SQL driver loader
/* Load the SQL driver specified in the configuration

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

// Check if driver exist and load it

if (is_readable("sql/".$CONFIG['sql_drv'].".php")) { 
	kernel_log("Loading SQL driver '".$CONFIG['sql_drv']."'");
	include_once("sql/".$CONFIG['sql_drv'].".php");
} elseif ($CONFIG['sql_drv'] == null) { kernel_log ("No SQL driver specified. SQL functions not available",4); 
} else {
	kernel_log ("SQL driver '".$CONFIG['sql_drv']."' does not exist or is inaccessible. Please check config",1);
}

kernel_log("Driver loaded and module ready");


?>