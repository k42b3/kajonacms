<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                              *
********************************************************************************************************/

/**
 * Class to represent a guestbook post
 *
 * @package module_guestbook
 * @author sidler@mulchprod.de
 * @targetTable guestbook_post.guestbook_post_id
 *
 * @module guestbook
 * @moduleId _guestbook_module_id_
 */
class class_module_guestbook_post extends class_model implements interface_model, interface_admin_listable, interface_search_portalobject {

    /**
     * @var string
     * @tableColumn guestbook_post.guestbook_post_name
     * @tableColumnDatatype char254
     *
     * @fieldType text
     *
     * @addSearchIndex
     */
    private $strGuestbookPostName = "";

    /**
     * @var string
     * @tableColumn guestbook_post.guestbook_post_email
     * @tableColumnDatatype char254
     *
     * @fieldType text
     * @fieldValidator class_email_validator
     *
     * @addSearchIndex
     */
    private $strGuestbookPostEmail = "";

    /**
     * @var string
     * @tableColumn guestbook_post.guestbook_post_page
     * @tableColumnDatatype char254
     *
     * @fieldType text
     *
     * @addSearchIndex
     */
    private $strGuestbookPostPage = "";

    /**
     * @var string
     * @tableColumn guestbook_post.guestbook_post_text
     * @tableColumnDatatype text
     *
     * @fieldType textarea
     *
     * @addSearchIndex
     */
    private $strGuestbookPostText = "";

    /**
     * @var int
     * @tableColumn guestbook_post.guestbook_post_date
     * @tableColumnDatatype int
     */
    private $intGuestbookPostDate = 0;



    /**
     * Returns the icon the be used in lists.
     * Please be aware, that only the filename should be returned, the wrapping by getImageAdmin() is
     * done afterwards.
     *
     * @return string the name of the icon, not yet wrapped by getImageAdmin()
     */
    public function getStrIcon() {
        return "icon_book";
    }

    /**
     * In nearly all cases, the additional info is rendered left to the action-icons.
     *
     * @return string
     */
    public function getStrAdditionalInfo() {
        return timeToString($this->getIntGuestbookPostDate(), false) . " " . $this->getStrGuestbookPostEmail();
    }

    /**
     * If not empty, the returned string is rendered below the common title.
     *
     * @return string
     */
    public function getStrLongDescription() {
        return uniStrTrim($this->getStrGuestbookPostText(), 70);
    }

    /**
     * Returns the name to be used when rendering the current object, e.g. in admin-lists.
     *
     * @return string
     */
    public function getStrDisplayName() {
        return $this->getStrGuestbookPostName();
    }


    /**
     * Disables new posts if the guestbook itself is moderated.
     *
     * @return bool
     */
    protected function onInsertToDb() {
        $objGuestbook = new class_module_guestbook_guestbook($this->getPrevId());
        if($objGuestbook->getIntGuestbookModerated() == "1") {
            $this->setIntRecordStatus(0, false);
        }

        return true;
    }


    /**
     * Loads all posts belonging to the given systemid (in most cases a guestbook)
     *
     * @param string $strPrevId
     * @param bool $bitJustActive
     * @param null $intStart
     * @param null $intEnd
     *
     * @return class_module_guestbook_post[]
     * @static
     */
    public static function getPosts($strPrevId = "", $bitJustActive = false, $intStart = null, $intEnd = null) {
        $strQuery = "SELECT *
						FROM " . _dbprefix_ . "guestbook_post,
						     " . _dbprefix_ . "system_right,
						     " . _dbprefix_ . "system
				   LEFT JOIN "._dbprefix_."system_date
                            ON system_id = system_date_id
						WHERE system_id = guestbook_post_id
						  AND system_prev_id = ?
						  AND system_id = right_id
						  " . ($bitJustActive ? " AND system_status = 1" : "") . "
						ORDER BY guestbook_post_date DESC";

        $objDB = class_carrier::getInstance()->getObjDB();
        $arrPosts = $objDB->getPArray($strQuery, array($strPrevId), $intStart, $intEnd);
        class_orm_rowcache::addArrayOfInitRows($arrPosts);
        $arrReturn = array();
        //load all posts as objects
        foreach($arrPosts as $arrOnePostID) {
            $arrReturn[] = class_objectfactory::getInstance()->getObject($arrOnePostID["system_id"]);
        }
        return $arrReturn;
    }

