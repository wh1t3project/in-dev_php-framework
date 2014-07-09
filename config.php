<?php
// Framework configuration file
/* All the main and debugging settings are here.

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
$CONFIG['sql_drv']			= '';
$CONFIG['app_location'] 	= '/';
$CONFIG['app_real_location']= '';
$CONFIG['default_document'] = 'index';
$CONFIG['theme']			= "default";
$CONFIG['lang']				= "fr";
$CONFIG['timezone']			= 'America/Montreal';
$CONFIG['allowed_file_ext'] = array('jpg','png','gif','txt'); //File's extensions allowed to be downloaded directly

// Debugging
$CONFIG['debug']			= false;
$CONFIG['debug_panic']		= true; // Run even if panic was raised before? Note that this setting is applied even if debug is disabled
$CONFIG['debug_file']		= "/debug.log";
$CONFIG['debug_level']		= 0; // Does not have any effect if debug is disabled
?>