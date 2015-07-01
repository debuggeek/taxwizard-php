<?php
include_once("functions.php");
include_once("batch.php");

$DEBUG_PROSPECTS=false;
$INSERTS=0;

function getTotalHoodCount(){
	sqldbconnect();
	$query = "SELECT DISTINCT hood_cd FROM PROP";
	$result=mysqli_query($query);
	$num=mysqli_num_rows($result);

	return $num;
}

function getProspects(){
	global $MAX_SALESPERHOOD,$MIN_SALESPERHOOD,$INSERTS;

	if(setupProspectView()){
		$hoodList = getProspectHoods($MAX_SALESPERHOOD,$MIN_SALESPERHOOD);
		echo "Found ". count($hoodList) . " hoods with less then " . $MAX_SALESPERHOOD .
				" sales and more then ".$MIN_SALESPERHOOD."<br>";
		
		$propList = getProspectsFromHoodList($hoodList);
		echo "Successfully inserted ". $INSERTS . " properties within these hoods<br>";
		echo "Found ". count($propList) . " properties within hoodList<br>";
	}
	else{
		echo "ERROR Creating view for prospect<br>";
	}

	if(!removeProspectView())
		echo "ERROR removing prospect view<br>";
}

/**
 * Creates view used for prospect mining
 * @return True if created
 */
function setupProspectView(){
	$year = date("Y");
	$lastyear = $year -1;
	
	sqldbconnect();
	$query = "CREATE VIEW PROSPECT AS
				SELECT PROP.prop_id,hood_cd,sale_price,sale_date
				FROM PROP
				INNER JOIN SPECIAL_SALE_EX_CONF
				ON PROP.prop_id=SPECIAL_SALE_EX_CONF.prop_id
				WHERE sale_price > 0
				AND (`sale_date` LIKE  '%".$year."%' OR `sale_date` LIKE  '%".$lastyear."%')";

	$result=mysqli_query($query);
	mysqli_close();
	return $result;
}

/**
 * Removes view used for prospect mining
 * @return True if removed
 */
function removeProspectView(){
	sqldbconnect();
	$query = "DROP VIEW PROSPECT";

	$result=mysqli_query($query);
	mysqli_close();
	return $result;
}

/**
 * Inserts property in to PROSPECT_LIST
 * @return True if created
 */
function insertProspectTable($prop_id){
	sqldbconnect();
	$query = "INSERT INTO PROSPECT_LIST (prop_id) VALUES (". $prop_id.")";
	$result=mysqli_query($query);
	mysqli_close();

	return $result;
}

/**
 * Retreive an array of hoods
 */
function getProspectHoods($maxCount,$minCount){
	global $DEBUG_PROSPECTS;

	$result = array();
	
	$query = "SELECT DISTINCT hood_cd
				FROM PROSPECT
				GROUP BY hood_cd
				HAVING COUNT(prop_id)<=".$maxCount.
				" AND COUNT(prop_id)>=".$minCount;
	
	if($DEBUG_PROSPECTS) echo("getProspectsFromHoodList>>".$query."<br>");

	$sqlResult=doSqlQuery($query);

	if(!$sqlResult)
		return null;

	while($row = mysqli_fetch_array($sqlResult))
	{
		$result[]=$row['hood_cd'];
	}
	return $result;
}

/**
 *
 * @param String hood_id
 * @return array of property ids
 */
function getProspectsFromHood($hood_in){
	$result = array();

	$query = "SELECT prop_id
				FROM PROP
				WHERE hood_cd='".$hood_in."'";
	$sqlResult=doSqlQuery($query);

	if(!$sqlResult)
		return null;

	while($row = mysqli_fetch_array($sqlResult))
	{
		$result[]=$row['prop_id'];
	}
	return $result;
}

/**
 *
 * @param String hood_id
 * @return array of property ids
 */
function getProspectsFromHoodList($hoodList_in){
	global $DEBUG_PROSPECTS,$INSERTS;

	$result = array();

	$where = " WHERE ";
	foreach($hoodList_in as $hood){
		$where = $where." hood_cd='".trim($hood)."' OR ";
	}
	$where = substr($where,0,strlen($where)-4);
	$query = "SELECT prop_id FROM PROP ".$where;

	if($DEBUG_PROSPECTS) echo("getProspectsFromHoodList>>".$query."<br>");

	$sqlResult=doSqlQuery($query);
	if(!$sqlResult)
		return "No Value Found!";

	while($row = mysqli_fetch_array($sqlResult))
	{
		$result[] = $row['prop_id'];
		if(insertProspectTable($row['prop_id']))
			$INSERTS = $INSERTS + 1;
	}
	return $result;
}

