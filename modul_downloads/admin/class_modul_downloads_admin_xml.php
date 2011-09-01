<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                             *
********************************************************************************************************/


/**
 * admin-class of the downloads-module
 * Serves xml-requests, e.g. syncing an archive
 *
 * @package modul_downloads
 * @author sidler@mulchprod.de
 */
class class_modul_downloads_admin_xml extends class_admin implements interface_xml_admin {

	/**
	 * Constructor
	 *
	 */
	public function __construct() {
        $arrModul = array();
		$arrModul["name"] 			= "modul_downloads";
		$arrModul["moduleId"] 		= _downloads_modul_id_;
		$arrModul["modul"]			= "downloads";

		//base class
		parent::__construct($arrModul);
	}



	/**
	 * Syncs the archive and creates a small report
	 *
	 * @return string
	 */
	protected function actionSyncArchive() {
		$strReturn = "";
		$strResult = "";

		$objArchive = new class_modul_downloads_archive($this->getSystemid());
        if($objArchive->rightRight1()) {
            $arrSyncs = class_modul_downloads_file::syncRecursive($objArchive->getSystemid(), $objArchive->getPath());
            $strResult .= $this->getText("syncro_ende")."<br />";
            $strResult .= $this->getText("sync_add").$arrSyncs["insert"]."<br />".$this->getText("sync_del").$arrSyncs["delete"]."<br />".$this->getText("sync_upd").$arrSyncs["update"];

            $strReturn .= "<archive>".$strResult."</archive>";
        }
        else {
            header(class_http_statuscodes::$strSC_UNAUTHORIZED);
            $strReturn .= "<message><error>".xmlSafeString($this->getText("commons_error_permissions"))."</error></message>";
        }

        class_logger::getInstance()->addLogRow("synced archive ".$this->getSystemid().": ".$strResult, class_logger::$levelInfo);

		return $strReturn;
	}

    /**
     * Syncs the gallery and creates a small report
     *
     * @return string
     */
    protected function actionMassSyncArchive() {
        $strReturn = "";
        $strResult = "";

        //load all galleries
        $arrArchives = class_modul_downloads_archive::getAllArchives();
        $arrSyncs = array( "insert" => 0, "delete" => 0, "update" => 0);
        foreach($arrArchives as $objOneArchive) {
            if($objOneArchive->rightRight1()) {
                $arrTemp = class_modul_downloads_file::syncRecursive($objOneArchive->getSystemid(), $objOneArchive->getPath());
                $arrSyncs["insert"] += $arrTemp["insert"];
                $arrSyncs["delete"] += $arrTemp["delete"];
                $arrSyncs["update"] += $arrTemp["update"];
            }
        }
        $strResult .= $this->getText("syncro_ende")."<br />";
        $strResult .= $this->getText("sync_add").$arrSyncs["insert"]."<br />".$this->getText("sync_del").$arrSyncs["delete"]."<br />".$this->getText("sync_upd").$arrSyncs["update"];

        $strReturn .= "<archive>".xmlSafeString(strip_tags($strResult))."</archive>";

        class_logger::getInstance()->addLogRow("mass synced archives: ".$strResult, class_logger::$levelInfo);
        return $strReturn;
    }


    /**
	 * Syncs the archive partially, so only a single level, and creates a small report
	 *
	 * @return string
	 */
	protected function actionPartialSyncArchive() {
		$strReturn = "";
		$strResult = "";

		$objFile = new class_modul_downloads_file($this->getSystemid());
        $strFilename = $objFile->getFilename();

        if($strFilename == "") {
            $objFile = new class_modul_downloads_archive ($this->getSystemid ());
            $strFilename = $objFile->getPath();
        }

        if($objFile->rightRight1()) {
            $arrSyncs = class_modul_downloads_file::syncRecursive($objFile->getSystemid(), $strFilename, false);

            $strResult .= $this->getText("syncro_ende")."<br />";
            $strResult .= $this->getText("sync_add").$arrSyncs["insert"]."<br />".$this->getText("sync_del").$arrSyncs["delete"]."<br />".$this->getText("sync_upd").$arrSyncs["update"];

            $strReturn .= "<archive>".$strResult."</archive>";
        }
        else {
            header(class_http_statuscodes::$strSC_UNAUTHORIZED);
            $strReturn .= "<message><error>".xmlSafeString($this->getText("commons_error_permissions"))."</error></message>";
        }

        class_logger::getInstance()->addLogRow("synced archive partially >".$objFile->getFilename()."< ".$this->getSystemid().": ".$strResult, class_logger::$levelInfo);

		return $strReturn;
	}


}

?>