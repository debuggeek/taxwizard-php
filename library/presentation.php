<?php

include_once "HTMLTable.php";

function iterateProps($proparray){
	echo "<pre>";
	echo "PropID     Market Value     SQFT     Value/Sqft     Savings     Agent<br>".PHP_EOL;
	foreach($proparray as $prop){
		echo $prop->mPropID . "        ".$prop->mMarketVal."        ".$prop->mLivingArea."     ".number_format($prop->getMrktSqft(),2)."     ".number_format(savings($prop),2)."     ".$prop->getAgent()."<br>".PHP_EOL;
	}
	echo "</pre>";
}

function cvsHeader(){
	/*
	 *echo '<a target="_blank" href="<?php echo $PHP_SELF?>?path=<?php echo rawurlencode($path)?>&amp;download=<?php echo rawurlencode($files[$i]["name"]);?>"><img src="<?php echo getIcon("download")?>" alt="<?php echo translate("download");?>" title="<?php echo translate("download");?>" width="7" height="16" style="vertical-align:middle;"/></a>';
	*/
	header("Content-type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=yourfilename.csv"); 
	header("Pragma: no-cache"); 
	header("Expires: 0");
}

function generateLetter(){
	global $date,$owner,$owneraddr,$ownercityzip,$propid,$propaddr,$hoodPriceSqft,$propPriceSqft,$propSavings;
	echo "<HTML><BODY>";
	echo "
		".$date."<br>
		".$owner."<br>
		".$owneraddr."<br>
		".$ownercityzip." <br>
		  <br>
		RE: Property ID #".$propid." , ".$propaddr."<br>
		  <br>
		Dear Taxpayer: <br>
		  <br>
		Our company monitors the appraisal and sales information on all properties in Travis County.  The 2009 data shows that your property was over appraised, and therefore you were overtaxed: <br>";
	echo"<UL><b>";
	echo "<li> Your neighborhood's average price per square foot was $".$hoodPriceSqft.", where as your house was taxed at $".$propPriceSqft." per/sqft. </li> ";
	echo "<li> Had we protested the appraised value, and lowered the value to be in line with the neighborhood, you would have saved approximately $".$propSavings."</li>";
	echo "<li> For the property tax year 2009, we successfully lowered our client's appraised value by an average of 13%, saving our clients on average $1,216 and achieved a reduction in property taxes 100% of the time.  Contrast this with the average agent's success rate of only 44% and an average reduction in value of only 6%. </li>";
	echo "<li> We work for a percentage of the tax savings we achieve.  <u>If we are unable to reduce your appraised value, you will owe us nothing.</u>  There are no hidden fees or other charges. </li>";
	echo "</UL></b><br>";
	echo "Our company combines extensive knowledge of the Texas property code, the inner workings of the appraisal district, statistical analysis, and unparalleled creativity to successfully argue and achieve a reduction in property taxes.  Our proprietary methodology can show and prove your property's value is being inflated by the appraisal district.  There is no other company that comes close to our results.
		  <br>
		We believe we have the ability to successfully appeal the valuation of your property in the future.  If you want to stop being over taxed, and give less of your hard earned money to the government, call us for a free consultation regarding your property taxes. <br>
		  <br>
		Sincerely, <br>             
		  <br>
		  <br>
		  <br>
		John Paul Krueger <br>";
	echo "</BODY></HTML>";
}

function returnNoHits($propid){
	emitHTMLHeader();
	echo '<div class="nohitpage">';
	echo '<H2>No Comparable Hits found for ' . $propid . '</H2>';
	emitHTMLFooter();
}

/**
 * @Deprecated
 * @param $subjcomparray
 * @param $isEquityComp
 */
