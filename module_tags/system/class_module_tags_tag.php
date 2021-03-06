<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                    *
********************************************************************************************************/

/**
 * Model-Class for tags.
 * There are two main purposes for this class:
 * - Representing the tag itself
 * - Acting as a wrapper to all tag-handling related methods such as assigning a tag
 *
 * @package module_tags
 * @author sidler@mulchprod.de
 * @since 3.4
 *
 * @targetTable tags_tag.tags_tag_id
 * @module tags
 * @moduleId _tags_modul_id_
 */
class class_module_tags_tag extends class_model implements interface_model, interface_admin_listable, interface_search_resultobject {

    /**
     * @var string
     * @tableColumn tags_tag.tags_tag_name
     * @tableColumnDatatype char254
     * @listOrder
     *
     * @addSearchIndex
     *
     * @fieldType text
     * @fieldMandatory
     */
    private $strName;

    /**
     * @var int
     * @tableColumn tags_tag.tags_tag_private
     * @tableColumnDatatype int
     */
    private $intPrivate = 0;

    /**
     * Constructor to create a valid object
     *
     * @param string $strSystemid (use "" on new objects)
     */
    public function __construct($strSystemid = "") {

        if(_tags_defaultprivate_ == "true")
            $this->intPrivate = 1;

        parent::__construct($strSystemid);

    }

    /**
     * Return an on-lick link for the passed object.
     * This link is used by the backend-search for the autocomplete-field
     *
     * @see getLinkAdminHref()
     * @return mixed
     */
    public function getSearchAdminLinkForObject() {
        return class_link::getLinkAdminHref($this->getArrModule("modul"), "showAssignedRecords", "&systemid=".$this->getSystemid());
    }


    /**
     * @return string
     */
    public function getStrDisplayName() {
        return $this->getStrName();
    }

    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon() {
        return "icon_tag";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     * @return string
     */
    public function getStrAdditionalInfo() {
        $strReturn = $this->getIntAssignments()." ".$this->getLang("tag_assignments", "tags");
        if($this->getIntPrivate() == 1)
            $strReturn .= ", ".$this->getLang("form_tags_private", "tags");

        return $strReturn;
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     * @return string
     */
    public function getStrLongDescription() {
        return "";
    }

    /**
     * Deletes the tag with the given systemid from the system
     *
     * @return bool
     */
    protected function deleteObjectInternal() {

        //delete matching favorites
        $arrFavorites = class_module_tags_favorite::getAllFavoritesForTag($this->getSystemid());
        foreach($arrFavorites as $objOneFavorite) {
            $objOneFavorite->deleteObject();
        }

        //delete memberships
        $strQuery1 = "DELETE FROM "._dbprefix_."tags_member WHERE tags_tagid=?";
        //delete the record itself
        if($this->objDB->_pQuery($strQuery1, array($this->getSystemid())))
            return parent::deleteObjectInternal();

        return false;
    }


    /**
     * Returns the list of tags related with the systemid passed.
     * If given, an attribute used to specify the relation can be passed, too.
     *
     * @param string $strSystemid
     * @param string $strAttribute
     * @return class_module_tags_tag[]
     */
    public static function getTagsForSystemid($strSystemid, $strAttribute = null) {

        $arrParams = array($strSystemid, class_carrier::getInstance()->getObjSession()->getUserID());

        $strWhere = "";
        if($strAttribute != null) {
            $strWhere = "AND tags_attribute = ?";
            $arrParams[] = $strAttribute;
        }

        $strQuery = "SELECT DISTINCT(tags_tagid), tags_tag_name
                       FROM "._dbprefix_."tags_member,
                            "._dbprefix_."tags_tag
                      WHERE tags_systemid = ?
                        AND tags_tag_id = tags_tagid
                        AND (tags_tag_private IS NULL OR tags_tag_private != 1 OR (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?))
                          ".$strWhere."
                   ORDER BY tags_tag_name ASC";

        $arrRows = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams);
        $arrReturn = array();
        foreach($arrRows as $arrSingleRow) {
            $arrReturn[] = new class_module_tags_tag($arrSingleRow["tags_tagid"]);
        }

        return $arrReturn;
    }

    /**
     * Returns a tag for a given tag-name - if present. Otherwise null.
     *
     * @param string $strName
     * @return class_module_tags_tag
     */
    public static function getTagByName($strName) {
        $strQuery = "SELECT tags_tag_id
                       FROM "._dbprefix_."tags_tag
                      WHERE tags_tag_name LIKE ?";
        $arrCols = class_carrier::getInstance()->getObjDB()->getPRow($strQuery, array(trim($strName)));
        if(isset($arrCols["tags_tag_id"]) && validateSystemid($arrCols["tags_tag_id"]))
            return new class_module_tags_tag($arrCols["tags_tag_id"]);
        else
            return null;
    }

    /**
     * Creates a list of tags matching the passed filter.
     *
     * @param string $strFilter
     * @return class_module_tags_tag[]
     */
    public static function getTagsByFilter($strFilter) {
        $strQuery = "SELECT tags_tag_id
                       FROM "._dbprefix_."tags_tag
                      WHERE tags_tag_name LIKE ?
                   ORDER BY tags_tag_name ASC";

        $arrRows = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array($strFilter."%"));
        $arrReturn = array();
        foreach($arrRows as $arrSingleRow) {
            $arrReturn[] = new class_module_tags_tag($arrSingleRow["tags_tag_id"]);
        }

        return $arrReturn;
    }

