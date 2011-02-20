<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                            *
********************************************************************************************************/


/**
 * Portal-part of the navigation. Creates the different navigation-views as sitemap or tree
 *
 * @package modul_navigation
 * @author sidler@mulchprod.de
 */
class class_modul_navigation_portal extends class_portal implements interface_portal {
	private $strCurrentSite = "";
	private $arrTree = array();
	private $intLevelMax = 0;

    /**
     * Internal structure for all nodes within a single navigation, permissions are evaluated.
     * The structure is a multi-dim array:
     * [navigation-id] = array("node" => $objNode, "subnodes" => array(
     *                                 array("node", "subnodes" => array(...) ),
     *                                 array("node", "subnodes" => array(...) ),
     *                                  ... )
     *                   );
     * 
     * @var array
     */
    private $arrTempNodes = array();

	/**
	 * Constructor
	 *
	 * @param mixed $arrElementData
	 */
	public function __construct($arrElementData) {
        $arrModul = array();
        $arrModul["modul"]          = "navigation";
		$arrModul["name"] 			= "modul_navigation";
		$arrModul["moduleId"] 		= _navigation_modul_id_;
		$arrModul["table"]		    = _dbprefix_."navigation";

		parent::__construct($arrModul, $arrElementData);

		//Determin the current site to load
		$this->strCurrentSite = $this->getPagename();

        
	}

    /**
     * Action Block to decide further actions
     *
     * @return string
     */
	public function action($strAction = "") {
		$strReturn = "";

        //init with the current navigation, required in all cases
        $objNavigation = new class_modul_navigation_tree($this->arrElementData["navigation_id"]);
        $this->arrTempNodes[$this->arrElementData["navigation_id"]] = $objNavigation->getCompleteNaviStructure();

		//Which kind of navigation do we want to load?
		if($this->arrElementData["navigation_mode"] == "tree")
			$strReturn = $this->loadNavigationTree();
		if($this->arrElementData["navigation_mode"] == "sitemap")
			$strReturn = $this->loadNavigationSitemap();

        //Add pe code
        $arrPeConfig = array(
                                  "pe_module" => "navigation",
                                  "pe_action_edit" => "list",
                                  "pe_action_edit_params" => "&systemid=".$this->arrElementData["navigation_id"],
                                  "pe_action_new" => "",
                                  "pe_action_new_params" => "",
                                  "pe_action_delete" => "",
                                  "pe_action_delete_params" => ""
                            );
        $strReturn = class_element_portal::addPortalEditorCode($strReturn, $this->arrElementData["navigation_id"], $arrPeConfig);

		return $strReturn;
	}
  

// --- Tree-Functions -----------------------------------------------------------------------------------

	/**
	 * Creates a common tree-view of the navigation
	 *
	 * @return string
	 */
	private function loadNavigationTree() {
		$strReturn = "";
		$strStack = "";

        $objPagePointData = $this->getActivePagePointData();
        $strStack = $this->getActiveIdStack($objPagePointData);

		//path created, build the tree using recursion
        if($this->objRights->rightView($this->arrElementData["navigation_id"]) && $this->getStatus($this->arrElementData["navigation_id"]) == 1)
            $this->createTree($strStack, 0, $this->arrElementData["navigation_id"]);

		//Create the tree
		$intCounter = -1;
		$arrTree = array();
		foreach($this->arrTree as $intLevel => $arrContents) {
			$arrTree[++$intCounter] = (isset($arrContents) ? $arrContents : "");
		}
		//Create tree from bottom
		$arrTemp = array();
		while($intCounter > 1) {
			$strLevel = $arrTree[$intCounter];

			//include into a wrapper?
			$strLevelTemplateID = $this->objTemplate->readTemplate("/modul_navigation/".$this->arrElementData["navigation_template"], "level_".$intCounter."_wrapper");
			$strWrappedLevel = $this->fillTemplate(array("level".$intCounter => $strLevel), $strLevelTemplateID);
			if(uniStrlen($strWrappedLevel) > 0)
			    $strLevel = $strWrappedLevel;

			$arrTemp["level".$intCounter] = $strLevel;

			$this->objTemplate->setTemplate($arrTree[$intCounter-1]);
			$arrTree[$intCounter-1] = $this->objTemplate->fillCurrentTemplate($arrTemp);
			$intCounter--;
		}

		//and add level 1 wrapper
        if($intCounter != -1) {
            $strLevelTemplateID = $this->objTemplate->readTemplate("/modul_navigation/".$this->arrElementData["navigation_template"], "level_".$intCounter."_wrapper");
            $strWrappedLevel = $this->fillTemplate(array("level".$intCounter => $arrTree[$intCounter]), $strLevelTemplateID);
            if(uniStrlen($strWrappedLevel) > 0)
                $arrTree[$intCounter] = $strWrappedLevel;


            $this->objTemplate->setTemplate($arrTree[$intCounter]);
            $this->objTemplate->deletePlaceholder();
            $strReturn = $this->objTemplate->getTemplate();
        }

		return $strReturn;

	}



