<?php
include_once("library/functions.php");

$allowedExts = array("txt", "log", "csv");
$maxEntries = 20000;

$response = array();

if (!$_FILES)
	echo("Must upload a file");
else{
    $tmp = explode(".", $_FILES["file"]["name"]);
	$extension = end($tmp);
	if (($_FILES["file"]["type"] == "text/plain" || $_FILES["file"]["type"] == "text/csv")
		&& ($_FILES["file"]["size"] < 20000)
		&& in_array($extension, $allowedExts))
	{
	  if ($_FILES["file"]["error"] > 0)
	    {
	    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	    }
	  else{
//	    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
          $response["file"] = $_FILES["file"]["name"];
//	    echo "Type: " . $_FILES["file"]["type"] . "<br>";
	    //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
	    //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
	    
	    $localfile = "upload/" . $_FILES["file"]["name"] . date("Ymd_H_m_s");
	    if (file_exists($localfile))
	      {
//	      echo $_FILES["file"]["name"] . " already exists. ";
            $response["status"] = "error";
            $response["error"] = "File Already exists";
            http_response_code(400);
	      }
	    else{  
	      $total_num = 0;
	      if(move_uploaded_file($_FILES["file"]["tmp_name"],$localfile)){
	      	//echo "Stored in: " . $localfile;
	      	$row = 1;
	      	if (($handle = fopen($localfile, "r")) !== FALSE) {
	      		$query = "INSERT INTO BATCH_PROP (prop,completed) VALUES ";
	      		while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
	      			$num = count($data);
	      			$total_num = $total_num + $num;
	      			//echo "<p> $num fields in line $row: <br /></p>\n";
	      			$row++;
	      			for ($c=0; $c < $num; $c++) {
	      				//echo $data[$c] . "<br />\n";
	      				$query = $query . "('" . $data[$c] . "','false'),";
	      			}
	      		}
                error_log("debug: upload_file: ".$total_num." properties found : ");
//	      		echo "<p> $total_num properties found <br /></p>\n";
	      		$response["idsFound"] = $total_num;
	      		$query = trim($query,",");
                $query = $query . "on duplicate key UPDATE completed= 'false', pdfs='', prop_mktval='', Median_Sale5='', Median_Sale10='', Median_Sale15='', Median_Eq11='' ";
	      		//echo $query . "<br />";
                error_log("debug: upload_file: query : ". $query);
	      		fclose($handle);
	      		if(doSqlQuery($query)) {
                    $response["status"] = "success";
//	      			echo "Successfully inserted into table<br />";
                }
	      		else {
//	      			echo "Insertion error<br />";
                    $response["status"] = "error";
                    $response["error"] = "Insertion error";
                }
	      	}	
	      }
	      //Execute the batch pdf
	      if($production==false)
	      	$phpCmd = "/Applications/MAMP/bin/php/php5.4.34/bin/php ";
	      else
	      	$phpCmd = "php-cli ";
	      $filename = "./cli/BatchPDF.php";
	      $output = shell_exec("$phpCmd $filename >error_log 2>&1 &");
	    }
	  }
	}
	else{
//	  echo "Invalid file<br>";
        $response["status"] = "error";
        $response["error"] = "Invalid File";
//	  echo $_FILES["file"]["type"];
	  if ($_FILES["file"]["size"] > $maxEntries)
//	  	echo "File exceeds maximum number of bytes". $maxEntries . ".  size:". $_FILES["file"]["size"];
	      $response["error"]="File size exceeded";
	  if (!in_array($extension, $allowedExts))
//	  	echo "Wrong file type. Must be txt, log, or csv";
	      $response["error"] = "Wrong file type";
	}
}
//echo '<br><br><A HREF="batch_complete.php">Back</A>';
echo json_encode($response);
?>