function createGenericTable($subjcomparray, $isEquityComp){
	global $fieldsofinterest,$fieldsofinteresteq;

	emitHTMLHeader();
	echo '<div class="page">';
	if($isEquityComp){
		echo '<H2>Comp Equity Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
		$relaventfields = $fieldsofinteresteq;
	}else{
		echo '<H2>Comp Sales Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
		$relaventfields = $fieldsofinterest;
	}

	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($relaventfields); $i++)
	{
		echo "\t<tr>".PHP_EOL;
		for($j=0; $j < count($subjcomparray); $j++)
		{
			$data = $subjcomparray[$j];
			//var_dump($data);
			if($i==0) //Header Row
			{
				if($j==0)//leave first cell empty
					echo "\t\t<td></td>\t\t<th class='colhead'>Subject</th>".PHP_EOL;
				else
					echo "\t\t<th class='colhead'>Comp ".$j."</th>".PHP_EOL;
			}
			else
			{
				if($j==0) {//Description column
					echo "\t\t<th>".$relaventfields[$i-1][1]."</th>".PHP_EOL;
				}
				//Check for the GlobalCAlculated fields in the subj column
				if($j ==0 && strcmp($relaventfields[$i-1][2],'GLOBALCALCULATED') == 0)
				{
					$currval = $_SESSION[$relaventfields[$i-1][1]];
				}
				else {
					$currval = $data->getFieldByName($relaventfields[$i - 1]["NAME"]);
				}
				if($currval === NULL)
				{
					echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
					continue;
				}
//				// ADD BACK IF YOU DON"T WANT '0' in left half of column
//				elseif(is_numeric ($currval) && $currval == 0)
//				{
//					echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
//					continue;
//				}
				else
				{
					$class = $relaventfields[$i-1]["NAME"];
					$trimmedclass = str_replace(" ","",$class);

					if(($multiRowField = hasMultiRow($class)) != NULL){
						outputMultiDataRows($multiRowField,$class, $trimmedclass,  $subjcomparray);
					} else {

						echo "\t\t<td><div class='".$trimmedclass."' >".$currval."</div>";

						if($j > 0 && ($delta = hasDelta($class)) != NULL){
							$currval = $data->getFieldByName($delta);
							if(is_numeric ($currval) && $currval == 0){
								echo "&nbsp".PHP_EOL;
							}
							else{
								echo "<div class='".$delta."' >".$currval."</div>";
							}
						}
						echo "</td>".PHP_EOL;
					}
				}
			}
		}
		echo "\t</tr>".PHP_EOL;
	}
	echo "</table>";
	emitHTMLFooter();
}

function outputMultiDataRows($multiRowField, $class, $trimmedclass, $subjcomparray)
{
	global $segmentsadjMultiRow;

	if($multiRowField == $segmentsadjMultiRow){
		outputSegAdj($subjcomparray);
		return;
	}

	$maxRows = 1;
	//Start the next row and skip the first column
	//When we are called we are in the Subj column
	for($row=0; $row < $maxRows; $row++){
		for($col=0; $col < count($subjcomparray); $col++){
			$property = $subjcomparray[$col];
			$dataArray = $property->getFieldByName($multiRowField);
			//echo var_dump($dataArray);
			if(count($dataArray) > $maxRows){
				$maxRows = count($dataArray) -1;
			}
			if($row >= count($dataArray)){
				//close and move to next column;
				echo "\t\t<td/>".PHP_EOL;
				continue;
			}
			$element = $dataArray[$row];
			echo "\t\t<td><div class='" . $trimmedclass . "'>" . $element->getDisplay() . "</div>";

			if (!$property->mSubj && ($delta = hasDelta($class)) != NULL) {
				$currval = $property->getFieldByName($delta);
				if (is_numeric($currval) && $currval == 0) {
					echo "&nbsp";
				} else {
					echo "<div class='" . $delta . "' >" . $currval . "</div>";
				}
			}
			echo "</td>" . PHP_EOL;
		}
		echo "\t</tr>" .PHP_EOL;
		echo "\t<tr>" . PHP_EOL;
		echo "\t\t<td class='unknown'>&nbsp</td>" . PHP_EOL;
	}
}

