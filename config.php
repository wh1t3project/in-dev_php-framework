<?php
if (! DEFINED('INSCRIPT')) {echo 'Direct access denied'; exit(1);}
// System's variable. Change them at your own risk!
$CONFIG['webroot']			= "webroot";
$CONFIG['themes']			= "themes";
$CONFIG['themes_css']		= "css";
$CONFIG['themes_js']		= "js";
$CONFIG['themes_img']		= "img";
$CONFIG['themes_mod']		= "modules";
$CONFIG['modules']			= "modules";

// User definable variables
$CONFIG['sql_user']			= '';
$CONFIG['sql_pass']			= '';
$CONFIG['sql_host']			= '';
$CONFIG['sql_db']			= '';
$CONFIG['app_location'] 	= '/newengine';
$CONFIG['app_real_location']= 'D:\SSD\Programs\wamp\www\newengine';
$CONFIG['default_document'] = 'index';
$CONFIG['theme']			= "oli";
$CONFIG['lang']				= "fr";
$CONFIG['timezone']			= 'America/Montreal';

// Debugging
$CONFIG['debug']			= true;
$CONFIG['debug_panic']		= true; // Run if panic raised? Note that this setting is applied even if debug is disabled
$CONFIG['debug_file']		= "/debug.log";
$CONFIG['debug_level']		= 4; // Does not have any effect if debug is disabled
?>