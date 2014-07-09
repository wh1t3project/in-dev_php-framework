<?php
// mysqli driver for SQL kernel module
/* Handle request for SQL using mysqli PHP extension

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
$TEMP['callinfo'] = debug_backtrace();
$TEMP['file'] = $TEMP['callinfo'][0]['file'];
if (! $TEMP['file'] === $GLOBALS['CONFIG']['app_real_location']."/Kernel/sql/init.php") { kernel_log("Illegal attempt to load MySQLi driver",1); exit(1);}

kernel_protected_var("sql_ses_id",null);

function SQL_connect($var1 = null, $var2 = null, $var3 = null, $var4 = null, $var5 = null){
	/* VAR definition
	var1: sql_host
	var2: sql_user
	var3: sql_pass
	var4: sql_db
	var5: sql_port
	*/
	$return = null;
	if ($var1 != null) {
		kernel_log("Attempting to connect to MySQL host '$var1' with user '$var2'");
		$sql_ses_id = mysqli_connect($var1,$var2,$var3);
		if ($sql_ses_id == null) { kernel_log("Error while connecting to MySQL:". mysqli_connect_error(),2); return;
		} else { kernel_log ("Connected to MySQL. Connecting to database '$var4'...");}
		mysqli_select_db($sql_ses_id,$var4);
		mysqli_set_charset ($sql_ses_id,"utf8");
		if (mysqli_error($sql_ses_id) != "") {mysqli_close ($sql_ses_id); kernel_log("Error while connecting to database: ". mysqli_error($sql_ses_id),2);
		} else { kernel_log ("Connection successful. Ready to process query"); $return = $sql_ses_id; kernel_protected_var("sql_ses_id",$sql_ses_id);}
	} else {
		kernel_log("Attempting to connect to MySQL host '".$GLOBALS['CONFIG']['sql_host']."' with user '".$GLOBALS['CONFIG']['sql_user']."'");
		$sql_ses_id = mysqli_connect($GLOBALS['CONFIG']['sql_host'],$GLOBALS['CONFIG']['sql_user'],$GLOBALS['CONFIG']['sql_pass']);
		if ($sql_ses_id == null) {kernel_log("Error while connecting to MySQL: ". mysqli_connect_error(),2); return;
		} else { kernel_log ("Connected to MySQL. Connecting to database '".$GLOBALS['CONFIG']['sql_db']."'...");}
		mysqli_select_db($sql_ses_id,$GLOBALS['CONFIG']['sql_db']);
		mysqli_set_charset ($sql_ses_id,"utf8");
		$sql_error = mysqli_error($sql_ses_id);
		if ($sql_error != "") {mysqli_close ($sql_ses_id); kernel_log("Error while connecting to database: $sql_error",2);
		} else { kernel_log ("Connection successful. Ready to process query"); $return = $sql_ses_id; kernel_protected_var("sql_ses_id",$sql_ses_id);}
		}
	return($return);
}
function SQL_close($var1) {
	if ($var1 == null) { kernel_log ("Missing argument when calling 'SQL_close'",3); return;}
	if (mysqli_ping ($var1) === true) { mysqli_close($var1); } else { kernel_log("Attempt to close an SQL session with an invalid session ID or host is down"); return;}
	
	return true;
}
function SQL_resid($var1 = null){
	if ($var1 == null) { return(kernel_protected_var("sql_ses_id"));
	} else {
		if (mysqli_ping ($var1) === true) { kernel_protected_var("sql_ses_id",$var1); kernel_log ("New SQL resource ID set"); return true; } else { kernel_log ("Bad SQL resource ID given"); return false;}
	}
}
function SQL_query($var1, $var2 = null){
	if ($var1 == null) {kernel_log("Missing argument(s) for 'SQL_query'",3);return;}
	if (gettype($var1) != "string") { kernel_log("Invalid argument type for 'SQL_query'",3);return;}
	if ($var2 == null) {$sql_ses_id = kernel_protected_var("sql_ses_id");} else {$sql_ses_id = $var2;}
	if ($sql_ses_id == null) { kernel_log ("No SQL resource ID specified",2); return;}
	if (! mysqli_ping ($sql_ses_id) === true) { kernel_log("Attempt to use 'SQL_query' with an invalid SQL resource ID or host is down",3); return;}
	$return = null;
	$result = mysqli_query($sql_ses_id,$var1);
	if (mysqli_error($sql_ses_id) != "") {kernel_log("An error as occurred while executing the query: $var1. The error was: ". mysqli_error($sql_ses_id),3); return;
	} else {if (gettype($result) === "object") {$return = mysqli_fetch_array($result);} else { $return = $result;} }
	return($return);
}
function SQL_select($var1, $var2, $var3 = null){
	if ($var1 == null or $var2 == null) {kernel_log("Missing argument for 'SQL_select'",3);return;}
	if (gettype($var1) != "string" or gettype($var2) != "string") { kernel_log("Invalid argument type for 'SQL_select'",3);return;}
	$return = null;
	
	$query = "SELECT ".$var1." FROM ".$var2;
	if ($var3 != null) {$query .= " ".$var3;}
	$result = SQL_query ($query);
	if ($result === null) { kernel_log ("Error while executing SELECT query. Please check log for details",3); return;} else { $return = $result;}
	
	return ($return);
}
function SQL_insert($var1, $var2, $var3){
	if ($var1 == null or $var2 == null or $var3 == null) { kernel_log("Missing argument for 'SQL_insert'",3);return;}
	if (gettype($var2) != "array" or gettype($var3) != "array") { kernel_log("Invalid argument type provided for 'SQL_insert'");return;}
	$return = null;
	
	$query = "INSERT INTO ".$var1." (";
	foreach ($var2 as $item) { $query .= "`$item`,"; }
	$query = substr($query,0,-1).") VALUES (";
	foreach ($var3 as $item) { $query .= "\"$item\","; }
	$query = substr($query,0,-1).")";
	$return = sql_query($query);
	return $return;
}
function SQL_create_database($var1){
	if ($var1 == null) {kernel_log("Missing argument for 'SQL_create_database'",3);return;}
	if (gettype($var1) != "string") {kernel_log("Invalid argument type for 'SQL_create_databse'",3);return;}
	$return = null;
	
	$query = "CREATE DATABASE $var1";
	$return = sql_query($query);
	return $return;
}
//TODO HERE: add sql_create_table
function SQL_update ($var1,$var2,$var3,$var4,$var5,$var6 = null){
	/* Var definition
	var1: Table_name
	var2: Column to update
	var3: Value to set
	var4: Column to match
	var5: Value to match
	var6: Optional extra argument
	*/
	if ($var1 == null or $var2 == null or $var3 == null or $var4 == null or $var5 == null) {kernel_log("Missing argument for 'SQL_update'",3);return;}
	if (gettype($var1) != "string" or gettype($var2) != "string" or gettype($var3) != "string" or gettype($var4) != "string" or gettype($var5) != "string") { kernel_log("Invalid argument type for 'SQL_update'",3);return;}
	$return = null;
	
	$query = "UPDATE $var1 SET $var2=$var3 WHERE $var4=$var5";
	if ($var6 != null) {$query .= " $var6";}
	$return = sql_query($query);
	return $return;
}
function SQL_delete($var1,$var2,$var3,$var4 = null){
	if ($var1 == null or $var2 == null or $var3 == null) {kernel_log("Missing argument for 'SQL_detet'",3);return;}
	if (gettype($var1) != "string" or gettype($var2) != "string" or gettype($var3) != "string") { kernel_log("Invalid argument type for 'SQL_delete'",3);return;}
	if ($var4 != null and gettype($var4) != "string") {kernel_log("Invalid argument type for 'SQL_delete'",3);return;}
	$return = null;
	
	$query = "DELETE FROM $var1 WHERE `$var2`='$var3'";
	if ($var4 != null) {$query .= " ".$var4;}
	
	$return = sql_query($query);
	return $return;
}
function SQL_drop_table ($var1){
	if ($var1 == null) { kernel_log("Missing argument for 'SQL_drop_table'",3); return;}
	if (gettype($var1) != "string") { kernel_log("Invalid argument type for 'SQL_drop_table'",3);return;}
	$return = null;
	
	$query = "DROP TABLE $var1";
	$return = sql_query($query);
	return $return;
}
function SQL_drop_database($var1){
	if ($var1 == null) { kernel_log("Missing argument for 'SQL_drop_database'",3); return;}
	if (gettype($var1) != "string") { kernel_log("Invalid argument type for 'SQL_drop_database'",3);return;}
	$return = null;
	
	$query = "DROP DATABASE $var1";
	$return = sql_query($query);
	return $return;
}

?>