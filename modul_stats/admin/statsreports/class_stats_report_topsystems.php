<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2009 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
*-------------------------------------------------------------------------------------------------------*
*	$Id$                            *
********************************************************************************************************/

//Interface
include_once(_adminpath_."/interface_admin_statsreport.php");

/**
 * This plugin creates a view common numbers, such as "user online" oder "total pagehits"
 *
 * @package modul_stats
 */
class class_stats_report_topsystems implements interface_admin_statsreports {

	//class vars
	private $intDateStart;
	private $intDateEnd;
	private $intInterval;

	private $objTexts;
	private $objToolkit;
	private $objDB;

	private $arrModule;

	private $arrBrowserGiven;
	private $arrBrowserGiven2;
	private $arrSystemCache = array();

	/**
	 * Constructor
	 *
	 */
	public function __construct($objDB, $objToolkit, $objTexts) {
		$this->arrModule["name"] 			= "modul_stats_reports_topsystems";
		$this->arrModule["author"] 			= "sidler@mulchprod.de";
		$this->arrModule["moduleId"] 		= _stats_modul_id_;
		$this->arrModule["table"] 		    = _dbprefix_."stats_data";
		$this->arrModule["modul"]			= "stats";

		$this->objTexts = $objTexts;
		$this->objToolkit = $objToolkit;
		$this->objDB = $objDB;

		//parse browser (browscap.ini)
		$arrBrowserGiven = parse_ini_file(_systempath_."/php_browscap.ini", true);

		//Update Array once to handle regex
		$arrSearch =  array( ".", "+", "^", "$", "!", "{", "}", "(", ")", "]", "[", "*", "?", "#");
		$arrReplace = array( "\.", "\+", "\^", "\$", "\!", "\{", "\}", "\(", "\)", "\]", "\[", ".*", ".", "\#");

		foreach($arrBrowserGiven as $strSignatureGiven => $arrBrowserData) 	{
			$strSignature = str_replace($arrSearch, $arrReplace, $strSignatureGiven);
			$arrBrowserGiven2[$strSignature] = $arrBrowserData;
		}
		$this->arrBrowserGiven = $arrBrowserGiven;
		$this->arrBrowserGiven2 = $arrBrowserGiven2;
	}

	public function setEndDate($intEndDate) {
	    $this->intDateEnd = $intEndDate;
	}

	public function setStartDate($intStartDate) {
	    $this->intDateStart = $intStartDate;
	}

	public function getReportTitle() {
	    return  $this->objTexts->getText("topsystem", "stats", "admin");
	}

	public function getReportCommand() {
	    return "statsTopSystem";
	}

	public function isIntervalable() {
	    return true;
	}

	public function setInterval($intInterval) {
	    $this->intInterval = $intInterval;
	}

	public function getReport() {
	    $strReturn = "";

        //Create Data-table
        $arrHeader = array();
        $arrValues = array();
        //Fetch data
		$arrStats = $this->getTopSystem();

		//calc a few values
		$intSum = 0;
		foreach($arrStats as $arrOneStat)
			$intSum += $arrOneStat;

		$intI =0;
		foreach($arrStats as $strName => $arrOneStat) {
			//Escape?
			if($intI >= _stats_nrofrecords_)
				break;

            $arrValues[$intI] = array();
			$arrValues[$intI][] = $intI+1;
			$arrValues[$intI][] = $strName;
			$arrValues[$intI][] = $arrOneStat;
			$arrValues[$intI][] = $this->objToolkit->percentBeam($arrOneStat / $intSum*100);
			$intI++;
		}
		//HeaderRow
		$arrHeader[] = "#";
		$arrHeader[] = $this->objTexts->getText("top_system_titel", "stats", "admin");
		$arrHeader[] = $this->objTexts->getText("top_system_gewicht", "stats", "admin");
		$arrHeader[] = $this->objTexts->getText("anteil", "stats", "admin");

		$strReturn .= $this->objToolkit->dataTable($arrHeader, $arrValues);

		return $strReturn;
	}



