<?php
// Framework configuration file
/* All the main and debugging settings are here.

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
$CONFIG['app_location'] 	= '/framework';
$CONFIG['app_real_location']= 'D:\SSD\Documents\GitHub\in-dev_php-framework';
$CONFIG['default_document'] = 'index';
$CONFIG['theme']			= "oliwez";
$CONFIG['lang']				= "fr";
$CONFIG['timezone']			= 'America/Montreal';

// Debugging
$CONFIG['debug']			= true;
$CONFIG['debug_panic']		= true; // Run if panic raised? Note that this setting is applied even if debug is disabled
$CONFIG['debug_file']		= "/debug.log";
$CONFIG['debug_level']		= 4; // Does not have any effect if debug is disabled
?>