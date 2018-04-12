<?php
include_once("library/functions.php");

$allowedExts = array("txt", "log", "csv");
$maxEntries = 200000;
$maxFileSize = 200000;

$response = array();

if (!$_FILES)
    echo("Must upload a file");
else{
    error_log("Processing uploaded file");
    $tmp = explode(".", $_FILES["file"]["name"]);
    $extension = end($tmp);
    if (($_FILES["file"]["type"] == "text/plain" || $_FILES["file"]["type"] == "text/csv")
        && ($_FILES["file"]["size"] < $maxFileSize)
        && in_array($extension, $allowedExts))
    {
        if ($_FILES["file"]["error"] > 0)
        {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
        }
        else{
            $response["file"] = $_FILES["file"]["name"];

            $localfile = "upload/" . $_FILES["file"]["name"] . date("Ymd_H_m_s");
            if (file_exists($localfile))
            {
                $response["status"] = "error";
                $response["error"] = "File Already exists";
                http_response_code(400);
            }
            else{
                $total_num = 0;
                if(move_uploaded_file($_FILES["file"]["tmp_name"],$localfile)){
                    $row = 1;
                    if (($handle = fopen($localfile, "r")) !== FALSE) {
                        $query = "INSERT INTO BATCH_PROP (prop,completed) VALUES ";
                        while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                            $num = count($data);
                            $row++;
                            for ($c=0; $c < $num; $c++) {
                                if(is_numeric($data[$c])) {
                                    $total_num = $total_num + 1;
                                    $query = $query . "('" . $data[$c] . "','false'),";
                                }
                            }
                        }
                        error_log("TRACE>>> upload_file: ".$total_num." properties found");
                        $response["idsFound"] = $total_num;
                        $query = trim($query,",");
                        $query = $query . "on duplicate key UPDATE completed= 'false', pdfs='', prop_mktval='', Median_Sale5='', Median_Sale10='', Median_Sale15='', Median_Eq11='' ";
                        //echo $query . "<br />";
                        error_log("debug: upload_file: query : ". $query);
                        fclose($handle);
                        if(doSqlQuery($query)) {
                            $response["status"] = "success";
                        }
                        else {
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
        $response["status"] = "error";
        $response["error"] = "Invalid File";
        if ($_FILES["file"]["size"] > $maxEntries)
            $response["error"]="File size exceeded";
        if (!in_array($extension, $allowedExts))
            $response["error"] = "Wrong file type";
    }
}
//echo '<br><br><A HREF="batch_complete.php">Back</A>';
if($response["status"] == "error") {
    error_log("ERROR processing uploaded file: " . $response["error"]);
}
echo json_encode($response);
?>