    /**
     * Loads a list of systems accessed the page
     *
     * @return mixed
     */
	public function getTopSystem() {
		$arrReturn = array();
		//load Data
		$strQuery = "SELECT stats_browser, count(*) as anzahl
						FROM ".$this->arrModule["table"]."
						WHERE stats_date >= ".(int)$this->intDateStart."
							AND stats_date <= ".(int)$this->intDateEnd."
						GROUP BY stats_browser";
		$arrBrowser = $this->objDB->getArray($strQuery);


		$arrBrowserGiven = &$this->arrBrowserGiven;
		$arrBrowserGiven2 = &$this->arrBrowserGiven2;

        //Search the best matching pattern
        foreach($arrBrowser as $arrRow) {
            $strPrevMatchingSignature = "";
            //Browser already found before?
            $strBrowserSignature = $arrRow["stats_browser"];
            if(!isset($this->arrSystemCache[$strBrowserSignature])) {
    			//Lookup in browsers
    			foreach($arrBrowserGiven2 as $strSignature => $arrBrowserData) {
                    //Current browser matching the browscap signature?
    				if(preg_match("#".$strSignature."#", $arrRow["stats_browser"])) {
    				    //better match then the one before?
    				    if(uniStrlen($strPrevMatchingSignature) <= uniStrlen($strSignature)) {
    				        //yes, save for next run
    				        $strPrevMatchingSignature = $strSignature;
    				    }
    				}
    			}

    			$arrCurrentBrowser = $arrBrowserGiven2[$strPrevMatchingSignature];
    			$strPlatform = isset($arrCurrentBrowser["Platform"]) ? $arrCurrentBrowser["Platform"] : "unknown";

    			$this->arrSystemCache[$strBrowserSignature] = $strPlatform;
            }
            else
                $strPlatform = $this->arrSystemCache[$strBrowserSignature];

			if(!isset($arrReturn[$strPlatform]))
				$arrReturn[$strPlatform] = $arrRow["anzahl"];
			else
				$arrReturn[$strPlatform] += $arrRow["anzahl"];
        }

		arsort($arrReturn);
		return $arrReturn;
	}

	public function getReportGraph() {
	    $arrReturn = array();
        include_once(_systempath_."/class_graph_pchart.php");
        $arrData = $this->getTopSystem();

        $intSum = 0;
		foreach($arrData as $arrOneStat)
			$intSum += $arrOneStat;

        $arrKeyValues = array();
        //max 6 entries
        $intCount = 0;
        $floatPercentageSum = 0;
        $arrValues = array();
        $arrLabels = array();
        foreach($arrData as $strName => $intOneSystem) {
            if(++$intCount <= 6) {
                $floatPercentage = $intOneSystem / $intSum*100;
                $floatPercentageSum += $floatPercentage;
                $arrKeyValues[$strName] = $floatPercentage;
                $arrLabels[] = $strName;
                $arrValues[] = $floatPercentage;
            }
            else {
                break;
            }
        }
        //add "others" part?
        if($floatPercentageSum < 99) {
            $arrKeyValues["others"] = 100-$floatPercentageSum;
            $arrLabels[] = "others";
            $arrValues[] =  100-$floatPercentageSum;
        }
        $objGraph = new class_graph_pchart();
        $objGraph->createPieChart($arrValues, $arrLabels);
        $strFilename = "/portal/pics/cache/stats_topsystem.png";
        $objGraph->saveGraph($strFilename);
		$arrReturn[] = _webpath_.$strFilename;

		//--- XY-Plot -----------------------------------------------------------------------------------
		//calc number of plots
		$arrPlots = array();
		$arrTickLabels = array();
		foreach($arrKeyValues as $strSystem => $arrData) {
		    if($strSystem != "others")
    		    $arrPlots[$strSystem] = array();
		}

		$intGlobalEnd = $this->intDateEnd;
		$intGlobalStart = $this->intDateStart;

		$this->intDateEnd = $this->intDateStart + 60*60*24*$this->intInterval;

		$intCount = 0;
		while($this->intDateStart <= $intGlobalEnd) {
		    $arrSystemData = $this->getTopSystem();
		    //init plot array for this period
		    $arrTickLabels[$intCount] = date("d.m.", $this->intDateStart);
		    foreach($arrPlots as $strSystem => &$arrOnePlot) {
		        $arrOnePlot[$intCount] = 0;
		        if(key_exists($strSystem, $arrSystemData)) {
		            $arrOnePlot[$intCount] = (int)$arrSystemData[$strSystem];
		        }

		    }
		    //increase start & end-date
		    $this->intDateStart = $this->intDateEnd;
		    $this->intDateEnd = $this->intDateStart + 60*60*24*$this->intInterval;
		    $intCount++;
		}
		//create graph


		//fehler fangen: mind. 2 datumswerte
		if(count($arrTickLabels) > 1 && count($arrPlots) > 0) {
    		$objGraph = new class_graph_pchart();
    		
    		foreach($arrPlots as $arrPlotName => $arrPlotData) {
    		    $objGraph->addLinePlot($arrPlotData, $arrPlotName);
    		}
            $objGraph->setArrXAxisTickLabels($arrTickLabels);
    		$strFilename = "/portal/pics/cache/stats_topsystem_plot.png";
            $objGraph->saveGraph($strFilename);
    		$arrReturn[] = _webpath_.$strFilename;
		}

		//reset global dates
		$this->intDateEnd = $intGlobalEnd;
		$this->intDateStart = $intGlobalStart;

		return $arrReturn;
	}
}
?>