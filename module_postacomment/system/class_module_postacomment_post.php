<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                           *
********************************************************************************************************/

/**
 * Model for comment itself
 *
 * @package module_postacomment
 * @author sidler@mulchprod.de
 *
 * @targetTable postacomment.postacomment_id
 *
 * @module postacomment
 * @moduleId _postacomment_modul_id_
 */
class class_module_postacomment_post extends class_model implements interface_model, interface_sortable_rating, interface_admin_listable, interface_search_portalobject {

    /**
     * @var string
     * @tableColumn postacomment_title
     * @tableColumnDatatype char254
     *
     * @fieldType text
     * @fieldLabel form_comment_title
     *
     * @addSearchIndex
     */
    private $strTitle;

    /**
     * @var string
     * @tableColumn postacomment_comment
     * @tableColumnDatatype text
     *
     * @fieldMandatory
     * @fieldType textarea
     * @fieldLabel postacomment_comment
     *
     * @addSearchIndex
     */
    private $strComment;

    /**
     * @var string
     * @tableColumn postacomment_username
     * @tableColumnDatatype char254
     *
     * @fieldMandatory
     * @fieldType text
     * @fieldLabel postacomment_username
     */
    private $strUsername;

    /**
     * @var int
     * @tableColumn postacomment_date
     * @tableColumnDatatype int
     */
    private $intDate;

    /**
     * @var string
     * @tableColumn postacomment_page
     * @tableColumnDatatype char254
     */
    private $strAssignedPage;

    /**
     * @var string
     * @tableColumn postacomment_systemid
     * @tableColumnDatatype char20
     */
    private $strAssignedSystemid;

    /**
     * @var string
     * @tableColumn postacomment_language
     * @tableColumnDatatype char20
     */
    private $strAssignedLanguage;


