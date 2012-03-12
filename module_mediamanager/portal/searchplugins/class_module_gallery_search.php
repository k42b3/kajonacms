<?php
/*"******************************************************************************************************
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_carrier.php 4059 2011-08-09 14:52:41Z sidler $                                            *
********************************************************************************************************/

/**
 * Search plugin of the gallery-module.
 *
 * @package module_mediamanager
 */
class class_modul_gallery_search extends class_portal implements interface_search_plugin  {

    private $arrTableConfig = array();
    private $arrSearchterm;
    private $strSearchtermRaw = "";
    private $arrHits = array();

    public function  __construct($arrSearchterm, $strSearchtermRaw) {
        parent::__construct();

        $this->arrSearchterm = $arrSearchterm;
        $this->strSearchtermRaw = $strSearchtermRaw;

        $arrSearch = array();


        //Gallery
        $arrSearch["gallery"] = array();
		$arrSearch["gallery"][_dbprefix_."gallery_pic"][] = "pic_name";
		$arrSearch["gallery"][_dbprefix_."gallery_pic"][] = "pic_description";
		$arrSearch["gallery"][_dbprefix_."gallery_pic"][] = "pic_subtitle";

		$this->arrTableConfig = $arrSearch;
    }


    public function doSearch() {
        if(class_modul_system_module::getModuleByName("gallery"))
          $this->searchGallery();
        return $this->arrHits;
    }


   /**
    * Searches the images in galleries
    *
    */
	private function searchGallery() {
		foreach($this->arrTableConfig["gallery"] as $strTable => $arrColumnConfig) {
			$arrWhere = array();
			//Build an or-statemement out of the columns
			foreach($arrColumnConfig as $strColumn) {
				foreach ($this->arrSearchterm as $strOneSeachterm)
                    $arrWhere[] = $strColumn.$strOneSeachterm;
			}
			$strWhere = "( ".implode(" OR ", $arrWhere). " ) ";

			//Query bauen
			$strQuery =
			"SELECT pic_name, pic_description, system_id, system_prev_id
			 FROM ".$strTable.",
			 		"._dbprefix_."system
			 WHERE ".$strWhere."
			 	AND system_id = pic_id
			 	AND system_status = 1";

			$arrPics = $this->objDB->getArray($strQuery);
			//register found pics
			if(count($arrPics) > 0) {
				foreach($arrPics as $arrOnePic) {

				    if(!$this->checkLanguage($arrOnePic)  || !$this->objRights->rightView($arrOnePic["system_id"]))
				        continue;

					if(isset($this->arrHits[$arrOnePic["system_id"]]["hits"]))
						$this->arrHits[$arrOnePic["system_id"]]["hits"]++;
					else {
    					$this->arrHits[$arrOnePic["system_id"]]["hits"] = 1;
    					$this->arrHits[$arrOnePic["system_id"]]["pagelink"] = getLinkPortal(_gallery_search_resultpage_, "", "_self", $arrOnePic["pic_name"], "detailImage", "&highlight=".html_entity_decode($this->strSearchtermRaw, ENT_QUOTES, "UTF-8"), $arrOnePic["system_id"], "", "", $arrOnePic["pic_name"]);
    					$this->arrHits[$arrOnePic["system_id"]]["pagename"] = _gallery_search_resultpage_;
    					$this->arrHits[$arrOnePic["system_id"]]["description"] = uniStrTrim($arrOnePic["pic_description"], 150);
					}
				}
			}
		}
	}

	/**
	 * Checks, if the hit is available on page using the current language
	 *
	 * @param array $arrOnePic
	 * @return bool true, if the post is visible
	 */
	private function checkLanguage($arrOnePic) {

        //Loop upwards to find the matching dl-repo
        $strPrevId = $arrOnePic["system_prev_id"];
        $intCount = 0;
        $intGalleryID = "";
        while($intCount == 0) {
            $strQuery = "SELECT COUNT(*)
                           FROM "._dbprefix_."gallery_gallery
                          WHERE gallery_id = '".dbsafeString($strPrevId)."'";
            $arrRow = $this->objDB->getRow($strQuery);
            $intCount = $arrRow["COUNT(*)"];
            $intGalleryID = $strPrevId;
            $objCommons = new class_modul_system_common($strPrevId);
            $strPrevId = $objCommons->getStrPrevId();
        }


        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."element_gallery,
                            "._dbprefix_."page_element,
                            "._dbprefix_."system
                      WHERE gallery_id = '".dbsafeString($intGalleryID)."'
                        AND content_id = page_element_id
                        AND content_id = system_id
                        AND system_status = 1
                        AND page_element_ph_language = '".dbsafeString($this->getPortalLanguage())."' " ;

        $arrRow = $this->objDB->getRow($strQuery);


        if(isset($arrRow["COUNT(*)"]) && (int)$arrRow["COUNT(*)"] >= 1)
            return true;

        return false;
	}

}
