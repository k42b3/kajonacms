<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2012 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                  *
********************************************************************************************************/

/**
 * Admin-Part of the tags.
 * No classical functionality, rather a list of helper-methods, e.g. in order to
 * create the form to tag content.
 *
 * @package module_tags
 * @author sidler@mulchprod.de
 */
class class_module_tags_admin extends class_admin_simple implements interface_admin {

	/**
	 * Constructor
	 */
	public function __construct() {
        $this->setArrModuleEntry("modul", "tags");
        $this->setArrModuleEntry("moduleId", _tags_modul_id_);
		parent::__construct();

	}

	protected function getOutputModuleNavi() {
	    $arrReturn = array();
        $arrReturn[] = array("right", getLinkAdmin("right", "change", "&changemodule=".$this->arrModule["modul"],  $this->getLang("commons_module_permissions"), "", "", true, "adminnavi"));
        $arrReturn[] = array("", "");
		$arrReturn[] = array("view", getLinkAdmin($this->arrModule["modul"], "list", "", $this->getLang("commons_list"), "", "", true, "adminnavi"));

        return $arrReturn;
	}

    protected function actionNew() {
    }

    /**
     * @return string
     * @autoTestable
     * @permissions view
     */
    protected function actionList() {

        $objArraySectionIterator = new class_array_section_iterator(class_module_tags_tag::getNumberOfTags());
        $objArraySectionIterator->setPageNumber((int)($this->getParam("pv") != "" ? $this->getParam("pv") : 1));
        $objArraySectionIterator->setArraySection(class_module_tags_tag::getAllTags($objArraySectionIterator->calculateStartPos(), $objArraySectionIterator->calculateEndPos()));

        return $this->renderList($objArraySectionIterator);
    }

    protected function getNewEntryAction($strListIdentifier) {
        return "";
    }


    /**
     * Generates the form to edit an existing tag
     * @param \class_admin_formgenerator|null $objForm
     * @return string
     * @permissions edit
     */
    protected function actionEdit(class_admin_formgenerator $objForm = null) {
        $objTag = new class_module_tags_tag($this->getSystemid());
		if($objTag->rightEdit()) {

            if($objForm == null)
                $objForm = $this->getAdminForm($objTag);

            return $objForm->renderForm(getLinkAdminHref($this->arrModule["modul"], "saveTag"));
		}
		else
			return $this->getLang("commons_error_permissions");

    }

    private function getAdminForm(class_module_tags_tag $objTag) {
        $objForm = new class_admin_formgenerator("tag", $objTag);
        $objForm->addDynamicField("name");

        return $objForm;
    }


    /**
     * Saves the passed tag-data back to the database.
     * @return string "" in case of success
     * @permissions edit
     */
    protected function actionSaveTag() {

        $objTag = new class_module_tags_tag($this->getSystemid());
        $objForm = $this->getAdminForm($objTag);

        if(!$objForm->validateForm())
            return $this->actionEdit($objForm);

		if($objTag->rightEdit()) {
            $objForm->updateSourceObject();
            $objTag->updateObjectToDb();
            $this->adminReload(getLinkAdminHref($this->arrModule["modul"]));
            return "";
		}
		else
			return $this->getLang("commons_error_permissions");
    }


    /**
     * Generates a form to add tags to the passed systemid.
     * Since all functionality is performed using ajax, there's no page-reload when adding or removing tags.
     * Therefore the form-handling of existing forms can remain as is
     *
     * @param string $strTargetSystemid the systemid to tag
     * @param string $strAttribute additional info used to differ between tag-sets for a single systemid
     * @return string
     * @permissions edit
     */
    public function getTagForm($strTargetSystemid, $strAttribute = null) {
        $strReturn = "";
        $strTagContent = "";

        $strTagsWrapperId = generateSystemid();

        $strTagContent .= $this->objToolkit->formHeader(getLinkAdminHref($this->arrModule["modul"], "saveTags"), "", "", "KAJONA.admin.tags.saveTag(document.getElementById('tagname').value+'', '".$strTargetSystemid."', '".$strAttribute."');return false;");
        $strTagContent .= $this->objToolkit->formTextRow($this->getLang("tag_name_hint"));
        $strTagContent .= $this->objToolkit->formInputTagSelector("tagname", $this->getLang("form_tag_name"));
        $strTagContent .= $this->objToolkit->formInputSubmit($this->getLang("button_add"), $this->getLang("button_add"), "");
        $strTagContent .= $this->objToolkit->formClose();

        $strTagContent .= $this->objToolkit->getTaglistWrapper($strTagsWrapperId, $strTargetSystemid, $strAttribute);

        $strReturn .= $this->objToolkit->divider();
        $strReturn .= $this->objToolkit->getFieldset($this->getLang("tagsection_header"), $strTagContent);

        return $strReturn;
    }


}
