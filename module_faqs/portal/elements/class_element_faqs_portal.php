<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_element_faqs.php 3679 2011-03-18 12:37:20Z sidler $						               	*
********************************************************************************************************/

/**
 * Portal-part of the faqs-element
 *
 * @package module_faqs
 * @author sidler@mulchprod.de
 */
class class_element_faqs_portal extends class_element_portal implements interface_portal_element {


	 /**
     * Contructor
     *
     * @param mixed $objElementData
     */
	public function __construct($objElementData) {
        $this->setArrModuleEntry("name", "element_faqs");
        $this->setArrModuleEntry("table", _dbprefix_."element_faqs");
        parent::__construct($objElementData);

        //we support ratings, so add cache-busters
        $this->setStrCacheAddon(getCookie(class_module_rating_rate::RATING_COOKIE));
	}


    /**
     * Loads the faqs-class and passes control
     *
     * @return string
     */
	public function loadData() {
		$strReturn = "";
		//Load the data
		$objFaqsModule = class_module_system_module::getModuleByName("faqs");
		if($objFaqsModule != null) {
    		$objFaqs = $objFaqsModule->getPortalInstanceOfConcreteModule($this->arrElementData);
            $strReturn = $objFaqs->action();
		}
		return $strReturn;
	}

}