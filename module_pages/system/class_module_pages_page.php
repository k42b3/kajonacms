<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                   *
********************************************************************************************************/

/**
 * Model for a page
 *
 * @package module_pages
 * @author sidler@mulchprod.de
 * @targetTable page.page_id
 *
 * @module pages
 * @moduleId _pages_modul_id_
 */
class class_module_pages_page extends class_model implements interface_model, interface_versionable, interface_admin_listable, interface_search_resultobject, interface_search_portalobject {

    public static $INT_TYPE_PAGE = 0;
    public static $INT_TYPE_ALIAS = 1;

    /**
     * @var string
     * @tableColumn page_name
     * @tableColumnDatatype char254
     * @versionable
     * @addSearchIndex
     *
     * @fieldMandatory
     * @fieldType text
     */
    private $strName = "";

    /**
     * @var int
     * @tableColumn page_type
     * @tableColumnDatatype int
     * @versionable
     */
    private $intType = 0;

    /**
     * @var string
     * @versionable
     *
     * @addSearchIndex
     */
    private $strKeywords = "";

    /**
     * @var string
     * @versionable
     *
     * @fieldType textarea
     *
     * @addSearchIndex
     */
    private $strDescription = "";

    /**
     * @var string
     * @versionable
     *
     * @fieldType dropdown
     *
     * @addSearchIndex
     */
    private $strTemplate = "";

    /**
     * @var string
     * @versionable
     *
     * @fieldMandatory
     * @fieldType text
     *
     * @addSearchIndex
     */
    private $strBrowsername = "";

    /**
     * @var string
     * @versionable
     *
     * @addSearchIndex
     */
    private $strSeostring = "";

    /**
     * @var string
     * @versionable
     */
    private $strLanguage = "";

    /**
     * @var string
     * @versionable
     *
     * @fieldType page
     */
    private $strAlias = "";

    /**
     * @var string
     * @versionable
     */
    private $strPath = "";

    /**
     * @var string
     * @versionable
     *
     * @fieldType dropdown
     * @fieldDDValues [_self => page_targetself],[_blank => page_targetblank]
     */
    private $strTarget = "";

    private $strOldName;
    private $intOldType;
    private $strOldKeywords;
    private $strOldDescription;
    private $strOldTemplate;
    private $strOldBrowsername;
    private $strOldSeostring;
    private $strOldLanguage;
    private $strOldAlias;
    private $strOldTarget;

    /**
     * Constructor to create a valid object
     *
     * @param string $strSystemid (use "" on new objects)
     */
    public function __construct($strSystemid = "") {

        //init the object with the language currently selected - admin or portal
        if(defined("_admin_") && _admin_ === true) {
            $this->setStrLanguage($this->getStrAdminLanguageToWorkOn());
        }
        else {
            $this->setStrLanguage($this->getStrPortalLanguage());
        }

        //base class
        parent::__construct($strSystemid);

        $this->objSortManager = new class_pages_sortmanager($this);
    }

    /**
     * Return an on-lick link for the passed object.
     * This link is used by the backend-search for the autocomplete-field
     *
     * @see getLinkAdminHref()
     * @return mixed
     */
    public function getSearchAdminLinkForObject() {
        return class_link::getLinkAdminHref("pages_content", "list", "&systemid=".$this->getSystemid());
    }

    /**
     * Return an on-lick link for the passed object.
     * This link is rendered by the portal search result generator, so
     * make sure the link is a valid portal page.
     * If you want to suppress the entry from the result, return an empty string instead.
     *
     * @param class_search_result $objResult
     *
     * @see getLinkPortalHref()
     * @return mixed
     */
    public function updateSearchResult(class_search_result $objResult) {
        $objResult->setStrLinkPageI($this->getStrName());
        $objResult->setStrLinkText($this->getStrBrowsername());
        $objResult->setStrLinkParams("&highlight=" . urlencode(html_entity_decode($objResult->getObjSearch()->getStrQuery(), ENT_QUOTES, "UTF-8")));
        $objResult->setStrLinkPagename($this->getStrName());
    }

