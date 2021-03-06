<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2014 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                    *
********************************************************************************************************/

/**
 * Copies assigned tags from one record to another
 *
 * @package module_tags
 * @author sidler@mulchprod.de
 *
 */
class class_module_tags_recordcopiedlistener implements interface_genericevent_listener {

    /**
     * Called whenever a record was copied.
     * copies the tag-assignments from the source object to the target object
     *
     * @param string $strEventName
     * @param array $arrArguments
     *
     * @return bool
     */
    public function handleEvent($strEventName, array $arrArguments) {

        //unwrap arguments
        $strOldSystemid = $arrArguments[0];
        $strNewSystemid = $arrArguments[1];

        $strQuery = "SELECT tags_tagid, tags_attribute, tags_owner
                       FROM "._dbprefix_."tags_member
                      WHERE tags_systemid = ?";
        $arrRows = class_carrier::getInstance()->getObjDB()->getPArray($strQuery, array($strOldSystemid));

        foreach($arrRows as $arrSingleRow) {
            $strQuery = "INSERT INTO "._dbprefix_."tags_member (tags_memberid, tags_tagid, tags_systemid, tags_attribute, tags_owner) VALUES (?, ?, ?, ?, ?)";
            class_carrier::getInstance()->getObjDB()->_pQuery($strQuery, array(generateSystemid(), $arrSingleRow["tags_tagid"], $strNewSystemid, $arrSingleRow["tags_attribute"], $arrSingleRow["tags_owner"]));
        }

        return true;
    }


    /**
     * Internal init to register the event listener, called on file-inclusion, e.g. by the class-loader
     * @return void
     */
    public static function staticConstruct() {
        class_core_eventdispatcher::getInstance()->removeAndAddListener(class_system_eventidentifier::EVENT_SYSTEM_RECORDCOPIED, new class_module_tags_recordcopiedlistener());
    }

}

//static init
class_module_tags_recordcopiedlistener::staticConstruct();
