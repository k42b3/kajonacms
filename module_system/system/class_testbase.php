<?php
/*"******************************************************************************************************
*   (c) 2007-2013 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                                             *
********************************************************************************************************/


require_once __DIR__."../../../bootstrap.php";


/**
 * The class_testbase is the common baseclass for all testcases.
 * Triggers the methods required to run proper PHPUnit tests such as starting the system-kernel
 *
 * @package module_system
 * @since 3.4
 * @author sidler@mulchprod.de
 */
abstract class class_testbase extends PHPUnit_Framework_TestCase {

    protected function setUp() {

        //echo "\n\nlogging test-setUp on ".get_class($this)." @ ".timeToString(time())."...\n";

        if(!defined("_block_config_db_loading_")) {
            define("_block_config_db_loading_", true);
        }

        if(!defined("_autotesting_")) {
            define("_autotesting_", true);
        }

        $objCarrier = class_carrier::getInstance();

        $strSQL = "UPDATE "._dbprefix_."system_config SET system_config_value = 'true'
                    WHERE system_config_name = '_system_changehistory_enabled_'";

        $objCarrier->getObjDB()->_query($strSQL);
        $objCarrier->getObjDB()->flushQueryCache();
        class_apc_cache::getInstance()->flushCache();

        class_config::getInstance()->loadConfigsDatabase(class_db::getInstance());

        //flush garbage collection, should avoid some segfaults on php 5.3.
        gc_collect_cycles();
        gc_disable();

        parent::setUp();
    }


    protected function tearDown() {

        //reenable garbage collection
        gc_enable();

        parent::tearDown();
    }

    protected function flushDBCache() {
        class_carrier::getInstance()->getObjDB()->flushQueryCache();
    }


    protected function createObject($strClassType, $strParentId) {
        //create the object
        $objReflector = new ReflectionClass($strClassType);
        $obj = $objReflector->newInstance();
        $obj->updateObjectToDb($strParentId);

        $arrReflectionProperties = $objReflector->getProperties();
        $objReflectorAnnotated = new class_reflection($strClassType);

        //set properties which are annotated with @var and have a setter method
        foreach($arrReflectionProperties as $objReflectionProperty) {
            $strPropName = $objReflectionProperty->getName();

            //check if the property is annotated with @tablecolumn
            if($objReflectorAnnotated->hasPropertyAnnotation($strPropName, class_orm_mapper::STR_ANNOTATION_TABLECOLUMN)) {
                $strSetterMethod = $objReflectorAnnotated->getSetter($strPropName);

                if($objReflector->hasMethod($strSetterMethod)) {
                    $objReflectionMethod = $objReflector->getMethod($strSetterMethod);

                    //determine the field type
                    $strDataType = $objReflectorAnnotated->getAnnotationValueForProperty($strPropName, "@var");
                    $strFieldType = $objReflectorAnnotated->getAnnotationValueForProperty($strPropName, "@fieldType");
                    $objMethodValue = null;

                    if($strDataType == "string") {
                        if($strFieldType != "dropdown") {
                            $objMethodValue = $strPropName."_".$obj->getStrSystemid();

                            if(strlen($objMethodValue) > 20) {
                                $objMethodValue = substr($objMethodValue, 0, 19);
                            }
                        }
                    }
                    else if($strDataType == "int") {
                        if($strFieldType != "dropdown") {
                            $objMethodValue = 0;
                        }
                    }
                    else if($strDataType == "class_date") {
                            $objMethodValue = new class_date();
                    }
                    else if($strDataType == "bool") {
                            $objMethodValue = false;
                    }

                    $objReflectionMethod->invoke($obj, $objMethodValue);
                }
            }
        }

        //save it
        $obj->updateObjectToDb($strParentId);
        return $obj;
    }

}


