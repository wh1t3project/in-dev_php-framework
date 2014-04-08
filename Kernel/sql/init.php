<?php
// SQL driver loader
/* Load the SQL driver specified in the configuration

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