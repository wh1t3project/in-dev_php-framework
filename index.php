<?php
// Index file. 
/* Load kernel and modules, show content and take care of URL override

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
DEFINE ('INSCRIPT',"1");
require 'config.php'; // Config file
if ($CONFIG['debug'] === true) {error_reporting(E_ALL);}
session_start();
$CONFIG['app_real_location'] = str_replace("\\","/",$CONFIG['app_real_location']); // Convert Windows path to unix for compatibility
// Load and boot the kernel
chdir ('Kernel');
require_once 'boot.php';
chdir ($CONFIG['app_real_location']);


// Define system's dynamic variables
$THEME['location'] 	= $CONFIG['themes']."/".$CONFIG['theme'];
$THEME['css']		= $THEME["location"]."/".$CONFIG['themes_css']."/";
$THEME['js']		= $THEME["location"]."/".$CONFIG['themes_js']."/";
$THEME['img']		= $THEME["location"]."/".$CONFIG['themes_img']."/";
$THEME['module']	= $THEME["location"]."/".$CONFIG['themes_mod']."/";

$INFO['modules'] = array();

// Load modules. Other than executing their code now, they should register the functions in the event system.
kernel_log ("Loading modules from the module directory");
$dirhandle = opendir($CONFIG['app_real_location']."/".$CONFIG['modules']) or die (kernel_log("Could not open module directory. Please check configuration",1));
$TEMP['system']['moduletoload'] = array();
while (false !== ($item = readdir($dirhandle))) {if (stripos($item,'.') !== 0) {array_push($TEMP['system']['moduletoload'],$item);}}
closedir($dirhandle);
unset ($dirhandle);
foreach ($TEMP['system']['moduletoload'] as $item) {
	if (preg_match("/\/(system|kernel|theme|webroot|_|-)/i","/$item")){ break;} // If a module start with one of the name, exclude it. That prevent confusion in the log.
	if (file_exists($CONFIG['app_real_location']."/".$CONFIG['modules']."/$item/init.php") and is_readable($CONFIG['app_real_location']."/".$CONFIG['modules']."/$item/init.php")) { 
		kernel_log("Loading module '$item'..."); 
		chdir ($CONFIG['app_real_location']."/".$CONFIG['modules']."/$item");
		$result = include_once("init.php");
		if ($result === 1) {array_push($INFO['modules'],$item);} else {kernel_log("Failed to load module '$item'");}
		unset ($result);
}}
kernel_log("Done loading modules");
chdir ($CONFIG['app_real_location']."/".$CONFIG['webroot']);
kernel_vartemp_clear();

$INFO['web_location'] = $_SERVER['REQUEST_URI']; // Set current web location
//$INFO['web_location'] = "/framework/"; // Uncomment to override web-location USEFULL WHEN DUBUGGING THROUGHT THE CONSOLE!!

// Filter WEB URL and find docpath
$INFO['web_location'] = substr($INFO['web_location'], 0, strpos($INFO['web_location'],"?"));
$TEMP['regex_app_location'] = str_replace("/","\\/",$CONFIG['app_location']);
preg_match('/(?<='.$TEMP['regex_app_location'].').*/',$INFO['web_location'],$TEMP['docpath']); // Get the URL of the web location (with app_location as root)
$TEMP['docpath'] = $TEMP['docpath'][0];
// If at root, make sure that there is a slash. If not, remove the last one.
if ($TEMP['docpath'] !== "/") {
	if ($TEMP['docpath'] == "") {
		$TEMP['docpath'] = "/";
	} elseif ($TEMP['docpath'][strlen($TEMP['docpath']) - 1] === "/"){
		$TEMP['docpath'] = substr($TEMP['docpath'],0,-1);
	}
}
kernel_log("WEB-URL: ". $TEMP['docpath']);
$TEMP['show_page'] = false;
$i = kernel_override_url();
while (true) {
	static $i2 = 0;
	if (! isset ($i['TYPE'][$i2])) {$TEMP['show_page'] = true; unset ($i); break;}
		if ($i['TYPE'][$i2] === 2) {
			if ($TEMP['docpath'] === $i['URL'][$i2]) {kernel_log("Executing script ".$i['SCRIPTNAME'][$i2]." with URL ".$i['URL'][$i2]." using explicit mode"); include_once $i['SCRIPT'][$i2]; unset ($i); break;}
		} elseif($i['TYPE'][$i2] === 1) {
			if (substr($TEMP['docpath'] . "/", 0, strlen($i['URL'][$i2]."/")) === $i['URL'][$i2]."/") {kernel_log("Executing script ".$i['SCRIPTNAME'][$i2]." with URL ".$i['URL'][$i2]." using normal mode"); $i['URL'][$i2]; include_once $i['SCRIPT'][$i2];unset ($i); break;}
		}
	$i2++;
}
if ($TEMP['show_page'] === true) { 
	unset ($TEMP['show_page']);
	include ("docname.inc.php");

	if ($TEMP['docpath'] == "/") {
		define ('DOCPATH',"/".$CONFIG['default_document']);
		
		$THEME['page_title'] = $DOCNAME[$CONFIG['default_document']];
		kernel_log("Sent DEFAULT document");
	} elseif (file_exists(".".$TEMP['docpath'].'.html')) {
		define ('DOCPATH',$TEMP['docpath']);

		$THEME['page_title'] = $DOCNAME[substr(DOCPATH,1)];
		kernel_log("Sent document '". DOCPATH ."'");

	} else { 
		kernel_log ("File ". $TEMP['docpath'] ." not found. Sent 404",4);
		define('DOCPATH','/404'); 
		$THEME['page_title'] = $DOCNAME[substr(DOCPATH,1)];
		
	}

	kernel_vartemp_clear();
	// System is ready and all modules are initialized. Booting up and loading content.
	kernel_event_trigger("STARTUP");
	kernel_log("Using theme '".$CONFIG['theme']."'");
	include_once ($THEME['location']."/functions.php");
	include_once ($THEME['location']."/header.php");
	kernel_event_trigger("SHOWHEADER");
	include_once(".". DOCPATH .".html");
	kernel_event_trigger("SHOWCONTENT");
	include_once ($THEME['location']."/footer.php");
	kernel_event_trigger("SHOWFOOTER");
}
kernel_log("Shutting down...");
kernel_event_trigger("SHUTDOWN");
kernel_log("HALT");
// Save kernel log if debug is enabled
if ($CONFIG['debug'] === TRUE) {
		$LOG = kernel_log();
		$file = $CONFIG['app_real_location'].$CONFIG['debug_file'];
		if (is_writable($file) or ! file_exists($file)) {
			file_put_contents("$file",$LOG,FILE_APPEND);
		} else {
			echo "WARNING: Debug is enabled but cannot write to log. Please check file permissions.\r\n"; } 
}


?>
