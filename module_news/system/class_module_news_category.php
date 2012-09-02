<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_modul_news_category.php 4049 2011-08-03 14:59:29Z sidler $                                *
********************************************************************************************************/

/**
 * Model for a newscategory
 *
 * @package module_news
 * @author sidler@mulchprod.de
 *
 * @targetTable news_category.news_cat_id
 */
class class_module_news_category extends class_model implements interface_model, interface_admin_listable  {

    /**
     * @var string
     * @tableColumn news_category.news_cat_title
     */
    private $strTitle = "";


    /**
     * Constructor to create a valid object
     *
     * @param string $strSystemid (use "" on new objects)
     */
    public function __construct($strSystemid = "") {
        $this->setArrModuleEntry("moduleId", _news_module_id_);
        $this->setArrModuleEntry("modul", "news");

        //base class
        parent::__construct($strSystemid);
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin(). Alternatively, you may return an array containing
     *         [the image name, the alt-title]
     */
    public function getStrIcon() {
        return "icon_dot.png";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo() {
        return "";
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription() {
        return "";
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName() {
        return $this->getStrTitle();
    }


    /**
	 * Loads all available categories from the db
	 *
	 * @return class_module_news_category[]
	 * @static
	 */
	public static function getCategories() {
		$strQuery = "SELECT system_id FROM "._dbprefix_."news_category,
						"._dbprefix_."system
						WHERE system_id = news_cat_id
						ORDER BY news_cat_title";

		$arrIds = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array());
		$arrReturn = array();
		foreach($arrIds as $arrOneId)
		    $arrReturn[] = new class_module_news_category($arrOneId["system_id"]);

		return $arrReturn;
	}

    /**
     * Counts all available categories in the db
     *
     * @return int
     * @static
     */
    public static function getCategoriesCount() {
        $strQuery = "SELECT COUNT(*) FROM "._dbprefix_."news_category,
						"._dbprefix_."system
						WHERE system_id = news_cat_id
						ORDER BY news_cat_title";

        $arrRow = class_carrier::getInstance()->getObjDB()->getPRow($strQuery, array());
        return $arrRow["COUNT(*)"];
    }

	/**
	 * Loads all categories, the given news is in
	 *
	 * @param string $strSystemid
	 * @return class_module_news_category[]
	 * @static
	 */
	public static function getNewsMember($strSystemid) {
	    $strQuery = "SELECT newsmem_category as system_id FROM "._dbprefix_."news_member
	                   WHERE newsmem_news = ? ";
	    $arrIds = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array($strSystemid));
		$arrReturn = array();
		foreach($arrIds as $arrOneId)
		    $arrReturn[] = new class_module_news_category($arrOneId["system_id"]);

		return $arrReturn;
	}

	/**
	 * Deletes all memberships of the given NEWS
	 *
	 * @param string $strSystemid NEWS-ID
	 * @return bool
	 */
	public static function deleteNewsMemberships($strSystemid) {
	    $strQuery = "DELETE FROM "._dbprefix_."news_member
	                  WHERE newsmem_news = ?";
        return class_carrier::getInstance()->getObjDB()->_pQuery($strQuery, array($strSystemid));
	}

	public function deleteObject() {
	    //start by deleting from members an cat table
        $strQuery = "DELETE FROM "._dbprefix_."news_member WHERE newsmem_category = ?";
        if($this->objDB->_pQuery($strQuery, array($this->getSystemid()))) {
            return parent::deleteObject();
        }
        return false;
	}

    /**
     * @return string
     * @fieldType text
     * @fieldMandatory
     */
    public function getStrTitle() {
        return $this->strTitle;
    }

    public function setStrTitle($strTitle) {
        $this->strTitle = $strTitle;
    }

}