	/**
	 * Creates the tree recursive
	 *
	 * @param string $strStack
	 * @param int $intLevel
	 * @param string $strSystemid
	 * @param bool $bitFirst
	 * @param bool $bitLast
	 */
	private function createTree($strStack, $intLevel, $strSystemid, $bitFirst = false, $bitLast = false) {
		//zuerst wird das Stack-Array erzeugt
		$arrStack = explode(",", $strStack);

		//Hold the level
		if($intLevel > $this->intLevelMax)
			$this->intLevelMax = $intLevel;
        //any childs?
        $arrChilds = $this->getNaviLayer($strSystemid);

		//Add the current point
		//active or inactive
		if(in_array($strSystemid, $arrStack)) {
			if(!isset($this->arrTree[$intLevel]))
				$this->arrTree[$intLevel] = "";

			$this->arrTree[$intLevel] .= $this->createNavigationPoint(new class_modul_navigation_point($strSystemid), true, $intLevel, $bitFirst, $bitLast);
		}
		else {
			if(!isset($this->arrTree[$intLevel]))
				$this->arrTree[$intLevel] = "";

			$this->arrTree[$intLevel] .= $this->createNavigationPoint(new class_modul_navigation_point($strSystemid), false, $intLevel, $bitFirst, $bitLast);
		}

		//Let the childs present themselfes
		$intNumber = count($arrChilds);
		if($intNumber > 0) {
			//First and last are handled special
			$intJ = 1;
			foreach($arrChilds as $objOneChild) {

				if(in_array($objOneChild->getPrevid(), $arrStack) || $intLevel == 0) {
    				if($intJ == 1)
    					$this->createTree($strStack, $intLevel+1, $objOneChild->getSystemid(), true, false);
    				elseif ($intJ == $intNumber)
    					$this->createTree($strStack, $intLevel+1, $objOneChild->getSystemid(), false, true);
    				else
    					$this->createTree($strStack, $intLevel+1, $objOneChild->getSystemid());
    				}
				$intJ++;
			}
		}
	}

// --- Sitemapfunktionen --------------------------------------------------------------------------------

	/**
	 * creates the code for a sitemap
	 *
	 * @return string
	 */
	private function loadNavigationSitemap() {
		$strReturn = "";
		//check rights on the navigation
		if($this->objRights->rightView($this->arrElementData["navigation_id"]) && $this->getStatus($this->arrElementData["navigation_id"]) == 1) {
            //create a stack to highlight the points being active
            $strStack = $this->getActiveIdStack($this->getActivePagePointData()); //FIXME

            //build the navigation
            $strReturn = $this->sitemapRecursive(1, $this->arrTempNodes[$this->arrElementData["navigation_id"]], $strStack);
		}
		return $strReturn;
	}