function outputSegAdj($subjcomparray){
	$maxRows = 1;
	//Start the next row and skip the first column
	//When we are called we are in the Subj column
	for($row=0; $row < $maxRows; $row++){
		for($col=0; $col < count($subjcomparray); $col++){
			/* @var $property propertyClass */
			$property = $subjcomparray[$col];
			$impDets = $property->getImpDets();
			//echo var_dump($dataArray);
			if(count($impDets) > $maxRows){
				$maxRows = count($impDets);
			}
			if($row >= count($impDets)){
				//close and move to next column;
				echo "\t\t<td/>".PHP_EOL;
				continue;
			}
			/* @var $impDetail ImprovementDetailClass */
			$impDetail = $impDets[$row];
			echo "\t\t<td><div class='Segments&Adj'>" . $impDetail->getDisplay() . "</div>";

			if (!$property->isSubj()) {
				$currval = $impDetail->getAdjustmentDelta();
				if (is_numeric($currval) && $currval == 0) {
					echo "&nbsp";
				} else {
					echo "<div class='SegAdjDelta'>" . $currval . "</div>";
				}
			}
			echo "</td>" . PHP_EOL;
		}
		echo "\t</tr>" .PHP_EOL;
		echo "\t<tr>" . PHP_EOL;
		echo "\t\t<td class='unknown'>&nbsp</td>" . PHP_EOL;
	}
}

function dumpProperties($proparray,$isEquity){
	global $fieldsofinterestprop,$isEquityComp;

	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($proparray); $i++)
	{
		echo "\t<tr>".PHP_EOL;
		if($i == 0) //Titles
		{
			for($j=0; $j < count($fieldsofinterestprop); $j++)
			{
				echo "\t\t<th>".$fieldsofinterestprop[$j][0]."</th>".PHP_EOL;
			}
		}
		else
		{
			$data = $proparray[$i-1];
			for($j=0; $j < count($fieldsofinterestprop); $j++)
			{
					$currval = $data->getFieldByName($fieldsofinterestprop[$j][0]);
					if($currval === NULL)
					{
						echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
						continue;
					}
					// ADD BACK IF YOU DON"T WANT '0' in left half of column
					elseif(is_numeric ($currval) && $currval == 0)
					{
						echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
						continue;
					}
					else
					{
						echo "\t\t<td>";
						$class = $fieldsofinterestprop[$j][0];
						$trimmedclass = str_replace(" ","",$class);
						echo "<div class='".$trimmedclass."' >".$currval."</div>";
						if($j > 1 && ($delta = hasDelta($class)) != NULL){
							$currval = $data->getFieldByName($delta);
							if(is_numeric ($currval) && $currval == 0)
							
								echo "&nbsp".PHP_EOL;
							else
								echo "<div class='".$delta."' >".$currval."</div>";
						}
						echo "</td>".PHP_EOL;
					}
			}
		}	
	}
	echo "</table>";
}

/**
 * Used to emit HTML for a printable page
 */
function emitHTMLHeader(){
	echo '<HTML>'.PHP_EOL;
	echo '<HEAD><link rel="stylesheet" type="text/css" href="default.css" /> '.PHP_EOL;
	echo '<STYLE>'.PHP_EOL;
	echo '@page { size 11in 8.5in; margin: 2cm }'.PHP_EOL;
	echo 'div.page { page-break-after: always }'.PHP_EOL;
	echo '</STYLE>'.PHP_EOL;
	echo '</HEAD>'.PHP_EOL;
	echo '<BODY>'.PHP_EOL;
}

function emitHTMLHeader2(){
	header( 'Content-type: text/html; charset=utf-8' );
	echo '<HTML>'.PHP_EOL;
	echo '<HEAD><link rel="stylesheet" type="text/css" href="proplist.css" /> '.PHP_EOL;
	echo '</HEAD>'.PHP_EOL;
	echo '<BODY>'.PHP_EOL;
}

function emitXML(propertyClass $propClasses){
	echo nl2br('<PROPERTIES>');
	
	for($i=0; $i < $propClasses.count(); $i++){
		if($i = 0)
			echo nl2br('<PROPTYPE>subject</PROPTYPE>');
		else
			echo nl2br('<PROPTYPE>');
	}
	
	echo nl2br('</PROPERTIES>');
}

function emitHTMLFooter(){
	echo '</BODY>'.PHP_EOL;
	echo '<HTML>';
}


/**
 * @param FullTable $fullTable
 * @param bool $isEquityComp
 * @return string
 * @internal param propertyclass[] $subjcomparray
 */
