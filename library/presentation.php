<?php


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


function createEQTable($comparray){
	global $fieldsofinteresteq,$isEquityComp;
	$isEquityComp = true;
	
	echo '<H2>Comp Equity Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($fieldsofinteresteq); $i++)
	{
		echo "\t<tr>".PHP_EOL;
		for($j=0; $j <= count($comparray); $j++)
		{
			if($j == 0)
				$data = null;
			elseif($j == 1)
				$data = $_SESSION['subjsess'];
			else
				$data = $_SESSION['comp'.$comparray[$j-2]];
				
			//var_dump($data);
			if($i==0) //Header Row
			{
				if($j==0)//leave first cell empty
					echo "\t\t<td></td>";
				else
				{
					if($j==1)
						echo "\t\t<th class='colhead'>Subject</th>".PHP_EOL;
					else
						echo "\t\t<th class='colhead'>Comp ".($comparray[$j-2])."</th>".PHP_EOL;
				}
			}
			else
			{
				if($j==0) //Description column
				{
					echo "\t\t<th>".$fieldsofinteresteq[$i-1][0]."</th>".PHP_EOL;
				}
				else
				{
					$currval = $data->getFieldByName($fieldsofinteresteq[$i-1][0]);
					if($currval == NULL)
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
						$class = $fieldsofinteresteq[$i-1][0];
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
		
	}
	echo "</table>";
	$isEquityComp = false;
}

function createEQTable2($subjcomparray){
	global $fieldsofinteresteq,$isEquityComp;
	$isEquityComp = true;
	emitHTMLHeader();
	echo '<div class="page">';
	echo '<H2>Comp Equity Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($fieldsofinteresteq); $i++)
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
					echo "\t\t<th>".$fieldsofinteresteq[$i-1][0]."</th>".PHP_EOL;
				}
				//Check for the GlobalCAlculated fields in the subj column
				if($j ==0 && strcmp($fieldsofinteresteq[$i-1][1],'GLOBALCALCULATED') == 0)
					{   
						$currval = $_SESSION[$fieldsofinteresteq[$i-1][0]];
					}
				else
					$currval = $data->getFieldByName($fieldsofinteresteq[$i-1][0]);
					
				if($currval == NULL)
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
					$class = $fieldsofinteresteq[$i-1][0];
					$trimmedclass = str_replace(" ","",$class);
					
					echo "\t\t<td><div class='".$trimmedclass."' >".$currval."</div>";
					
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
	emitHTMLFooter();
	$isEquityComp = false;
}

function createEQTableCSV($subjcomparray){
	global $fieldsofinteresteq,$isEquityComp;
	$isEquityComp = true;
	cvsHeader();
	echo 'Comp Equity Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').PHP_EOL;
	for($i=0; $i <= count($fieldsofinteresteq); $i++)
	{
		for($j=0; $j < count($subjcomparray); $j++)
		{
			$data = $subjcomparray[$j];
				
			//var_dump($data);
			if($i==0) //Header Row
			{
				if($j==0)
					echo ";Subject";
				else
					echo ";Comp ".($j);
			}
			else
			{
				if($j==0) //Description column
					echo $fieldsofinteresteq[$i-1][0];
				
				$currval = $data->getFieldByName($fieldsofinteresteq[$i-1][0]);
				if($currval == NULL)
				{
					echo ";";
					continue;
				}
				// ADD BACK IF YOU DON"T WANT '0' in left half of column
				elseif(is_numeric ($currval) && $currval == 0)
				{
					echo ";";
					continue;
				}
				else
				{
					$class = $fieldsofinteresteq[$i-1][0];
					$trimmedclass = str_replace(" ","",$class);
					
					echo ";".$currval;
					
					//Only display delta's for comps
					if($j > 0 && ($delta = hasDelta($class)) != NULL){
						$currval = $data->getFieldByName($delta);
						if(is_numeric ($currval) && $currval == 0)
						
							echo "()";
						else
							echo "(".$currval.")";
					}
				}
			}		
		}
		echo PHP_EOL;
	}
	$isEquityComp = false;
}

function createSalesTable2($subjcomparray){
	global $fieldsofinterest,$isEquityComp;
	$isEquityComp = true;
	emitHTMLHeader();
	echo '<div class="page">';
	echo '<H2>Comp Sales Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($fieldsofinterest); $i++)
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
					echo "\t\t<th>".$fieldsofinterest[$i-1][0]."</th>".PHP_EOL;
				}
				//Check for the GlobalCAlculated fields in the subj column
				if($j ==0 && strcmp($fieldsofinterest[$i-1][1],'GLOBALCALCULATED') == 0)
				{
					$currval = $_SESSION[$fieldsofinterest[$i-1][0]];
				}
				else
					$currval = $data->getFieldByName($fieldsofinterest[$i-1][0]);
					
				if($currval == NULL)
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
					$class = $fieldsofinterest[$i-1][0];
					$trimmedclass = str_replace(" ","",$class);
						
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
	echo "</table>";
	emitHTMLFooter();
}