	/**
	 * Creates a sitemap recursive level by level
	 *
	 * @param int $intLevel
	 * @param string $strSystemid
	 * @return string
	 */
	private function sitemapRecursive($intLevel, $objStartEntry, $strStack) {
		$strReturn = "";
		$arrChilds = $objStartEntry["subnodes"];

        $intNrOfChilds = count($arrChilds);
		//Anything to do right here?
		if($intNrOfChilds == 0)
			return "";

		//Iterate over every child
		for($intI = 0; $intI < $intNrOfChilds; $intI++) {
		    $arrOneChild = $arrChilds[$intI];
			//Check the rights
			if($arrOneChild["node"]->rightView()) {
                //current point active?
                $bitActive = false;
                if(uniStripos($strStack, $arrOneChild["node"]->getSystemid()) !== false)
                    $bitActive = true;

			    //Create the navigation point
			    if($intI == 0)
				    $strCurrentPoint = $this->createNavigationPoint($arrOneChild["node"], $bitActive, $intLevel, true);
				elseif ($intI == $intNrOfChilds-1)
				    $strCurrentPoint = $this->createNavigationPoint($arrOneChild["node"], $bitActive, $intLevel, false, true);
				else
				    $strCurrentPoint = $this->createNavigationPoint($arrOneChild["node"], $bitActive, $intLevel);

				//And load all points below
				$strChilds = $this->sitemapRecursive($intLevel+1, $arrOneChild, $strStack);

				//Put the childs below into the current template
				$this->objTemplate->setTemplate($strCurrentPoint);
				$arrTemp = array("level".($intLevel+1) => $strChilds);
				$strTemplate = $this->objTemplate->fillCurrentTemplate($arrTemp);

				$strReturn .= $strTemplate;
			}
		}

		//wrap into the wrapper-section
        $strLevelTemplateID = $this->objTemplate->readTemplate("/modul_navigation/".$this->arrElementData["navigation_template"], "level_".$intLevel."_wrapper");
        $strWrappedLevel = $this->fillTemplate(array("level".$intLevel => $strReturn), $strLevelTemplateID);
        if(uniStrlen($strWrappedLevel) > 0)
            $strReturn = $strWrappedLevel;

		return $strReturn;
	}

// --- Helpers ------------------------------------------------------------------------------------------

    /**
     * Builds a string of concatenated systemids. Those ids are the systemids of the page-points
     * being active. The delimiter is the ','-char.
     * Ths ids are sorted top to bottom. So the first one is current active one,
     * the last one is the last parent being active, so in most cases the node on the
     * first level of the navigation
     *
     * @param class_modul_navigation_point $objActivePoint
     * @return string
     */
    private function getActiveIdStack($objActivePoint) {
        $strStack = "";

        //Loading the points above
        $objTemp = $objActivePoint;

		if($objTemp == null) {
		  //Special case: no active point found --> load the first level inactive
		  $strStack = $this->arrElementData["navigation_id"];
		  $objTemp = new class_modul_navigation_point($this->arrElementData["navigation_id"]);
		}
		else {
		    $strStack = $objTemp->getSystemid();
		    $objTemp = new class_modul_navigation_point($objTemp->getPrevId());
		}

        while($objTemp->getPrevId() != $this->getModuleSystemid("navigation") && $objTemp->getStrName() != "") {
            $strStack .= ",".$objTemp->getSystemid();
            $objTemp = new class_modul_navigation_point($objTemp->getPrevId());
        }

        return $strStack;
    }

    /**
     * Tries to find the navigation point being active in the current navigation-tree.
     * Checks if the fallback-page has to be used, as long as the page is not linked in any other
     * navigation-element on the current page.
     *
     * @return class_modul_navigation_point
     */
    private function getActivePagePointData() {
        $objPagePointData = null;

        //Get Systemid of the current page in the navigations-table
		//short: find the point in the navigation linking on the current page
		//First try: The current page found by the constructor
		$objPagePointData = $this->loadPagePoint($this->strCurrentSite, $this->arrElementData["navigation_id"]);
		//If we find an empty array, the current page isn't in the tree.
		//as a workaround we could try to load the point of the page the user has visited before
		if($objPagePointData == null) {
			//check if the page is linked in another tree
            if(!$this->isPageVisibleInOtherNavigation()) {
			    $strFallbackPage = $this->objSession->getSession("navi_fallback_page_".$this->arrElementData["navigation_id"]);
			    if($strFallbackPage !== false) {
			        $objPagePointData = $this->loadPagePoint($strFallbackPage, $this->arrElementData["navigation_id"]);
			    }
			    else {
			        // Whoa. Now we got a problem. Suggestion: Load the Navigation with no activated point
			        $objPagePointData = null;
			    }
            }
            else
                $objPagePointData = null;
		}
		else {
		    //save this page as a fallback page, dependant of the navigation_id / navigation_tree
		    $this->objSession->setSession("navi_fallback_page_".$this->arrElementData["navigation_id"], $this->strCurrentSite);
		}

        return $objPagePointData;
    }