function generateJsonRows($fullTable, $isEquityComp = true){
	global $fieldsofinterest,$fieldsofinteresteq, $SEGMENTSADJ, $TCADSCORE;

	$debug = false;

	if($isEquityComp){
		$relaventfields = $fieldsofinteresteq;
	}else{
		$relaventfields = $fieldsofinterest;
	}

    /**
     * @var propertyClass[]
     */
	$subjcomparray = $fullTable->getSubjCompArray();

    if($subjcomparray === null || count($subjcomparray) == 0){
        error_log("Not generating json due to empty subjcompArray");
        return '{}';
    }
    
	$obj = new stdClass();
	$obj->isEquity=$isEquityComp;
	$obj->compCount = count($subjcomparray)-1;//Don't count the subj
	$obj->rows = array();

	$roundtwo = array();
	foreach($relaventfields as $field){
		if($field["TYPE"] == "GLOBALCALCULATED"){
			$roundtwo[] = $field;
		} else if($field["NAME"] !== $SEGMENTSADJ["NAME"]) {
		    if(isSkipable($field)){
		        if(!shouldDisplay($fullTable, $field)){
		            if($debug) error_log("Skipping display of ".$field["NAME"]);
		            continue;
                }
            }
			$currRow = array();
			$currRow['description'] = $field["STRING"];
			for ($i = 0; $i < count($subjcomparray); $i++) {
				/* @var propertyClass $prop */
				$prop = $subjcomparray[$i];
				$currCol = 'col' . ($i + 1);
				if (!hasDelta($field["NAME"])) {
					$currRow[$currCol] = $prop->getFieldByName($field["NAME"]);
				} else {
					$currRow[$currCol] = populateDeltaObj($prop, $field);
				}
			}
			$obj->rows[] = $currRow;
		} else {
		    // Special case
			// 2016 Dealing with segments and Adj as only case right now
			$obj->rows = array_merge($obj->rows, addPrimaryImprovements($subjcomparray, $field));
			//Add secondary improvements
			$obj->rows = array_merge($obj->rows, addSecondaryImprovements($subjcomparray));
		}
	}

	foreach($roundtwo as $field){
		$currRow = array();
		$currRow['description'] = $field["STRING"];
		for ($i = 0; $i < count($subjcomparray); $i++) {
			$currCol = 'col' . ($i + 1);
			if($currCol == 'col1') {
				//Always only go in first col
				$funcName = $field['KEY'];
				$currRow[$currCol] = $fullTable->$funcName();
			} else {
				$currRow[$currCol] = null;
			}
		}
		$obj->rows[] = $currRow;
	}

	return json_encode($obj, JSON_PRETTY_PRINT);
}

/**
 * For a given array of properties in JSON format return an HTML table
 * @param FullTable $fullTable
 * @param Boolean - True if type = equity
 * @return HTML string of table
 */
function returnJsonBasedHTMLTable($fullTable, $isEquityComp){
	$jsonData = generateJsonRows($fullTable, $isEquityComp);
	$htmlTable = new HTMLTable();
	$htmlTable->parseJson($jsonData);
	return $htmlTable->toHTML($isEquityComp);
}

/**
 * @param propertyClass() $subjcomparray
 * @param $field
 * @return array()
 */
