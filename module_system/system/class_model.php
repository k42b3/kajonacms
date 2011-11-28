<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2011 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*   $Id$                                               *
********************************************************************************************************/

/**
 * Top-level class for all model-classes.
 * Please be aware that all logic located in this class will be moved to class_root. This means that this
 * class will become useless. It will remain for API-compatibility but without any logic.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @deprectated this class will be removed from future releases, all logic will be moved to class root.
 *
 *
 */
class class_model extends class_root {

    public function __construct($arrModule, $strSystemid)  {

        parent::__construct($arrModule, $strSystemid, "model");
    }



// --- RATING -------------------------------------------------------------------------------------------

    /**
     * Rating of the current file, if module rating is installed.
     *
     * @param $bitRound Rounds the rating or disables rounding
     * @see interface_sortable_rating
     * @return float
     */
    public function getFloatRating($bitRound = true) {
        $floatRating = null;
        $objModule = class_modul_system_module::getModuleByName("rating");
        if($objModule != null) {
            $objRating = class_modul_rating_rate::getRating($this->getSystemid());
            if($objRating != null) {
               $floatRating = $objRating->getFloatRating();
               if($bitRound) {
                   $floatRating = round($floatRating, 2);
               }
            } else
               $floatRating = 0.0;
        }

        return $floatRating;
    }

    /**
     * Checks if the current user is allowed to rate the file
     *
     * @return bool
     */
    public function isRateableByUser() {
        $bitReturn = false;
        $objModule = class_modul_system_module::getModuleByName("rating");
        if($objModule != null) {
            $objRating = class_modul_rating_rate::getRating($this->getSystemid());
            if($objRating != null)
               $bitReturn = $objRating->isRateableByCurrentUser();
            else
               $bitReturn = true;
        }

        return $bitReturn;
    }

    /**
     * Number of rating for the current file
     *
     * @see interface_sortable_rating
     * @return int
     */
    public function getIntRatingHits() {
        $intHits = 0;
        $objModule = class_modul_system_module::getModuleByName("rating");
        if($objModule != null) {
            $objRating = class_modul_rating_rate::getRating($this->getSystemid());
            if($objRating != null)
               $intHits = $objRating->getIntHits();
            else
               return 0;
        }

        return $intHits;
    }

}
?>