    /**
     * @return string
     */
    public function getStrDisplayName() {
        return $this->getStrTitle();
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
        return "icon_comment";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo() {
        return timeToString($this->intDate);
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription() {
        return uniStrTrim($this->strComment, 120);
    }


    /**
     * Returns a list of posts
     *
     * @param bool $bitJustActive
     * @param string $strPagefilter
     * @param string $strSystemidfilter false to ignore the filter
     * @param string $strLanguagefilter
     * @param bool $intStart
     * @param bool $intEnd
     *
     * @return class_module_postacomment_post[]
     */
    public static function loadPostList($bitJustActive = true, $strPagefilter = "", $strSystemidfilter = "", $strLanguagefilter = "", $intStart = false, $intEnd = false) {
        $arrReturn = array();
        $arrParams = array();
        $strFilter = "";
        if($strPagefilter != "") {
            $strFilter .= " AND postacomment_page = ? ";
            $arrParams[] = $strPagefilter;
        }

        if($strSystemidfilter != "") {
            $strFilter .= " AND postacomment_systemid = ? ";
            $arrParams[] = $strSystemidfilter;
        }

        if($strLanguagefilter != "") {//check against '' to remain backwards-compatible
            $strFilter .= " AND (postacomment_language = ? OR postacomment_language = '')";
            $arrParams[] = $strLanguagefilter;
        }
        if($bitJustActive) {
            $strFilter .= " AND system_status = 1 ";
        }

        $strQuery = "SELECT *
					 FROM "._dbprefix_."postacomment,
						  "._dbprefix_."system_right,
						  "._dbprefix_."system
				LEFT JOIN "._dbprefix_."system_date
                            ON system_id = system_date_id
					 WHERE system_id = postacomment_id
					   AND system_id = right_id "
                     . $strFilter ."
					 ORDER BY postacomment_page ASC,
						      postacomment_language ASC,
							  postacomment_date DESC";

        $arrComments = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, $arrParams, $intStart, $intEnd);
        class_orm_rowcache::addArrayOfInitRows($arrComments);
        if(count($arrComments) > 0) {
            foreach($arrComments as $arrOneComment) {
                $arrReturn[] = class_objectfactory::getInstance()->getObject($arrOneComment["system_id"]);
            }
        }

        return $arrReturn;
    }

    /**
     * Counts the number of posts currently in the database
     *
     * @param bool $bitJustActive
     * @param string $strPageid
     * @param bool|string $strSystemidfilter false to ignore the filter
     * @param string $strLanguagefilter
     *
     * @return int
     */
    public static function getNumberOfPostsAvailable($bitJustActive = true, $strPageid = "", $strSystemidfilter = "", $strLanguagefilter = "") {
        $strQuery = "SELECT COUNT(*) FROM "._dbprefix_."postacomment, "._dbprefix_."system WHERE system_id = postacomment_id ";
        $arrParams = array();

        if($strPageid != "") {
            $strQuery .= " AND postacomment_page= ?";
            $arrParams[] = $strPageid;
        }

        if($bitJustActive) {
            $strQuery .= " AND system_status = 1 ";
        }

        if($strSystemidfilter != "") {
            $strQuery .= " AND postacomment_systemid = ? ";
            $arrParams[] = $strSystemidfilter;
        }

        if($strLanguagefilter != "") {//check against '' to remain backwards-compatible
            $strQuery .= " AND (postacomment_language = ? OR postacomment_language = '')";
            $arrParams[] = $strLanguagefilter;
        }

        $arrRow = class_carrier::getInstance()->getObjDB()->getPRow($strQuery, $arrParams);
        return $arrRow["COUNT(*)"];
    }

    /**
     * Return an on-lick link for the passed object.
     * This link is rendered by the portal search result generator, so
     * make sure the link is a valid portal page.
     * If you want to suppress the entry from the result, return an empty string instead.
     * If you want to add additional entries to the result set, clone the result and modify
     * the new instance to your needs. Pack them in an array and they'll be merged
     * into the result set afterwards.
     * Make sure to return the passed result-object in this array, too.
     *
     * @param class_search_result $objResult
     *
     * @see getLinkPortalHref()
     * @return mixed
     */
    public function updateSearchResult(class_search_result $objResult) {
        $objPage = new class_module_pages_page($this->getStrAssignedPage());
        $objResult->setStrLinkPageI($objPage->getStrName());
        $objResult->setStrLinkText($this->getStrTitle() != "" ? $this->getStrTitle() : $objPage->getStrName());
        $objResult->setStrLinkPagename($objPage->getStrName());
        $objResult->setStrDescription($this->getStrComment());
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
        return $this->getStrAssignedLanguage();
    }

    /**
     * Return an on-lick link for the passed object.
     * This link is used by the backend-search for the autocomplete-field
     *
     * @see getLinkAdminHref()
     * @return mixed
     */
    public function getSearchAdminLinkForObject() {
        return "";
    }


    /**
     * @return string
     */
    public function getStrTitle() {
        return $this->strTitle;
    }

    /**
     * @return string
     */
    public function getStrComment() {
        return $this->strComment;
    }

    /**
     * @return string
     */
    public function getStrUsername() {
        return $this->strUsername;
    }

    /**
     * @return int
     */
    public function getIntDate() {
        if($this->intDate == null || $this->intDate == "") {
            $this->intDate = time();
        }

        return $this->intDate;
    }

    /**
     * @return string
     */
    public function getStrAssignedPage() {
        return $this->strAssignedPage;
    }

    /**
     * @return string
     */
    public function getStrAssignedSystemid() {
        return $this->strAssignedSystemid;
    }

    /**
     * @return string
     */
    public function getStrAssignedLanguage() {
        return $this->strAssignedLanguage;
    }

    /**
     * @param string $strTitle
     * @return void
     */
    public function setStrTitle($strTitle) {
        $this->strTitle = $strTitle;
    }

    /**
     * @param string $strComment
     * @return void
     */
    public function setStrComment($strComment) {
        $this->strComment = $strComment;
    }

    /**
     * @param string $strUsername
     * @return void
     */
    public function setStrUsername($strUsername) {
        $this->strUsername = $strUsername;
    }

    /**
     * @param int $intDate
     * @return void
     */
    public function setIntDate($intDate) {
        $this->intDate = $intDate;
    }

    /**
     * @param string $strAssignedPage
     * @return void
     */
    public function setStrAssignedPage($strAssignedPage) {
        $this->strAssignedPage = $strAssignedPage;
    }

    /**
     * @param string $strAssignedSystemid
     * @return void
     */
    public function setStrAssignedSystemid($strAssignedSystemid) {
        $this->strAssignedSystemid = $strAssignedSystemid;
    }

    /**
     * @param string $strAssignedLanguage
     * @return void
     */
    public function setStrAssignedLanguage($strAssignedLanguage) {
        $this->strAssignedLanguage = $strAssignedLanguage;
    }


}
