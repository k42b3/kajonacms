<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_modul_search_portal_xml.php 3597 2011-02-11 14:09:51Z sidler $						    *
********************************************************************************************************/

/**
 * Portal-class of the search.
 * Serves xml-requests, e.g. generates search results
 *
 * @package module_search
 * @author sidler@mulchprod.de
 */
class class_module_search_portal_xml extends class_portal implements interface_xml_portal {

    private static $INT_MAX_NR_OF_RESULTS = 30;

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->setArrModuleEntry("moduleId", _search_module_id_);
        $this->setArrModuleEntry("modul", "search");
        parent::__construct();
	}


	/**
	 * Searches for a passed string
	 *
	 * @return string
     * @permissions view
	 */
	protected function actionDoSearch() {
	    $strReturn = "";

	    $strSearchterm = "";
	    if($this->getParam("searchterm") != "") {
			$strSearchterm = htmlToString(urldecode($this->getParam("searchterm")), true);
		}

		$arrResult = array();
	    $objSearchCommons = new class_module_search_commons();
	    if($strSearchterm != "") {
	        $arrResult = $objSearchCommons->doPortalSearch($strSearchterm);
	    }

	    $strReturn .= $this->createSearchXML($strSearchterm, $arrResult);

        return $strReturn;
	}


	private function createSearchXML($strSearchterm, $arrResults) {
        $strReturn = "";

        $strReturn .=
        "<search>\n"
	    ."    <searchterm>".xmlSafeString($strSearchterm)."</searchterm>\n"
	    ."    <nrofresults>".count($arrResults)."</nrofresults>\n";



        //And now all results
        $intI = 0;
        $strReturn .="    <resultset>\n";
        foreach($arrResults as $arrOneResult) {

            if(++$intI > self::$INT_MAX_NR_OF_RESULTS)
                break;

            //create a correct link
            if(!isset($arrOneResult["pagelink"]))
				$arrOneResult["pagelink"] = getLinkPortal($arrOneResult["pagename"], "", "_self", $arrOneResult["pagename"], "", "&highlight=".$strSearchterm."#".$strSearchterm);

            $strReturn .=
             "        <item>\n"
		    ."            <pagename>".$arrOneResult["pagename"]."</pagename>\n"
		    ."            <pagelink>".$arrOneResult["pagelink"]."</pagelink>\n"
		    ."            <description>".xmlSafeString($arrOneResult["description"])."</description>\n"
		    ."        </item>\n";
        }

        $strReturn .="    </resultset>\n";
	    $strReturn .= "</search>";
        return $strReturn;
	}
}