function addPrimaryImprovements($subjcomparray, $field){
	$resultRows = array();
	$maxOverallImp = getMaxPrimaryImpCount($subjcomparray);
	$ListofPropsImprv = array();
	for ($i = 0; $i < count($subjcomparray); $i++) {
		$ListofPropsImprv[$i] = ImpHelper::getPrimaryImprovements($subjcomparray[$i]->getImpDets());
	}
	$seenDetIds = array();
	for ($i = 0; $i < $maxOverallImp; $i++) {
		$currRow = array();
		if ($i == 0) {
			//only first row has description
			$currRow['description'] = $field[0];
		} else {
			$currRow['description'] = null;
		}
		//We start with the order in subject
		$subjImprovments = $ListofPropsImprv[0];
		if($i < count($subjImprovments)) {
			//While we have improvements that exist in subject
			$currImpDetCode = $subjImprovments[$i]->getImprvDetTypeCd();
			for ($j = 0; $j < count($ListofPropsImprv); $j++) {
				$currImpList = $ListofPropsImprv[$j];
				$currCol = 'col' . ($j + 1);
				$improvement = ImpHelper::getImprovObjByCode($currImpList, $currImpDetCode, $seenDetIds);
				$currRow[$currCol] = populateSegObj($improvement);
				if($improvement != null && $improvement->getImprvDetId() != null) {
					$seenDetIds[] = $improvement->getImprvDetId();
				}
			}
		} else {
			//Now we just need to get the rest of the improvements on the comps
			for ($j = 0; $j < count($ListofPropsImprv); $j++) {
				$currImpList = $ListofPropsImprv[$j];
				$currCol = 'col' . ($j + 1);
				if($j == 0){
					//we know the subject doesn't have this
					$currRow[$currCol] = populateSegObj(null);
				} else {
					$foundOne = false;
					foreach($currImpList as $improvDetail){
						/* @var ImprovementDetailClass $improvDetail */
						if(!in_array($improvDetail->getImprvDetId(), $seenDetIds)){
							$currRow[$currCol] = populateSegObj($improvDetail);
							$seenDetIds[] = $improvDetail->getImprvDetId();
							$foundOne = true;
							break;
						}
					}
					if(!$foundOne){
						// If we couldn't find one then it's an empty cell
						$currRow[$currCol] = null;
					}
				}
			}
		}
		$resultRows[] = $currRow;
	}
	return $resultRows;
}

/**
 * @param $subjcomparray
 * @return array()
 */
function addSecondaryImprovements($subjcomparray){
	$resultRows = array();
	$currRow = array();
	$currRow['description'] = "Secondary Imp";
	for($i=0; $i < count($subjcomparray); $i++){
		/* @var propertyClass $currProp */
		$currProp = $subjcomparray[$i];
		$currCol = 'col' . ($i + 1);
		$deltaObj = new stdClass();
		$deltaObj->value = $currProp->getSegAdj();
		$deltaObj->delta = $currProp->getSegAdjDelta();
		$currRow[$currCol] = $deltaObj;
	}
	$resultRows[] = $currRow;
	return $resultRows;
}

/**
 * @param ImprovementDetailClass $improvement
 * @return stdClass
 */
function populateSegObj($improvement){
	$obj = new stdClass();
	$obj->value = $improvement ? $improvement->getImprvDetTypeDesc() : null;
	$obj->subvalue = $improvement ? $improvement->getDetArea() : null;
	$obj->delta = $improvement ? $improvement->getAdjustmentDelta() : null;
	return $obj;
}

/**
 * @param propertyClass $prop
 * @param array $field
 * @return stdClass with value and delta populated
 */
function populateDeltaObj($prop, $field){
	$delta = hasDelta($field["NAME"]);
	$deltaObj = new stdClass();
	$deltaObj->value = $prop->getFieldByName($field["NAME"]);
	if(!$prop->isSubj()) {
	    // Don't populate deltas on the subject
        $deltaObj->delta = $prop->getFieldByName($delta);
    }
	return $deltaObj;
}

/**
 * @param propertyClass[] $subjCompArray
 */
function getMaxPrimaryImpCount($subjCompArray){
	$maxCount = 0;
	if(count($subjCompArray) > 0) {
		foreach ($subjCompArray as $prop) {
			/* @var propertyClass $prop */
			$currCount = count(ImpHelper::getPrimaryImprovements($prop->getImpDets()));
			if ($currCount > $maxCount) {
				$maxCount = $currCount;
			}

		}
	}
	return $maxCount;
}

/**
 * Returns the maximum number of secondary improvements across all properties
 * @param $subjcomparray
 * @return int
 */
function getMaxSecondaryImprovement($subjCompArray){
	$maxCount = 0;
	foreach($subjCompArray as $prop){
		/* @var propertyClass $prop */
		$currCount = count(ImpHelper::getSecondaryImprovements($prop->getImpDets()));
		if($currCount > $maxCount){
			$maxCount = $currCount;
		}

	}
	return $maxCount;
}

/**
 * For a given array of properties return an HTML table
 * @param Array of Properties (first item is subject)
 * @param Boolean - True if type = equity
 * @return HTML string of table
 */