	/**
	 * Loads all navigations points one layer under the given systemid and verifies the permisson to view the node
	 *
	 * @param string $strSystemid
	 * @return mixed Array of objects
	 */
	private function getNaviLayer($strSystemid) {
	    $arrObjects = class_modul_navigation_point::getDynamicNaviLayer($strSystemid);
        $arrReturn = array();
        foreach($arrObjects as $arrOneObject)
            if($arrOneObject->rightView())
                $arrReturn[] = $arrOneObject;

        return $arrReturn;
	}




	/**
	 * Tries to load the data for the given pagename in the current navigation-tree
	 * If the page is being linked several times, the deepest point in the tree is searched and returned
	 *
	 * @param string $strPagename
	 * @param string $strNavigationId
	 * @return mixed
	 */
	private function loadPagePoint($strPagename, $strNavigationId) {
	    $objPoint = null;
	    $arrAllPoints = class_modul_navigation_point::loadPagePoint($strPagename);

	    $intCounter = 0;
	    foreach ($arrAllPoints as $objOnePoint) {
	        $intCurCounter = 0;
	        $objTemp = $objOnePoint;
    	    //now check, if its the correct navigation-tree and count levels
	        while($objTemp->getPrevid() != $this->getModuleSystemid("navigation")) {
	            $objTemp = new class_modul_navigation_point($objTemp->getPrevId());
	            $intCurCounter++;
	        }
	        if($objTemp->getSystemid() == $strNavigationId) {
	            if($intCurCounter >= $intCounter) {
	                $objPoint = $objOnePoint;
                    $intCounter = $intCurCounter;
                }
	        }
	    }
	    return $objPoint;
	}

	/**
	 * Searches the current page in other navigation-trees found on the current page.
	 * This can be usefull to avoid a session-based "opening" of the current tree.
	 * The user may find it confusing, if the current tree remains opened but he clicked
	 * a navigation-point of another tree.
	 *
	 * @return bool
	 */
	private function isPageVisibleInOtherNavigation() {

	   //load the placeholders placed on the current page-template. therefore, instantiate a page-object
       $objPageData = class_modul_pages_page::getPageByName($this->getPagename());
       $objMasterPageData = class_modul_pages_page::getPageByName("master");
       if($objPageData != null) {
           //analyze the placeholders on the page, faster than iterating the the elements available in the db
           $strTemplateId = $this->objTemplate->readTemplate("/modul_pages/".$objPageData->getStrTemplate());
           $arrElementsTemplate = $this->objTemplate->getElements($strTemplateId);
           $arrElementsTemplate = array_merge($this->objTemplate->getElements($strTemplateId, 0), $this->objTemplate->getElements($strTemplateId, 1));

           //loop elements to remove navigation-elements. to do so, get the current elements-name (maybe the user renamed the default "navigation")
          // var_dump($this->arrElementData);
          // var_dump($arrElementsTemplate);
           foreach($arrElementsTemplate as $arrPlaceholder) {
               if($arrPlaceholder["placeholder"] != $this->arrElementData["page_element_placeholder_placeholder"]) {
                  //loop the elements-list
                  foreach($arrPlaceholder["elementlist"] as $arrOneElement) {
                      if($arrOneElement["element"] == $this->arrElementData["page_element_placeholder_element"]) {

                          //seems as we have a navigation-element different than the current one.
                          //check, if the element is installed on the current page
                          $objElement = class_modul_pages_pageelement::getElementByPlaceholderAndPage($objPageData->getSystemid(), $arrPlaceholder["placeholder"], $this->getPortalLanguage());
                          //maybe on the masters-page?
                          if($objElement == null)
                              $objElement = class_modul_pages_pageelement::getElementByPlaceholderAndPage($objMasterPageData->getSystemid(), $arrPlaceholder["placeholder"], $this->getPortalLanguage());

                          if($objElement != null) {
                              //wohooooo, an element was found.
                              //check, if the current point is in the tree linked by the navigation - if it's a different navigation....
                          	  //load the real-pageelement
                          	  $objRealElement = new class_element_navigation($objElement);
                          	  $arrContent = $objRealElement->getElementContent($objElement->getSystemid());

                          	  if($arrContent["navigation_mode"] == "tree" && $this->loadPagePoint($this->strCurrentSite, $arrContent["navigation_id"]) != null) {
                          	      //jepp, page found in another tree, so return true
                          	      return true;
                          	  }

                          }


                      }

                  }

               }
           }

       }
	   return false;
	}