/**
 * @return Array with 'total' entries in lookup table and 'remaining' entries within table not looked up
 */
function getLookupStats(){
	$result = array();

	$query = "SELECT COUNT(prop_id)
				FROM PROSPECT_LIST
				WHERE market_val=0";
	$sqlResult = doSqlQuery($query);
	$row = mysqli_fetch_array($sqlResult);

	$result['remaining'] = $row['COUNT(prop_id)'];

	$query2 = "SELECT COUNT(prop_id)
				FROM PROSPECT_LIST";
	$sqlResult = doSqlQuery($query2);
	$row = mysqli_fetch_array($sqlResult);
	$result['total'] = $row['COUNT(prop_id)'];

	return $result;
}

/**
 * @param (Optional) Number of entries to process (default=null unlimited)
 * @return number of entries lookedup
 */
function processLookups($numToProcess=null,$start=0){
	global $DEBUG_PROSPECTS;
	$result = 0;

	$query = "SELECT prop_id
				FROM PROSPECT_LIST
				WHERE market_val=0";
	if ($numToProcess != null)
		$query = $query. " LIMIT ". $start .",".$numToProcess;
	
	$sqlResult = doSqlQuery($query);
	while($row = mysqli_fetch_array($sqlResult)){
			$singResult = processSingleton($row['prop_id'],false,4,true);
			if($DEBUG_PROSPECTS) {
				if($singResult['comps'] == "")
					echo "No sales comps found for ". $row['prop_id']."<br>";
				else
					echo $row['prop_id'] . " singResult>>".$singResult['market_val']." | ".$singResult['mean_val']." | ".$singResult['comps']."<br>";
			}
			if($singResult['comps'] == ""){
				if(moveToNone($row['prop_id']))
					$result++;
			}else{
				if(updateProspect($row['prop_id'],$singResult))
					$result++;
			}			
	}
	return $result;
}

/**
 * 
 * @param Int $propID
 * @return boolean true if successful, false otherwise
 */
function moveToNone($propID){
	global $DEBUG_PROSPECTS;
	
	$query = "INSERT PROSPECT_LIST_NONE (prop_id) VALUES (".$propID.");";
	if(doSqlQuery($query)){
			$query2 = "DELETE FROM PROSPECT_LIST WHERE prop_id=".$propID;
			if(!doSqlQuery($query2)){
				$msg = "Unable to remove ".$propID." from table PROSPECT_LIST:".mysqli_error();
				error_log($msg);
				if($DEBUG_PROSPECTS) echo $msg . "<br>";
				return false;
			}
	}else{
		$msg = "Unable to insert ".$propID." into table PROSPECT_LIST_NONE:".mysqli_error();
		error_log($msg);
		if($DEBUG_PROSPECTS) echo $msg . "<br>";
		return false;
	}
	return true;
}

function updateProspect($prop_id,$singletonResult){
	global $TABLE_PROSPECT_LIST,$DEBUG_PROSPECTS;
	
	$mktValInt = (int)str_replace(array(' ', ','), '', $singletonResult['market_val'] );
	$meanValInt = (int)str_replace(array(' ', ','), '', $singletonResult['mean_val']);
	$diff = ($meanValInt - $mktValInt) / $mktValInt;
	
	$query="UPDATE " . $TABLE_PROSPECT_LIST . "
			SET computed_date='" . date("Y-m-d H:i:s") . "',
					market_val=" . $mktValInt . ",
					mean_val=" . $meanValInt . ",
					comps_csv='" . $singletonResult['comps'] . "',
					prop_addr='".$singletonResult['prop_addr'] . "',
					prop_owner='".$singletonResult["prop_owner"] . "',
					diff=".$diff."
					WHERE prop_id=".$prop_id;
	
	echo $query;
	
	if(!doSqlQuery($query)){
		$msg = "Unable to update ".$prop_id." in table PROSPECT_LIST:".mysqli_error();
		error_log($msg);
		if($DEBUG_PROSPECTS) echo $msg . "<br>";
		return false;
	}
	return true;
}
?>