function returnGenericTable($subjcomparray,$isEquityComp){
	global $fieldsofinterest,$fieldsofinteresteq;
	
	$returnHTML = '<HTML>'.PHP_EOL;
	$returnHTML = $returnHTML .  '<HEAD>'.PHP_EOL;
	$returnHTML = $returnHTML .  '</HEAD>'.PHP_EOL;
	$returnHTML = $returnHTML .  '<BODY>'.PHP_EOL;

	$returnHTML = $returnHTML .  '<div class="page">';
	if($isEquityComp){
		$returnHTML = $returnHTML .  '<H2>Comp Equity Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
		$relaventfields = $fieldsofinteresteq;
	}else{
		$returnHTML = $returnHTML .  '<H2>Comp Sales Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
		$relaventfields = $fieldsofinterest;
	}

	$returnHTML = $returnHTML .  '<table>'.PHP_EOL;
	for($i=0; $i <= count($relaventfields); $i++)
	{
		$returnHTML = $returnHTML .  "\t<tr>".PHP_EOL;
		for($j=0; $j < count($subjcomparray); $j++)
		{
			$data = $subjcomparray[$j];
			//var_dump($data);
			if($i==0) //Header Row
			{
				if($j==0)//leave first cell empty
					$returnHTML = $returnHTML .  "\t\t<td></td>\t\t<th class='colhead'>Subject</th>".PHP_EOL;
				else
					$returnHTML = $returnHTML .  "\t\t<th class='colhead'>Comp ".$j."</th>".PHP_EOL;
			}
			else
			{
				if($j==0) {//Description column
					$returnHTML = $returnHTML .  "\t\t<th>".$relaventfields[$i-1][0]."</th>".PHP_EOL;
				}
				//Check for the GlobalCAlculated fields in the subj column
				if($j ==0 && strcmp($relaventfields[$i-1][1],'GLOBALCALCULATED') == 0)
				{
					$currval = $_SESSION[$relaventfields[$i-1][0]];
				}
				else
					$currval = $data->getFieldByName($relaventfields[$i-1][0]);
					
				if($currval === NULL)
				{
					$returnHTML = $returnHTML .  "\t\t<td class='unknown'></td>".PHP_EOL;
					continue;
				}
				// ADD BACK IF YOU DON"T WANT '0' in left half of column
				elseif(is_numeric ($currval) && $currval == 0)
				{
					$returnHTML = $returnHTML .  "\t\t<td class='unknown'></td>".PHP_EOL;
					continue;
				}
				else
				{
					$class = $relaventfields[$i-1][0];
					$trimmedclass = str_replace(" ","",$class);

					$returnHTML = $returnHTML .  "\t\t<td><div class='".$trimmedclass."' >".$currval."</div>";

					if($j > 0 && ($delta = hasDelta($class)) != NULL){
						$currval = $data->getFieldByName($delta);
						if(is_numeric ($currval) && $currval == 0){
							$returnHTML = $returnHTML.PHP_EOL;
						}
						else{
							$returnHTML = $returnHTML .  "<div class='".$delta."' >".$currval."</div>";
						}
					}
					$returnHTML = $returnHTML .  "</td>".PHP_EOL;
				}
			}
		}

	}
	$returnHTML = $returnHTML .  "</table>";
	
	$returnHTML = $returnHTML .  '</BODY>'.PHP_EOL;
	$returnHTML = $returnHTML .  '<HTML>';
	
	return $returnHTML;
}


/**
 * @param mixed[] $fieldArray
 * @return bool
 */
function isSkipable($fieldArray) : bool{
    if($fieldArray != null) {
        if (in_array("SKIPABLE", $fieldArray)) {
            return $fieldArray["SKIPABLE"];
        }
    }
    return false;
}

/**
 * @param FullTable $fullTable
 * @param mixed[] $fieldArray
 * @return bool
 */
function shouldDisplay($fullTable, $fieldArray) : bool{
    global $TCADSCORE, $SALERATIO;

    switch($fieldArray["NAME"]){
        case $SALERATIO["NAME"]:
            return $fullTable->getShowSaleRatios();
        case $TCADSCORE["NAME"]:
            return $fullTable->getShowTcadScores();
        default:
            return false;
    }
}


?>