function returnNoHits($propid){
	emitHTMLHeader();
	echo '<div class="nohitpage">';
	echo '<H2>No Comparable Hits found for ' . $propid . '</H2>';
	emitHTMLFooter();
}

function createGenericTable($subjcomparray,$isEquityComp){
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
					echo "\t\t<th>".$relaventfields[$i-1][0]."</th>".PHP_EOL;
				}
				//Check for the GlobalCAlculated fields in the subj column
				if($j ==0 && strcmp($relaventfields[$i-1][1],'GLOBALCALCULATED') == 0)
				{
					$currval = $_SESSION[$relaventfields[$i-1][0]];
				}
				else
					$currval = $data->getFieldByName($relaventfields[$i-1][0]);
					
				if($currval == NULL)
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
					$class = $relaventfields[$i-1][0];
					$trimmedclass = str_replace(" ","",$class);

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
	echo "</table>";
	emitHTMLFooter();
}
function createSalesTable($comparray){
	global $fieldsofinterest,$SEGMENTSADJ;
	echo '<div class="page">';
	echo '<H2>Comp Sales Grid - Tax Tiger - '.date('l jS \of F Y h:i:s A').'</H2>'.PHP_EOL;
	echo '<table>'.PHP_EOL;
	for($i=0; $i <= count($fieldsofinterest); $i++)
	{
		echo "\t<tr>".PHP_EOL;
		for($j=0; $j <= count($comparray); $j++)
		{
			if($j == 0)
				$data = null;
			elseif($j == 1)
				$data = $_SESSION['subjsess'];
			else
				$data = $_SESSION['comp'.$comparray[$j-2]];
				
			//var_dump($data);
			if($i==0) //Header Row
			{
				if($j==0)//leave first cell empty
					echo "\t\t<td></td>";
				else
				{
					if($j==1)
						echo "\t\t<th class='colhead'>Subject</th>".PHP_EOL;
					else
						echo "\t\t<th class='colhead'>Comp ".($comparray[$j-2])."</th>".PHP_EOL;
				}
			}
			else
			{
				if($j==0) //Description column
				{
					echo "\t\t<th>".$fieldsofinterest[$i-1][0]."</th>".PHP_EOL;
				}
				else
				{
					$class = $fieldsofinterest[$i-1][0];
					$currval = $data->getFieldByName($class);
					if($currval == NULL)
					{
						echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
						continue;
					}
					// ADD BACK IF YOU DON"T WANT '0' in left half of column
					elseif(is_numeric ($currval) && $currval == 0 && $class != $SEGMENTSADJ[0])
					{
						echo "\t\t<td class='unknown'>&nbsp</td>".PHP_EOL;
						continue;
					}
					else
					{
						echo "\t\t<td>";
						$trimmedclass = str_replace(" ","",$class);
						echo "<div class='".$trimmedclass."' >".$currval."</div>";
						if($j > 1 && ($delta = hasDelta($class)) != NULL){
							$currval = $data->getFieldByName($delta);
							if(is_numeric ($currval) && $currval == 0)			
							{			
								echo "&nbsp".PHP_EOL;
							}
							else
							{
								echo "<div class='".$delta."' >".$currval."</div>";
							}
						}
						echo "</td>".PHP_EOL;
					}
				}
			}
		}
		
	}
	echo "</table>";
	echo '</div>'; // Page Div
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
					if($currval == NULL)
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
					
				if($currval == NULL)
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
?>