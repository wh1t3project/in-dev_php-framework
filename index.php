<?php

TODO: FIX CUSTOM LOOP FOR HANDLING URL

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
//$INFO['web_location'] = "/newengine/test/df"; // Uncomment to override web-location USEFULL WHEN DUBUGGING THROUGHT THE CONSOLE!!

// Find docpath and docname
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
include ("docname.inc.php");
if ($TEMP['docpath'] == "/") {
	define ('DOCPATH',"/".$CONFIG['default_document']);
	
	$THEME['page_title'] = $DOCNAME[$CONFIG['default_document']];
	kernel_log("Sent DEFAULT document");
} elseif (file_exists(".".$TEMP['docpath'].'.html')) {
	define ('DOCPATH',$TEMP['docpath']);
	
	preg_match('/(?!.*\/).*/',DOCPATH,$TEMP['docname']); // Get the file name
	define ('DOCNAME',$TEMP['docname'][0]);
	$THEME['page_title'] = $DOCNAME[DOCNAME];
	kernel_log("Sent document '". DOCNAME ."'");

} else { 
	kernel_log ("File ". $TEMP['docpath'] ." not found. Sent 404",4);
	define('DOCPATH','/404'); 
	define('DOCNAME','404'); 
	$THEME['page_title'] = $DOCNAME[DOCNAME];
	
}

kernel_vartemp_clear();
// System is ready and all modules are initialized. Booting up and loading content.
kernel_event_trigger("STARTUP");
$TEMP['show_page'] = false;
while (true) {
	$i = kernel_override_url();
	static $index = 0;
		if ($i['TYPE'][$index] === 2) {
			if (DOCPATH === $i['URL'][$index]) { include_once $i['SCRIPT'][$index];}
		} elseif($i['TYPE'][$index] === 1) {
			if (! stripos(DOCPATH ."/",$i['URL'][$index]."/") !== 0) { include_once $i['SCRIPT'][$index];}
		} elseif ($i['URL'][$index] == null ) { $TEMP['show_page'] = true; break;}
	$index++;
}
if ($TEMP['show_page'] === true) { 
unset ($TEMP['show_page']);
kernel_log("Using theme '".$CONFIG['theme']."'");
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