    /**
     * Since the portal may be split in different languages,
     * return the content lang of the current record using the common
     * abbreviation such as "de" or "en".
     * If the content is not assigned to any language, return "" instead (e.g. a single image).
     *
     * @return mixed
     */
    public function getContentLang() {
        return $this->getStrLanguage();
    }


    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName() {
        $strName = $this->getStrBrowsername();

        if($strName == "")
            $strName = $this->getStrName();

        return $strName;
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon() {
        if($this->getIntType() == self::$INT_TYPE_ALIAS) {
            return "icon_page_alias";
        }
        else {
            return "icon_page";
        }
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo() {
        if($this->getIntType() == self::$INT_TYPE_ALIAS) {
            return "-> " . uniStrTrim($this->getStrAlias(), 20);
        }
        else {
            return $this->getStrName();
        }
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
     * Initialises the current object, if a systemid was given
     * @return void
     */
    protected function initObjectInternal() {

        $strQuery = "SELECT *
                          FROM "._dbprefix_."system_right,
                               "._dbprefix_."page,
                               "._dbprefix_."page_properties,
                       ".$this->objDB->encloseTableName(_dbprefix_."system")."
                     LEFT JOIN "._dbprefix_."system_date
                            ON system_id = system_date_id
                         WHERE system_id = right_id
                           AND system_id = page_id
                           AND page_id = pageproperties_id
                           AND system_id = ?
                           AND pageproperties_language = ?";

        $arrRow = $this->objDB->getPRow($strQuery, array($this->getSystemid(), $this->getStrLanguage()));

        if(!isset($arrRow["page_id"]) || $arrRow["page_id"] == null) {
            $strQuery = "SELECT *
                          FROM "._dbprefix_."system_right,
                               "._dbprefix_."page,
                   ".$this->objDB->encloseTableName(_dbprefix_."system")."
                     LEFT JOIN "._dbprefix_."system_date
                            ON system_id = system_date_id
                         WHERE system_id = right_id
                           AND system_id = page_id
                           AND system_id = ? ";

            $arrRow = $this->objDB->getPRow($strQuery, array($this->getSystemid()));
            $arrRow["pageproperties_browsername"] = "";
            $arrRow["pageproperties_description"] = "";
            $arrRow["pageproperties_keywords"] = "";
            $arrRow["pageproperties_template"] = "";
            $arrRow["pageproperties_seostring"] = "";
            $arrRow["pageproperties_alias"] = "";
            $arrRow["pageproperties_path"] = "";
            $arrRow["pageproperties_target"] = "";
            $arrRow["pageproperties_language"] = $this->getStrLanguage();
        }


        $this->setArrInitRow($arrRow);

        if(isset($arrRow["page_name"])) {
            $this->setStrName($arrRow["page_name"]);
            $this->setIntType($arrRow["page_type"]);
            $this->setStrBrowsername($arrRow["pageproperties_browsername"]);
            $this->setStrDesc($arrRow["pageproperties_description"]);
            $this->setStrKeywords($arrRow["pageproperties_keywords"]);
            $this->setStrTemplate($arrRow["pageproperties_template"]);
            $this->setStrSeostring($arrRow["pageproperties_seostring"]);
            $this->setStrAlias($arrRow["pageproperties_alias"]);
            $this->setStrPath($arrRow["pageproperties_path"]);
            $this->setStrTarget($arrRow["pageproperties_target"]);
            $this->setStrLanguage($this->getStrLanguage());

            $this->strOldBrowsername = $arrRow["pageproperties_browsername"];
            $this->strOldDescription = $arrRow["pageproperties_description"];
            $this->strOldKeywords = $arrRow["pageproperties_keywords"];
            $this->strOldName = $this->strName;
            $this->intOldType = $this->intType;
            $this->strOldTemplate = $arrRow["pageproperties_template"];
            $this->strOldSeostring = $arrRow["pageproperties_seostring"];
            $this->strOldLanguage = $arrRow["pageproperties_language"];
            $this->strOldAlias = $arrRow["pageproperties_alias"];
            $this->strOldTarget = $arrRow["pageproperties_target"];
        }
    }

    /**
     * saves the current object as a new object to the database
     *
     * @return bool
     */
    public function onInsertToDb() {

        //Create the system-record
        if(_pages_newdisabled_ == "true") {
            $this->setIntRecordStatus(0);
        }

        $this->updatePath();

        //fix the initial sort-id
        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."system
                      WHERE system_prev_id = ?
                        AND (system_module_nr = ? OR system_module_nr = ?)";
        $arrRow = $this->objDB->getPRow($strQuery, array($this->getPrevId(), _pages_modul_id_, _pages_folder_id_));

        $this->setIntSort($arrRow["COUNT(*)"]);

        return true;
    }


    /**
     * Updates the current object to the database
     *
     * @return bool
     */
    protected function updateStateToDb() {

        //Make texts db-safe
        $strName = $this->generateNonexistingPagename($this->getStrName());
        $this->setStrName($strName);

        //create change-logs
        $objChanges = new class_module_system_changelog();
        $objChanges->createLogEntry($this, class_module_system_changelog::$STR_ACTION_EDIT);

        $this->updatePath();

        //Update the baserecord
        $bitBaseUpdate = parent::updateStateToDb();

        //and the properties record
        //properties for this language already existing?
        $strCountQuery = "SELECT COUNT(*)
                          FROM " . _dbprefix_ . "page_properties
		                 WHERE pageproperties_id= ?
		                   AND pageproperties_language= ?";
        $arrCountRow = $this->objDB->getPRow($strCountQuery, array($this->getSystemid(), $this->getStrLanguage()), 0, false);

        if((int)$arrCountRow["COUNT(*)"] >= 1) {
            //Already existing, updating properties
            $strQuery2 = "UPDATE  " . _dbprefix_ . "page_properties
    					SET pageproperties_description=?,
    						pageproperties_template=?,
    						pageproperties_keywords=?,
    						pageproperties_browsername=?,
    						pageproperties_seostring=?,
    						pageproperties_alias=?,
    						pageproperties_target=?,
                            pageproperties_path=?
    				  WHERE pageproperties_id=?
    				    AND pageproperties_language=?";

            $arrParams = array(
                $this->getStrDesc(),
                $this->getStrTemplate(),
                $this->getStrKeywords(),
                $this->getStrBrowsername(),
                $this->getStrSeostring(),
                $this->getStrAlias(),
                $this->getStrTarget(),
                $this->getStrPath(),
                $this->getSystemid(),
                $this->getStrLanguage()
            );
        }
        else {
            //Not existing, create one
            $strQuery2 = "INSERT INTO " . _dbprefix_ . "page_properties
						(pageproperties_id, pageproperties_keywords, pageproperties_description, pageproperties_template, pageproperties_browsername,
						 pageproperties_seostring, pageproperties_alias, pageproperties_target, pageproperties_language, pageproperties_path) VALUES
						(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $arrParams = array(
                $this->getSystemid(),
                $this->getStrKeywords(),
                $this->getStrDesc(),
                $this->getStrTemplate(),
                $this->getStrBrowsername(),
                $this->getStrSeostring(),
                $this->getStrAlias(),
                $this->getStrTarget(),
                $this->getStrLanguage(),
                $this->getStrPath()
            );
        }

        $bitBaseUpdate = $bitBaseUpdate && $this->objDB->_pQuery($strQuery2, $arrParams);

        $arrChildIds = $this->getChildNodesAsIdArray();

        foreach ($arrChildIds as $strChildId) {
            $objInstance = class_objectfactory::getInstance()->getObject($strChildId);
            $objInstance->updateObjectToDb();
        }

        return $bitBaseUpdate;
    }

    /**
     * Updates the navigation path of this page based on the parent's name.
     * @return void
     */
    public function updatePath() {
        $arrPathIds = $this->getPathArray("", _pages_modul_id_);
        $arrPathIds = array_slice($arrPathIds, 0, count($arrPathIds) - 1);
        $arrPathNames = array();

        foreach ($arrPathIds as $strParentId) {
            $objInstance = class_objectfactory::getInstance()->getObject($strParentId);

            if($objInstance instanceof class_module_pages_page) {
                $arrPathNames[] = urlSafeString($objInstance->getStrBrowsername());
            }
            //elseif($objInstance instanceof class_module_pages_folder) {
            //    $arrPathNames[] = urlSafeString($objInstance->getStrName());
            //}
        }

        $arrPathNames[] = urlSafeString($this->getStrBrowsername());

        $this->strPath = implode("/", $arrPathNames);
    }


    /**
     * Loads all pages known by the system
     *
     * @param int $intStart
     * @param int $intEnd
     * @param string $strFilter
     *
     * @return class_module_pages_page[]
     * @static
     */
    public static function getAllPages($intStart = null, $intEnd = null, $strFilter = "") {
        $arrParams = array();

        if($strFilter != "") {
            $arrParams[] = $strFilter . "%";
        }

        $strQuery = "SELECT *
					FROM " . _dbprefix_ . "page,
					     " . _dbprefix_ . "system_right,
					     " . _dbprefix_ . "system
				LEFT JOIN "._dbprefix_."system_date
                        ON system_id = system_date_id
					WHERE system_id = page_id
					  AND system_id = right_id
					" . ($strFilter != "" ? " AND page_name like ? " : "") . "
					ORDER BY page_name ASC";

        $arrIds = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams, $intStart, $intEnd);
        class_orm_rowcache::addArrayOfInitRows($arrIds);
        $arrReturn = array();
        foreach($arrIds as $arrOneId) {
            $arrReturn[] = class_objectfactory::getInstance()->getObject($arrOneId["system_id"]);
        }

        return $arrReturn;
    }

    /**
     * Returns a new page-instance, using the given name.
     * If not found, null is returned instead
     *
     * @param string $strName
     *
     * @return class_module_pages_page|null
     */
    public static function getPageByName($strName) {
        //strip possible anchors
        if(uniStrpos($strName, "#") !== false)
            $strName = uniSubstr($strName, 0, uniStrpos($strName, "#"));

        $strQuery = "SELECT page_id
						FROM " . _dbprefix_ . "page
						WHERE page_name= ?";
        $arrId = class_carrier::getInstance()->getObjDB()->getPRow($strQuery, array($strName));

        if(isset($arrId["page_id"]))
            return new class_module_pages_page($arrId["page_id"]);
        else
            return null;

    }

    /**
     * Checks, how many elements are on this page
     *
     * @param bool $bitJustActive
     *
     * @return int
     */
    public function getNumberOfElementsOnPage($bitJustActive = false) {
        //Check, if there are any Elements on this page
        $strQuery = "SELECT COUNT(*)
						 FROM " . _dbprefix_ . "page_element,
						      " . _dbprefix_ . "element,
						      " . _dbprefix_ . "system
						 WHERE system_prev_id=?
						   AND page_element_ph_element = element_name
						   AND system_id = page_element_id
						   " . ($bitJustActive ? "AND system_status = 1 " : "") . "
						   AND page_element_ph_language = ?";
        $arrRow = $this->objDB->getPRow($strQuery, array($this->getSystemid(), $this->getStrLanguage()));
        return $arrRow["COUNT(*)"];
    }

    /**
     * Checks, how many locked elements are on this page
     *
     * @return int
     */
    public function getNumberOfLockedElementsOnPage() {
        //Check, if there are any Elements on this page
        $strQuery = "SELECT COUNT(*)
						 FROM " . _dbprefix_ . "system as system
						  WHERE system_prev_id=?
							AND system_lock_id != ?
							AND system_lock_id != ? ";
        $arrRow = $this->objDB->getPRow($strQuery, array( $this->getSystemid(), $this->objSession->getUserID(), "0"));
        return $arrRow["COUNT(*)"];
    }

    /**
     * Deletes the given page and all related elements from the system.
     * Subelements, so pages and/or folders below are delete, too
     *
     * @return bool
     */
    protected function deleteObjectInternal() {

        //Delete the page and the properties out of the tables
        $strQuery2 = "DELETE FROM " . _dbprefix_ . "page_properties WHERE pageproperties_id = ?";
        $this->objDB->_pQuery($strQuery2, array($this->getSystemid()));

        return parent::deleteObjectInternal();
    }

    /**
     * Tries to assign all page-properties not yet assigned to a language.
     * If properties are already existing, the record won't be modified
     *
     * @param string $strTargetLanguage
     * @param bool $bitForce if true, all entries will be updated
     *
     * @return bool
     */
    public static function assignNullProperties($strTargetLanguage, $bitForce = false) {
        //Load all non-assigned props
        if($bitForce)
            $strQuery = "SELECT pageproperties_id FROM " . _dbprefix_ . "page_properties";
        else
            $strQuery = "SELECT pageproperties_id FROM " . _dbprefix_ . "page_properties WHERE pageproperties_language = '' OR pageproperties_language IS NULL";
        $arrPropIds = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array());

        foreach($arrPropIds as $arrOneId) {
            $strId = $arrOneId["pageproperties_id"];
            $strCountQuery = "SELECT COUNT(*)
                                FROM " . _dbprefix_ . "page_properties
                               WHERE pageproperties_language = ?
                                 AND pageproperties_id = ? ";
            $arrCount = class_carrier::getInstance()->getObjDB()->getPRow($strCountQuery, array($strTargetLanguage, $strId));

            if((int)$arrCount["COUNT(*)"] == 0) {
                $strUpdate = "UPDATE " . _dbprefix_ . "page_properties
                              SET pageproperties_language = ?
                              WHERE pageproperties_id = ? ";

                if(!class_carrier::getInstance()->getObjDB()->_pQuery($strUpdate, array($strTargetLanguage, $strId))) {
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Does a deep copy of the current page.
     * Inlcudes all page-elements created on the page
     * and all languages.
     *
     * @param string $strNewPrevid
     *
     * @return bool
     */
    public function copyObject($strNewPrevid = "") {


        $this->objDB->transactionBegin();

        //fetch data to be updated after the general copy process
        //page-properties, language dependant
        $arrBasicSourceProperties = $this->objDB->getPArray("SELECT * FROM " . _dbprefix_ . "page_properties WHERE pageproperties_id = ?", array($this->getSystemid()));

        //create a new page-name
        $this->setStrName($this->generateNonexistingPagename($this->getStrName(), false));

        //copy the page-instance and all elements on the page
        parent::copyObject($strNewPrevid);

        //update the pages' properties in the table - manually
        foreach($arrBasicSourceProperties as $arrOneProperty) {

            //insert or update - the properties for the current language should aready be in place
            $this->objDB->flushQueryCache();
            $arrCount = $this->objDB->getPRow("SELECT COUNT(*) FROM " . _dbprefix_ . "page_properties WHERE pageproperties_id = ? AND pageproperties_language = ? ", array($this->getSystemid(), $arrOneProperty["pageproperties_language"]));

            if($arrCount["COUNT(*)"] == 0) {

                $strQuery = "INSERT INTO " . _dbprefix_ . "page_properties
                (pageproperties_browsername,
                 pageproperties_keywords,
                 pageproperties_description,
                 pageproperties_template,
                 pageproperties_seostring,
                 pageproperties_alias,
                 pageproperties_path,
                 pageproperties_target,
                 pageproperties_language,
                 pageproperties_id
                ) VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            }
            else {
                $strQuery = "UPDATE " . _dbprefix_ . "page_properties
                        SET pageproperties_browsername = ?,
                            pageproperties_keywords = ?,
                            pageproperties_description = ?,
                            pageproperties_template = ?,
                            pageproperties_seostring = ?,
                            pageproperties_alias = ?,
                            pageproperties_path = ?,
                            pageproperties_target = ?
                      WHERE pageproperties_language = ?
                        AND pageproperties_id = ?";

            }

            $arrValues = array(
                $arrOneProperty["pageproperties_browsername"],
                $arrOneProperty["pageproperties_keywords"],
                $arrOneProperty["pageproperties_description"],
                $arrOneProperty["pageproperties_template"],
                $arrOneProperty["pageproperties_seostring"],
                $arrOneProperty["pageproperties_alias"],
                $arrOneProperty["pageproperties_path"],
                $arrOneProperty["pageproperties_target"],
                $arrOneProperty["pageproperties_language"],
                $this->getSystemid()
            );

            if(!$this->objDB->_pQuery($strQuery, $arrValues, array(false, false, false, false, false, false, false, false))) {
                $this->objDB->transactionRollback();
                class_logger::getInstance()->addLogRow("error while copying page properties", class_logger::$levelError);
                return false;
            }
        }


        $this->objDB->transactionCommit();

        return $this;
    }

    /**
     * Generates a pagename not yet existing.
     * Tries to detect if the new name is the name of the current page. If given, the same name
     * is being returned. Can be suppressed.
     *
     * @param string $strName
     * @param bool $bitAvoidSelfchek
     *
     * @return string
     */
    public function generateNonexistingPagename($strName, $bitAvoidSelfchek = true) {
        //Filter blanks out of pagename
        $strName = str_replace(" ", "_", $strName);

        //Pagename already existing?
        $strQuery = "SELECT page_id
					FROM " . _dbprefix_ . "page
					WHERE page_name=? ";
        $arrTemp = $this->objDB->getPRow($strQuery, array($strName));

        $intNumbers = count($arrTemp);
        if($intNumbers != 0 && !($bitAvoidSelfchek && $arrTemp["page_id"] == $this->getSystemid())) {
            $intCount = 1;
            $strTemp = "";
            while($intNumbers != 0 && !($bitAvoidSelfchek && $arrTemp["page_id"] == $this->getSystemid())) {
                $strTemp = $strName . "_" . $intCount;
                $strQuery = "SELECT page_id
							FROM " . _dbprefix_ . "page
							WHERE page_name=? ";
                $arrTemp = $this->objDB->getPRow($strQuery, array($strTemp));
                $intNumbers = count($arrTemp);
                $intCount++;
            }
            $strName = $strTemp;
        }
        return $strName;
    }


    /**
     * @param string $strAction
     *
     * @return string
     */
    public function getVersionActionName($strAction) {
        if($strAction == class_module_system_changelog::$STR_ACTION_EDIT) {
            return $this->getLang("seite_bearbeiten", "pages");
        }
        else if($strAction == class_module_system_changelog::$STR_ACTION_DELETE) {
            return $this->getLang("seite_loeschen", "pages");
        }

        return $strAction;
    }

    /**
     * @param string $strProperty
     * @param string $strValue
     *
     * @return string
     */
    public function renderVersionValue($strProperty, $strValue) {
        return $strValue;
    }

    /**
     * @param string $strProperty
     *
     * @return string
     */
    public function getVersionPropertyName($strProperty) {
        return $strProperty;
    }

    /**
     * @return string
     */
    public function getVersionRecordName() {
        return class_carrier::getInstance()->getObjLang()->getLang("change_object_page", "pages");
    }

    /**
     * @return string
     */
    public function getStrName() {
        return $this->strName;
    }

    /**
     * @return string
     */
    public function getStrKeywords() {
        return $this->strKeywords;
    }

    /**
     * @return string
     */
    public function getStrDesc() {
        return $this->strDescription;
    }

    /**
     * @return string
     */
    public function getStrTemplate() {
        return $this->strTemplate;
    }

    /**
     * @return string
     */
    public function getStrBrowsername() {
        return $this->strBrowsername;
    }

    /**
     * @return string
     */
    public function getStrSeostring() {
        return $this->strSeostring;
    }

    /**
     * @return string
     */
    public function getStrLanguage() {
        return $this->strLanguage;
    }

    /**
     * @param string $strName
     * @return void
     */
    public function setStrName($strName) {
        //make a valid pagename
        $strName = uniStrtolower(urlSafeString($strName));

        $this->strName = $strName;
    }

    /**
     * @param string $strKeywords
     * @return void
     */
    public function setStrKeywords($strKeywords) {
        $this->strKeywords = $strKeywords;
    }

    /**
     * @param string $strDesc
     * @return void
     */
    public function setStrDesc($strDesc) {
        $this->strDescription = $strDesc;
    }

    /**
     * @param string $strTemplate
     * @return void
     */
    public function setStrTemplate($strTemplate) {
        $this->strTemplate = $strTemplate;
    }

    /**
     * @param string $strBrowsername
     * @return void
     */
    public function setStrBrowsername($strBrowsername) {
        $this->strBrowsername = $strBrowsername;
    }

    /**
     * @param string $strSeostring
     * @return void
     */
    public function setStrSeostring($strSeostring) {
        //Remove permitted characters
        $this->strSeostring = urlSafeString($strSeostring);
    }

    /**
     * @param string $strLanguage
     * @return void
     */
    public function setStrLanguage($strLanguage) {
        $this->strLanguage = $strLanguage;
    }

    /**
     * @param string $strPath
     * @return void
     */
    public function setStrPath($strPath) {
        $this->strPath = $strPath;
    }

    /**
     * @return int
     */
    public function getIntType() {
        return $this->intType;
    }

    /**
     * @param int $intType
     * @return void
     */
    public function setIntType($intType) {
        $this->intType = $intType;
    }

    /**
     * @return string
     */
    public function getStrAlias() {
        return $this->strAlias;
    }

    /**
     * @param string $strAlias
     * @return void
     */
    public function setStrAlias($strAlias) {
        $this->strAlias = $strAlias;
    }

    /**
     * @param string $strDescription
     * @return void
     */
    public function setStrDescription($strDescription) {
        $this->strDescription = $strDescription;
    }

    /**
     * @return string
     */
    public function getStrDescription() {
        return $this->strDescription;
    }

    /**
     * @return string
     */
    public function getStrPath() {
        return $this->strPath;
    }

    /**
     * @param string $strTarget
     * @return void
     */
    public function setStrTarget($strTarget) {
        $this->strTarget = $strTarget;
    }

    /**
     * @return string
     */
    public function getStrTarget() {
        return $this->strTarget;
    }



}
