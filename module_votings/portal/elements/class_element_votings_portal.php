<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_element_votings.php 4042 2011-07-25 17:37:44Z sidler $						               	*
********************************************************************************************************/

/**
 * Portal-part of the votings-element
 *
 * @package module_votings
 * @author sidler@mulchprod.de
 */
class class_element_votings_portal extends class_element_portal implements interface_portal_element {


    /**
     * Contructor
     *
     * @param class_module_pages_pageelement|mixed $objElementData
     * @internal param mixed $arrElementData
     */
	public function __construct($objElementData) {
        parent::__construct($objElementData);
        $this->setArrModuleEntry("table", _dbprefix_."element_universal");
    }


    /**
     * Loads the votings-class and passes control
     *
     * @return string
     */
	public function loadData() {
		$strReturn = "";
		//Load the data
		$objvotingsModule = class_module_system_module::getModuleByName("votings");
		if($objvotingsModule != null) {
    		$objVotings = $objvotingsModule->getPortalInstanceOfConcreteModule($this->arrElementData);
            $strReturn = $objVotings->action();
		}
		return $strReturn;
	}

}