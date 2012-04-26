<?php
/*"******************************************************************************************************
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id: class_module_tags_admin.php 4485 2012-02-07 12:48:04Z sidler $                                  *
********************************************************************************************************/

/**
 * Admin-GUI of the packagemanager.
 * The packagemanager provides a way to handle the template-packs available.
 * In addition, setting packs as the current active-one is supported, too.
 *
 * @package module_packagemanager
 * @author sidler@mulchprod.de
 * @since 4.0
 */
class class_module_packagemanager_admin extends class_admin_simple implements interface_admin {

    /**
     * Constructor
     */
    public function __construct() {
        $this->setArrModuleEntry("modul", "packagemanager");
        $this->setArrModuleEntry("moduleId", _packagemanager_module_id_);
        parent::__construct();

        class_module_packagemanager_template::syncTemplatepacks();
    }

    public function getOutputModuleNavi() {
        $arrReturn = array();
        $arrReturn[] = array("right", getLinkAdmin("right", "change", "&changemodule=".$this->arrModule["modul"],  $this->getLang("commons_module_permissions"), "", "", true, "adminnavi"));
        $arrReturn[] = array("", "");
        $arrReturn[] = array("view", getLinkAdmin($this->arrModule["modul"], "list", "", $this->getLang("actionList"), "", "", true, "adminnavi"));
        $arrReturn[] = array("view", getLinkAdmin($this->arrModule["modul"], "listTemplates", "", $this->getLang("actionListTemplates"), "", "", true, "adminnavi"));

        return $arrReturn;
    }

    public function getRequiredFields() {
        if($this->getAction() == "copyPack") {
            return array("pack_name" => "string");
        }

        return parent::getRequiredFields();
    }

