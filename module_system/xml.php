<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                                      *
********************************************************************************************************/


//Determine the area to load
if(issetGet("admin") && getGet("admin") == 1)
	define("_admin_", true);
else
	define("_admin_", false);

define("_autotesting_", false);


/**
 * Class handling all requests to be served with xml
 *
 * @package module_system
 */
class class_xml {

    private static $bitSuppressXmlHeader = false;
    private static $strReturnContentType = "Content-Type: text/xml; charset=utf-8";

    public function __construct() {
		class_carrier::getInstance();
    }

    public function processRequest() {

        $strModule = getGet("module");
        if($strModule == "")
            $strModule = getPost("module");

        $strAction = getGet("action");
        if($strAction == "")
            $strAction = getPost("action");

        $strLanguageParam = getGet("language");
        if($strLanguageParam == "")
            $strLanguageParam = getPost("language");


        $objDispatcher = new class_request_dispatcher();
        $strContent = $objDispatcher->processRequest(_admin_, $strModule, $strAction, $strLanguageParam);

        if($strContent == "") {
            header(class_http_statuscodes::SC_BADREQUEST);
            $strContent = "<error>An error occurred, malformed request</error>";
        }

        if(!self::$bitSuppressXmlHeader)
            $strContent = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n".$strContent;
        return $strContent;
    }

    /**
     * If set to true, the output will be sent without the mandatory xml-head-element
     * @param bool $bitSuppressXmlHeader
     */
    public static function setBitSuppressXmlHeader($bitSuppressXmlHeader) {
        self::$bitSuppressXmlHeader = $bitSuppressXmlHeader;
    }

    /**
     * Use this method to set a new response type, e.g. json
     * @static
     * @param $strReturnContentType
     */
    public static function setStrReturnContentType($strReturnContentType) {
        self::$strReturnContentType = $strReturnContentType;
    }

    public static function getStrReturnContentType() {
        return self::$strReturnContentType;
    }

}

//pass control
$objXML = new class_xml();
$strContent = $objXML->processRequest();
header(class_xml::getStrReturnContentType());
echo $strContent;

