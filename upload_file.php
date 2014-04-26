<?php
include_once("library/functions.php");

$allowedExts = array("txt", "log", "csv");
if (!$_FILES)
	echo("Must upload a file");
else{
	$extension = end(explode(".", $_FILES["file"]["name"]));
	if (($_FILES["file"]["type"] == "text/plain" || $_FILES["file"]["type"] == "text/csv")
		&& ($_FILES["file"]["size"] < 20000)
		&& in_array($extension, $allowedExts))
	{
	  if ($_FILES["file"]["error"] > 0)
	    {
	    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	    }
	  else{
	    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
	    echo "Type: " . $_FILES["file"]["type"] . "<br>";
	    //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
	    //echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
	    
	    $localfile = "upload/" . $_FILES["file"]["name"] . date("Ymd_H_m_s");
	    if (file_exists($localfile))
	      {
	      echo $_FILES["file"]["name"] . " already exists. ";
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
	      		echo "<p> $total_num properties found <br /></p>\n";
	      		$query = trim($query,",");
	      		//echo $query . "<br />";
	      		fclose($handle);
	      		if(executeQuery($query))
	      			echo "Successfully inserted into table<br />";
	      		else
	      			echo "Insertion error<br />";
	      	}	
	      }
	      //Execute the batch pdf
	      if($production==false)
	      	$phpCmd = "/Applications/MAMP/bin/php/php5.2.17/bin/php ";
	      else
	      	$phpCmd = "php-cli ";
	      $filename = "./cli/batch_pdf.php";
	      $output = shell_exec("$phpCmd $filename >error_log 2>&1 &");
	    }
	  }
	}
	else{
	  echo "Invalid file<br>";
	  echo $_FILES["file"]["type"];
	  if ($_FILES["file"]["size"] > 20000)
	  	echo "File to large: ". $_FILES["file"]["size"];
	  if (!in_array($extension, $allowedExts))
	  	echo "Wrong file type. Must be txt, log, or csv";
	}
}
echo '<br><br><A HREF="batch_complete.php">Back</A>';
?>