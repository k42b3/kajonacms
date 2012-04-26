<?php
/*"******************************************************************************************************
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_installer_tags.php 4413 2012-01-03 19:38:11Z sidler $                                          *
********************************************************************************************************/

/**
 * Class providing an install for the packagemanager module
 *
 * @package module_packagemanager
 */
class class_installer_packagemanager extends class_installer_base implements interface_installer {

	public function __construct() {

        $this->objMetadata = new class_module_packagemanager_metadata();
        $this->objMetadata->autoInit(uniStrReplace(array("/installer", _realpath_), array("", ""), __DIR__));

        $this->setArrModuleEntry("moduleId", _packagemanager_module_id_);
		parent::__construct();
	}

    public function install() {
		$strReturn = "";


        $strReturn .= "Installing table templatepacks...\n";

        $arrFields = array();
        $arrFields["templatepack_id"] 		    = array("char20", false);
        $arrFields["templatepack_name"] 	    = array("char254", true);

        if(!$this->objDB->createTable("templatepacks", $arrFields, array("templatepack_id")))
            $strReturn .= "An error occured! ...\n";

		//register the module
		$this->registerModule(
            "packagemanager",
            _packagemanager_module_id_,
            "",
            "class_module_packagemanager_admin.php",
            $this->objMetadata->getStrVersion(),
            true
        );

		$strReturn .= "Registering system-constants...\n";
        $this->registerConstant("_packagemanager_defaulttemplate_", "", class_module_system_setting::$int_TYPE_STRING, _packagemanager_module_id_);

		return $strReturn;

	}


	public function update() {
	    $strReturn = "";
        //check installed version and to which version we can update
        $arrModul = $this->getModuleData($this->objMetadata->getStrTitle(), false);
        $strReturn .= "Version found:\n\t Module: ".$arrModul["module_name"].", Version: ".$arrModul["module_version"]."\n\n";


        return $strReturn."\n\n";
	}

}