	/**
	 * Creates the html-code for one single navigationpoint. The check if the user has the needed rights should have been made before!
	 *
	 * @param class_modul_navigation_point $objPointData
	 * @param bool $bitActive
	 * @param int $intLevel
	 * @param bool $bitFirst
	 * @param bool $bitLast
	 * @return string
	 */
	private function createNavigationPoint($objPointData, $bitActive, $intLevel, $bitFirst= false, $bitLast = false) {
		//and start to create a link and all needed stuff
        $arrTemp = array();
		$arrTemp["page_intern"] = $objPointData->getStrPageI();
		$arrTemp["page_extern"] = $objPointData->getStrPageE();
		$arrTemp["text"] = $objPointData->getStrName();
		$arrTemp["link"] = getLinkPortal($arrTemp["page_intern"], $arrTemp["page_extern"], $objPointData->getStrTarget(), $arrTemp["text"]);
		$arrTemp["href"] = getLinkPortalHref($arrTemp["page_intern"], $arrTemp["page_extern"], "", "", "");
		$arrTemp["target"] = $objPointData->getStrTarget();
		if($objPointData->getStrImage() != "") {
			$arrTemp["image"] = getLinkPortal($arrTemp["page_intern"], $arrTemp["page_extern"], $objPointData->getStrTarget(), "<img src=\""._webpath_.$objPointData->getStrImage()."\" border=\"0\" alt=\"".$arrTemp["text"]."\"/>");
            $arrTemp["image_src"] = $objPointData->getStrImage();
        }

        if($objPointData->getStrPageI() != "") {
            $objPage = class_modul_pages_page::getPageByName($objPointData->getStrPageI());
            $arrTemp["lastmodified"] = strftime("%Y-%m-%dT%H:%M:%S", $objPage->getEditDate());
        }

		//Load the correct template
		$strSection = "level_".$intLevel."_".($bitActive ? "active" : "inactive").($bitFirst ? "_first" : "").($bitLast ? "_last" : "");
		$strTemplateId = $this->objTemplate->readTemplate("/modul_navigation/".$this->arrElementData["navigation_template"], $strSection);
		//Fill the template
		$strReturn = $this->objTemplate->fillTemplate($arrTemp, $strTemplateId, false);
		//BUT: if we received an empty string and are in the situation of a first or last point, then maybe the template
		//     didn't supply a first / last section. so we'll try to load a regular point
		if($strReturn == "" && ($bitFirst || $bitLast)) {
			$strSection = "level_".$intLevel."_".($bitActive ? "active" : "inactive");
			$strTemplateId = $this->objTemplate->readTemplate("/modul_navigation/".$this->arrElementData["navigation_template"], $strSection);
			//And fill it once more
			$strReturn = $this->objTemplate->fillTemplate($arrTemp, $strTemplateId, false);
		}

		return $strReturn;
	}

}

?>