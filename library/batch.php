<?php
include_once 'propertyClass.php';
include_once 'functions.php';

//$debug = true;
$TRIMINDICATED = true;
$COMPSTODISPLAY = 100;

//$input = array("129972","708686");

	/**
	 * Process Array of Strings representing property ids
	 * @param String[] $queueIn
	 */
	function processArray($queueIn){		
		foreach($queueIn as $propid){	
			processSingleton($propid);	
		}
	}
	

	/**
	 * For a given property ID it will find the best comps with at least 2 comps up to max
	 * 
	 * @param String Property_ID
	 * @param (optional) Boolean isEquityComp (default=true)
	 * @param (optional) Int maximum number of comps (default=10)
	 * @param (optional) Boolean Trim to only comps < subj value (default=true)
	 * @return Array with 'mean_val', 'market_val', and 'comps'
	 */
	function processSingleton($propIdIn,$isEquityComp=true,$maxComps=10,$trim=true){
		global $INDICATEDVAL,$TABLE_SALES,$debug;
		$result = array();
		
		if($debug) echo("Processing prop: " . $propIdIn);
		$property = getProperty($propIdIn);

		//Quick filter		
		if($property->mImprovCount==null || $property->mImprovCount==0){
			error_log("No Improvements for ". $propIdIn. " so throwing out");
			return;
		}
		
		$result["prop_owner"] = $property->mOwner;
		$result["prop_addr"] = $property->mSitus;
		
		$property->setisSubj(true);
		$compsarray = findBestComps($property,$isEquityComp,75,$trim);
		
		if(sizeof($compsarray) == 0){
			//error_log("No Hits for " . $propIdIn);
			if($debug) echo("No Hits for " . $propIdIn);
			return;
		}
		if(sizeof($compsarray) == 1){
			//error_log("Only 1 hit for " . $propIdIn);
			if($debug) echo("Only 1 hit for " . $propIdIn);
			return;
		}
		
		
		usort($compsarray,"cmpProp");
		
		$comp_min = MIN($maxComps,count($compsarray));
		$subjcomparray = array();
		$subjcomparray[0] = $property;
		
		for($i=0; $i < $comp_min; $i++){
			$subjcomparray[$i+1] = $compsarray[$i];
		}
		
		$meanVal = getMeanVal($subjcomparray);
		$meanValSqft = getMeanValSqft($subjcomparray);
		$medianVal = getMedianVal($subjcomparray);
		$medianValSqft = getMedianValSqft($subjcomparray);
		
		$result["mean_val"] = getMeanVal($subjcomparray);
		$result["market_val"] = $property->mMarketVal;
		
		$compCSV = "";
		foreach($compsarray as $comp){
			$compCSV = $compCSV . $comp->mPropID . ',';
		}
		$result["comps"] = $compCSV;
		
		return $result;
		//putPropHistory($propIdIn,getMeanVal($subjcomparray),$property->getFieldByName($INDICATEDVAL[0]),$property->mNeighborhood);
		//if($debug) echo("inserted " . $propIdIn);
	}

	/** 
	 * Generates a single pdf page passed on passed in html
	 * @param unknown_type $html
	 * @param unknown_type $title
	 */
	function generatePDF($html,$title="property"){
		include("MPDF56/mpdf.php");
		
		$mpdf=new mPDF('c','A4-L',"","","10","10","10","10");
		$mpdf->SetDisplayMode('fullpage');
		
		$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
		
		// LOAD a stylesheet
		$stylesheet = file_get_contents('default_pdf.css');
		$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
		
		$mpdf->WriteHTML($html,2);
		
		$mpdf->Output(title.'.pdf','I');
		exit;
	}
?>