    /**
     * Loads all tags having at least one assigned systemrecord
     * and being active
     * @return class_module_tags_tag[]
     */
    public static function getTagsWithAssignments() {
        $strQuery = "SELECT DISTINCT(tags_tagid)
                       FROM "._dbprefix_."tags_member,
                            "._dbprefix_."tags_tag,
                            "._dbprefix_."system
                      WHERE tags_tag_id = tags_tagid
                        AND tags_tag_id = system_id
                        AND (tags_tag_private IS NULL OR tags_tag_private != 1 OR (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?))
                        AND system_status = 1";

        $arrRows = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array(class_carrier::getInstance()->getObjSession()->getUserID()));
        $arrReturn = array();
        foreach($arrRows as $arrSingleRow) {
            $arrReturn[] = new class_module_tags_tag($arrSingleRow["tags_tagid"]);
        }

        //search them by name
        usort(
            $arrReturn,
            function (class_module_tags_tag $objA, class_module_tags_tag $objB) {
                return strcmp($objA->getStrName(), $objB->getStrName());
            }
        );

        return $arrReturn;
    }

    /**
     * Loads the list of assignments.
     * Please note that this is only the raw array, not yet the object-structure.
     * By default, only active records are returned.
     * @return array
     */
    public function getListOfAssignments() {
        $strQuery = "SELECT member.*
                       FROM "._dbprefix_."tags_member as member,
                            "._dbprefix_."system as system,
                            "._dbprefix_."tags_tag as tag
                      WHERE tags_tagid = ?
                        AND system.system_id = member.tags_systemid
                        AND member.tags_tagid = tag.tags_tag_id
                        AND (tags_tag_private IS NULL OR tags_tag_private != 1 OR (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?))
                        ";

        return $this->objDB->getPArray($strQuery, array($this->getSystemid(), $this->objSession->getUserID()));
    }

    /**
     * Counts the number of assignments
     *
     * @return int
     */
    public function getIntAssignments() {
        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."tags_member as member,
                            "._dbprefix_."tags_tag as tag,
                            "._dbprefix_."system as system
                      WHERE member.tags_tagid = ?
                        AND member.tags_tagid = tag.tags_tag_id
                        AND system.system_id = member.tags_systemid
                        AND (tags_tag_private IS NULL OR tags_tag_private != 1 OR (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?)) ";

        $arrRow = $this->objDB->getPRow($strQuery, array($this->getSystemid(), $this->objSession->getUserID()));
        return $arrRow["COUNT(*)"];
    }

    /**
     * Loads a list of assigned records and creates the concrete instances.
     *
     * @param int $intStart
     * @param int $intEnd
     * @return class_model[]
     */
    public function getArrAssignedRecords($intStart = null, $intEnd = null) {
        $strQuery = "SELECT system.system_id
                       FROM "._dbprefix_."tags_member as member,
                            "._dbprefix_."tags_tag,
                            "._dbprefix_."system as system
                      WHERE tags_tagid = ?
                        AND tags_tagid = tags_tag_id
                        AND system.system_id = member.tags_systemid
                        AND (tags_tag_private IS NULL OR tags_tag_private != 1 OR (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?))
                   ORDER BY system_comment ASC";

        $arrRecords = $this->objDB->getPArray($strQuery, array($this->getSystemid(), $this->objSession->getUserID()), $intStart, $intEnd);

        $arrReturn = array();
        foreach($arrRecords as $arrOneRecord)
            $arrReturn[] = class_objectfactory::getInstance()->getObject($arrOneRecord["system_id"]);

        return $arrReturn;
    }

    /**
     * Connects the current tag with a systemid (and attribute, if given).
     * If the assignment already exists, nothing is done.
     *
     * @param string $strTargetSystemid
     * @param string $strAttribute
     * @return bool
     */
    public function assignToSystemrecord($strTargetSystemid, $strAttribute = null) {
        if($strAttribute == null)
            $strAttribute = "";

        $arrParams = array($strTargetSystemid, $this->getSystemid(), $strAttribute);

        $this->objDB->flushQueryCache();

        $strPrivate = "";
        if($this->getIntPrivate() == 1) {
            $strPrivate = "AND tags_owner = ?";
            $arrParams[] = $this->objSession->getUserID();
        }

        //check if not already set
        $strQuery = "SELECT COUNT(*)
                       FROM "._dbprefix_."tags_member
                      WHERE tags_systemid= ?
                        AND tags_tagid = ?
                        AND tags_attribute = ?
                        ".$strPrivate;
        $arrRow = $this->objDB->getPRow($strQuery, $arrParams, 0, false);
        if($arrRow["COUNT(*)"] != 0)
            return true;

        $strQuery = "INSERT INTO "._dbprefix_."tags_member
                      (tags_memberid, tags_systemid, tags_tagid, tags_attribute, tags_owner) VALUES
                      (?, ?, ?, ?, ?)";

        $bitReturn = $this->objDB->_pQuery($strQuery, array(generateSystemid(), $strTargetSystemid, $this->getSystemid(), $strAttribute, $this->objSession->getUserID()));

        //trigger an object update
        class_objectfactory::getInstance()->getObject($strTargetSystemid)->updateObjectToDb();

        return $bitReturn;
    }

    /**
     * Deletes an assignment of the current tag from the database.
     *
     * @param string $strTargetSystemid
     * @param string $strAttribute
     * @return bool
     */
    public function removeFromSystemrecord($strTargetSystemid, $strAttribute = null) {

        $arrParams = array();
        $arrParams[] = $strTargetSystemid;

        if($strAttribute != null)
            $arrParams[] = $strAttribute;

        $arrParams[] = $this->getSystemid();

        $strPrivate = "";
        if($this->getIntPrivate() == 1) {
            $strPrivate = "AND (tags_owner IS NULL OR tags_owner = '' OR tags_owner = ?)";
            $arrParams[] = $this->objSession->getUserID();
        }

        $strQuery = "DELETE FROM "._dbprefix_."tags_member
                           WHERE tags_systemid = ?
                             ".($strAttribute != null ? "AND tags_attribute = ?" : "")."
                             AND tags_tagid = ?
                             ".$strPrivate;

        $bitReturn = $this->objDB->_pQuery($strQuery, $arrParams);

        //trigger an object update
        class_objectfactory::getInstance()->getObject($strTargetSystemid)->updateObjectToDb();

        return $bitReturn;
    }


    /**
     * @param string $strNewPrevid
     * @param bool $bitChangeTitle
     *
     * @return bool
     */
    public function copyObject($strNewPrevid = "", $bitChangeTitle = true) {

        $strPrefix = $this->getStrName()."_";
        $intCount = 1;

        $strQuery = "SELECT COUNT(*) FROM "._dbprefix_."tags_tag WHERE tags_tag_name = ?";
        $arrRow = $this->objDB->getPRow($strQuery, array($strPrefix.$intCount));

        while($arrRow["COUNT(*)"] > 0) {
            $arrRow = $this->objDB->getPRow($strQuery, array($strPrefix.++$intCount));
        }

        $this->setStrName($strPrefix.$intCount);

        //save assigned records
        $arrRecords = $this->getListOfAssignments();

        parent::copyObject($strNewPrevid, $bitChangeTitle);

        //copy the tag assignments
        foreach($arrRecords as $arrOneRecord) {
            $this->assignToSystemrecord($arrOneRecord["tags_systemid"]);
        }

        return true;
    }


    /**
     * @return string
     */
    public function getStrName() {
        return $this->strName;
    }

    /**
     * @param string $strName
     * @return void
     */
    public function setStrName($strName) {
        $this->strName = trim($strName);
    }

    /**
     * @param int $intPrivate
     * @return void
     */
    public function setIntPrivate($intPrivate) {
        $this->intPrivate = $intPrivate;
    }

    /**
     * @return int
     */
    public function getIntPrivate() {
        return $this->intPrivate;
    }


}
