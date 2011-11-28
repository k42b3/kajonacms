<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                                 *
********************************************************************************************************/

/**
 * @package module_system
 *
 */


//helper for bad bad bad cases
function rawIncludeError($strFileMissed) {
    $strErrorMessage = "<html><head></head><body><div style=\"border: 1px solid red; padding: 5px; margin: 20px; font-family: arial,verdana, serif; font-size: 12px; \">\n";
    $strErrorMessage .= "<div style=\"background-color: #cccccc; color: #000000; font-weight: bold; \">An error occurred:</div>\n";
    $strErrorMessage .= "Error including necessary files. Can't proceed.<br />";
    $strErrorMessage .= "Searched for ".$strFileMissed." but failed. Going home now...<br />";
    $strErrorMessage .= "</div></body></html>";
	die($strErrorMessage);
}

//---The Path on the filesystem------------------------------------------------------------------------------
	//Determine the current path on the filesystem. Use the dir-name of the current file
	define("_realpath_", substr(dirname(__FILE__), 0, -5));
	define("_corepath_", dirname(__FILE__));

//--- Loader pre-configuration
    if(!defined("_xmlLoader_"))
        define("_xmlLoader_", false);

//---Include Section 1-----------------------------------------------------------------------------------

    //Setting up the default timezone, determined by the server / environment
	@date_default_timezone_set(date_default_timezone_get());

	//Functions to have fun & check for mb-string
	if(!@include_once(_corepath_."/module_system/system/functions.php"))
		rawIncludeError(_corepath_."/module_system/system/functions.php");

	//Exception-Handler
	if(!@include_once(_corepath_."/module_system/system/class_exception.php"))
		rawIncludeError("global exception handler");
	//register global exception handler for exceptions thrown but not catched (bad style ;) )
	@set_exception_handler(array("class_exception", "globalExceptionHandler"));

	//Include the logging-engine
    if(!@include_once(_corepath_."/module_system/system/class_logger.php"))
		rawIncludeError("logging engine");


//---The Path on web-------------------------------------------------------------------------------------

    include_once (_corepath_."/module_system/system/class_config.php");
    $strHeaderName = class_config::readPlainConfigsFromFilesystem("https_header");
    $strHeaderValue = strtolower(class_config::readPlainConfigsFromFilesystem("https_header_value"));

	if(strpos($_SERVER['SCRIPT_FILENAME'], "/debug/")) {
		//Determine the current path on the web
		$strWeb = dirname ((isset($_SERVER[$strHeaderName]) && (strtolower($_SERVER[$strHeaderName]) == $strHeaderValue) ? "https://" : "http://") .$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
		$strWeb = substr_replace($strWeb, "", strrpos($strWeb, "/"));
		define("_webpath_", saveUrlEncode($strWeb));
	}
	else {
		//Determine the current path on the web
		$strWeb = dirname ((isset($_SERVER[$strHeaderName]) && (strtolower($_SERVER[$strHeaderName]) == $strHeaderValue) ? "https://" : "http://") .$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);
		$strWeb = saveUrlEncode($strWeb);
		define("_webpath_", $strWeb);
	}

//---Include Section 2-----------------------------------------------------------------------------------
	//Module-Constants
	foreach(scandir(_corepath_."/") as $strDirEntry ) {
        if(is_dir(_corepath_."/".$strDirEntry."/system/config/")) {
            foreach(scandir(_corepath_."/".$strDirEntry."/system/config/") as $strModuleEntry ) {
                if(preg_match("/modul\_([a-z])+\_id\.php/", $strModuleEntry)) //FIXME: rename id-files to module_
                    @include_once(_corepath_."/".$strDirEntry."/system/config/".$strModuleEntry);
            }
        }

	}


    //---Auto-Loader for classes--------------------------------------------------------------------------
    include_once (_corepath_."/module_system/system/class_classloader.php");
    spl_autoload_register(array (class_classloader::getInstance(), "loadClass"));

    //The Carrier-Class
    if(!@include_once(_corepath_."/module_system/system/class_carrier.php"))
        rawIncludeError("carrier-class");



?>