    /**
     * Looks up the posts available
     *
     * @param string $strPrevID
     * @param bool $bitJustActive
     *
     * @return int
     * @static
     */
    public static function getPostsCount($strPrevID = "", $bitJustActive = false) {
        $strQuery = "SELECT COUNT(*)
						FROM " . _dbprefix_ . "guestbook_post, " . _dbprefix_ . "system
						WHERE system_id = guestbook_post_id
						  AND system_prev_id=?
						  " . ($bitJustActive ? " AND system_status = 1" : "") . "";

        $objDB = class_carrier::getInstance()->getObjDB();
        $arrRow = $objDB->getPRow($strQuery, array($strPrevID));
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
        $strQuery =  "SELECT page_name, guestbook_amount, page_id
                       FROM "._dbprefix_."element_guestbook,
                            "._dbprefix_."page_element,
                            "._dbprefix_."page,
                            "._dbprefix_."system
                      WHERE guestbook_id = ?
                        AND content_id = page_element_id
                        AND content_id = system_id
                        AND system_prev_id = page_id
                        AND system_status = 1
                        AND page_element_ph_language = ? " ;

        $arrRows = $this->objDB->getPArray($strQuery, array($this->getPrevId(), $objResult->getObjSearch()->getStrPortalLangFilter()));
        $arrReturn = array();
        foreach($arrRows as $arrOnePage) {

            //check, if the post is available on a page using the current language
            if(!isset($arrOnePage["page_name"]) || $arrOnePage["page_name"] == "")
                continue;

            //search pv position
            $intAmount = $arrOnePage["guestbook_amount"];
            $arrPostsInGB = class_module_guestbook_post::getPosts($this->getPrevId(), true);
            $intCounter = 0;
            foreach($arrPostsInGB as $objOnePostInGb) {
                $intCounter++;
                if($objOnePostInGb->getSystemid() == $this->getSystemid())
                    break;
            }
            //calculate pv
            $intPvPos = ceil($intCounter/$intAmount);

            $objNewResult = clone $objResult;
            $objNewResult->setStrLinkPageI($arrOnePage["page_name"]);
            $objNewResult->setStrLinkText($arrOnePage["page_name"]);
            $objNewResult->setStrLinkParams("&highlight=" . urlencode(html_entity_decode($objResult->getObjSearch()->getStrQuery(), ENT_QUOTES, "UTF-8")));
            $objNewResult->setStrLinkPagename($arrOnePage["page_name"]);
            $objNewResult->setStrDescription($this->getStrGuestbookPostText());

            $arrReturn[] = $objNewResult;
        }

        return $arrReturn;
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
        return "";
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


    public function setIntGuestbookPostDate($intGuestbookPostDate) {
        $this->intGuestbookPostDate = $intGuestbookPostDate;
    }

    public function getIntGuestbookPostDate() {
        return $this->intGuestbookPostDate;
    }

    public function setStrGuestbookPostEmail($strGuestbookPostEmail) {
        $this->strGuestbookPostEmail = $strGuestbookPostEmail;
    }

    public function getStrGuestbookPostEmail() {
        return $this->strGuestbookPostEmail;
    }

    public function setStrGuestbookPostName($strGuestbookPostName) {
        $this->strGuestbookPostName = $strGuestbookPostName;
    }

    public function getStrGuestbookPostName() {
        return $this->strGuestbookPostName;
    }

    public function setStrGuestbookPostPage($strGuestbookPostPage) {
        //Remove protocol-prefixes
        $strGuestbookPostPage = str_replace("http://", "", $strGuestbookPostPage);
        $strGuestbookPostPage = str_replace("https://", "", $strGuestbookPostPage);
        $this->strGuestbookPostPage = $strGuestbookPostPage;
    }

    public function getStrGuestbookPostPage() {
        return $this->strGuestbookPostPage;
    }

    public function setStrGuestbookPostText($strGuestbookPostText) {
        $this->strGuestbookPostText = $strGuestbookPostText;
    }

    public function getStrGuestbookPostText() {
        return $this->strGuestbookPostText;
    }

}