    /**
     * Generic list of all packages available on the local filesystem
     * @return string
     * @permissions view
     * @autoTestable
     */
    protected function actionList() {
        $strReturn = "";
        $objManager = new class_module_packagemanager_manager();
        $arrPackages = $objManager->getAvailablePackages();

        $strReturn .= $this->objToolkit->listHeader();
        $intI = 0;
        foreach($arrPackages as $objOneMetadata) {
            $strReturn .= $this->objToolkit->simpleAdminList($objOneMetadata, "", $intI++);
        }

        $strAddActions = $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "addPackage", "", $this->getLang("actionUploadPackage"), $this->getLang("actionUploadPackage"), "icon_new.gif"));
        $strReturn .= $this->objToolkit->genericAdminList(generateSystemid(), "", "", $strAddActions, $intI);


        $strReturn .= $this->objToolkit->listFooter();

        return $strReturn;
    }


    /**
     * Validates a local package, renders the metadata
     * and provides, if feasible, a button to start the installation.
     *
     * @permissions edit
     * @return string
     */
    protected function actionProcessPackage() {
        $strReturn = "";
        $strFile = $this->getParam("package");

        $objManager = new class_module_packagemanager_manager();
        $objHandler = $objManager->getPackageManagerForPath($strFile);

        if($objManager->validatePackage($strFile)) {

            $strReturn .= $this->objToolkit->formHeadline($objHandler->getObjMetadata()->getStrTitle());
            $strReturn .= $this->objToolkit->getTextRow($objHandler->getObjMetadata()->getStrDescription());
            $strReturn .= $this->objToolkit->getTextRow($this->getLang("package_type")." ".$objHandler->getObjMetadata()->getStrType());
            $strReturn .= $this->objToolkit->getTextRow($this->getLang("package_version")." ".$objHandler->getObjMetadata()->getStrVersion());
            $strReturn .= $this->objToolkit->getTextRow($this->getLang("package_author")." ".$objHandler->getObjMetadata()->getStrAuthor());
            $strReturn .= $this->objToolkit->getTextRow($this->getLang("package_modules")." ".$objHandler->getObjMetadata()->getStrRequiredModules());
            $strReturn .= $this->objToolkit->getTextRow($this->getLang("package_minversion")." ".$objHandler->getObjMetadata()->getStrMinVersion());


            if(!$objHandler->getObjMetadata()->getBitProvidesInstaller() || $objHandler->isInstallable()) {
                $strReturn .= $this->objToolkit->formHeader(getLinkAdminHref($this->getArrModule("modul"), "installPackage"));
                $strReturn .= $this->objToolkit->formInputHidden("package", $strFile);
                $strReturn .= $this->objToolkit->formInputSubmit($this->getLang("package_doinstall"));
                $strReturn .= $this->objToolkit->formClose();
            }
            else {
                $strReturn .= $this->objToolkit->warningBox($this->getLang("package_notinstallable"));
            }


        }
        else
            return $this->getLang("provider_error_package");

        return $strReturn;
    }

    /**
     * @permissions edit
     * @return string
     */
    protected function actionInstallPackage() {
        $strReturn = "";
        $strFile = $this->getParam("package");

        $objManager = new class_module_packagemanager_manager();

        if($objManager->validatePackage($strFile)) {
            $objHandler = $objManager->extractPackage($strFile);
            $objFilesystem = new class_filesystem();
            $objFilesystem->fileDelete($strFile);

            $strReturn .= $objHandler->move2Filesystem();

            if($objHandler->getObjMetadata()->getBitProvidesInstaller())
                $strReturn .= $objHandler->installOrUpdate();

            if($objHandler instanceof class_module_packagemanager_packagemanager_module)
                $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "list"));
        }

        return $strReturn;
    }


    /**
     * Generates the gui to add new packages
     * @return string
     * @permissions edit
     * @autoTestable
     */
    protected function actionAddPackage() {
        $objManager = new class_module_packagemanager_manager();
        $arrContentProvider = $objManager->getContentproviders();
        if($this->getParam("provider") == "") {
            $strReturn = $this->objToolkit->listHeader();

            $intI = 0;
            foreach($arrContentProvider as $objOneProvider) {
                $strReturn .= $this->objToolkit->genericAdminList(
                    generateSystemid(),
                    $objOneProvider->getDisplayTitle(),
                    getImageAdmin("icon_dot.gif"),
                    $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "addPackage", "&provider=".get_class($objOneProvider), $this->getLang("provider_select"), $this->getLang("provider_select"), "icon_accept.gif")),
                    $intI++
                );
            }

            $strReturn .= $this->objToolkit->listFooter();
            return $strReturn;
        }

        $strProvider = $this->getParam("provider");
        $objProvider = null;
        foreach($arrContentProvider as $objOneProvider)
            if(get_class($objOneProvider) == $strProvider)
                $objProvider = $objOneProvider;

        if($objProvider == null)
            return $this->getLang("commons_error_permissions");

        return $objProvider->renderPackageList();
    }

    /**
     * @permissions edit
     * @return string
     */
    protected function actionUploadPackage() {
        $objManager = new class_module_packagemanager_manager();
        $arrContentProvider = $objManager->getContentproviders();

        $strProvider = $this->getParam("provider");
        $objProvider = null;
        foreach($arrContentProvider as $objOneProvider)
            if(get_class($objOneProvider) == $strProvider)
                $objProvider = $objOneProvider;

        if($objProvider == null)
            return $this->getLang("commons_error_permissions");

        $strFile = $objProvider->processPackageUpload();

        if($strFile == null)
            return $this->getLang("provider_error_transfer", "packagemanager");

        if(!$objManager->validatePackage($strFile)) {
            $objFilesystem = new class_filesystem();
            $objFilesystem->fileDelete($strFile);
            return $this->getLang("provider_error_package", "packagemanager");
        }

        $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "processPackage", "&package=".$strFile));
        return "";
    }



    /**
     * @return string
     * @autoTestable
     * @permissions view
     */
    protected function actionListTemplates() {

        $objArraySectionIterator = new class_array_section_iterator(class_module_packagemanager_template::getAllTemplatepacksCount());
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(
            class_module_packagemanager_template::getAllTemplatepacks($objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos())
        );

        return $this->renderList($objArraySectionIterator);
    }

    protected function getNewEntryAction($strListIdentifier, $bitDialog = false) {
        $strReturn = "";
        if($this->getObjModule()->rightEdit()) {
            //$strReturn .= $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "download", "", $this->getLang("action_download"), $this->getLang("action_download"), "icon_install.gif"));
            $strReturn .= $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "uploadPackage", "", $this->getLang("actionUploadPackage"), $this->getLang("actionUploadPackage"), "icon_upload.gif"));
            $strReturn .= $this->objToolkit->listButton(getLinkAdmin($this->getArrModule("modul"), "new", "", $this->getLang("action_new_copy"), $this->getLang("action_new_copy"), "icon_new.gif"));
        }

        return $strReturn;
    }

    protected function renderEditAction(class_model $objListEntry, $bitDialog = false) {
        return "";
    }

    protected function renderStatusAction(class_model $objListEntry) {
        if($objListEntry->rightEdit()) {
            if(_packagemanager_defaulttemplate_ == $objListEntry->getStrName()) {
                return $this->objToolkit->listButton(getImageAdmin("icon_enabled.gif", $this->getLang("pack_active_no_status")));
            }
            else
                return $this->objToolkit->listStatusButton($objListEntry, true);
        }
    }


    protected function renderDeleteAction(interface_model $objListEntry) {
        if($objListEntry->rightDelete()) {
            if(_packagemanager_defaulttemplate_ == $objListEntry->getStrName()) {
                return $this->objToolkit->listButton(getImageAdmin("icon_tonDisabled.gif", $this->getLang("pack_active_no_delete")));
            }
            else
                return $this->objToolkit->listDeleteButton(
                    $objListEntry->getStrDisplayName(), $this->getLang("delete_question"), getLinkAdminHref($this->getArrModule("modul"), "deleteTemplate", "&systemid=".$objListEntry->getSystemid()."")
                );
        }
    }

    /**
     * Wrapper to delete a template-pack
     *
     * @return void
     */
    protected function actionDeleteTemplate() {
        parent::actionDelete();
        $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "listTemplates"));
    }


    /**
     * @return string
     * @permissions edit
     */
    protected function actionEdit() {
        return $this->getLang("commons_error_permissions");
    }

    /**
     * @param \class_admin_formgenerator|null $objForm
     *
     * @return string
     * @permissions edit
     */
    protected function actionNew(class_admin_formgenerator $objForm = null) {
        if($objForm == null)
            $objForm = $this->getPackAdminForm();

        return $objForm->renderForm(getLinkAdminHref($this->getArrModule("modul"), "copyPack"));
    }

    private function getPackAdminForm() {
        $objFormgenerator = new class_admin_formgenerator("pack", new class_module_system_common());
        $objFormgenerator->addField(new class_formentry_text("pack", "name"))->setStrLabel($this->getLang("pack_name"))->setBitMandatory(true)->setStrValue($this->getParam("pack_name"));
        $objFormgenerator->addField(new class_formentry_headline())->setStrValue($this->getLang("pack_copy_include"));
        $arrModules = class_resourceloader::getInstance()->getArrModules();
        foreach($arrModules as $strOneModule) {
            //validate if there's a template-folder existing
            if(is_dir(_corepath_."/".$strOneModule."/templates"))
                $objFormgenerator->addField(new class_formentry_checkbox("pack", "modules[".$strOneModule."]"))->setStrLabel($strOneModule)->setStrValue(true);
        }
        return $objFormgenerator;
    }

    /**
     * @permissions edit
     * @return string
     */
    protected function actionCopyPack() {
        $objForm = $this->getPackAdminForm();

        $strPackName = $this->getParam("pack_name");
        $strPackName = createFilename($strPackName, true);

        if(is_dir(_realpath_._templatepath_."/".$strPackName))
            $objForm->addValidationError("name", $this->getLang("pack_folder_existing"));

        if(!$objForm->validateForm())
            return $this->actionNew($objForm);


        $objFilesystem = new class_filesystem();
        $objFilesystem->folderCreate(_templatepath_."/".$strPackName);

        $arrModules = $this->getParam("pack_modules");
        foreach($arrModules as $strName => $strValue) {
            if($strValue != "") {
                $objFilesystem->folderCopyRecursive("/core/".$strName."/templates/default", _templatepath_."/".$strPackName);
            }
        }

        $this->adminReload(getLinkAdminHref($this->getArrModule("modul"), "listTemplates"));
    }
}