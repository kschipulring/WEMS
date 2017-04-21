<?php 


function uploadFile($eventID, $lConponent){
    require '../wemsDatabase.php';

    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');



    for($i=0; $i<count($_FILES['fileToUpload']['name']); $i++) {
        
                    $target_file = "/www/cmc/documents/".basename($_FILES["fileToUpload"]["name"][$i]);
        
                    $uploadOk = 1;
                    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        
                    if ($uploadOk == 0){
                        $locationErrMsg = "Sorry, your file was not uploaded.";
                        //if everything is ok, try to upload file
                    }else {
        
                        // echo "<pre>";
                        //print_r($_FILES);
        
                        if(!empty($_FILES['fileToUpload']["name"][$i]))
                        {
        
                            //echo "<pre>";
                            //print_r($_FILES);
        
                            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file))
                            {
                                $image = file_get_contents($target_file);
        
                        //$contents = file_get_contents($target_file);
                        //  echo $contents;
        
                        $fileName = basename( $_FILES["fileToUpload"]["name"][$i]);
        
                        $sql = oci_parse($c, "INSERT INTO WEMS_LOCDOCS (EVENTID, MARKERID, BLOB_COL, ID) VALUES(:EVENTID, :CTID, empty_blob(), :ID) RETURNING BLOB_COL INTO :BLOB_COL");
        
                        oci_bind_by_name($sql, ":EVENTID", $eventID, -1);
                        oci_bind_by_name($sql, ":CTID", $lConponent, -1);
                        oci_bind_by_name($sql, ":ID", $fileName, -1);
                        	
        
                        $blob = oci_new_descriptor($c, OCI_D_LOB);
        
                        oci_bind_by_name($sql, ":BLOB_COL", $blob, -1, OCI_B_BLOB);
        
                        oci_execute($sql, OCI_DEFAULT) or die ("Unable to execute query");
        
                        // $blob->write($image);
        
                        if(!$blob->save($image))
                        {
                            oci_rollback($c);
                        }
                        else
                        {
                            oci_commit($c);
                        }
        
                        oci_free_statement($sql);
                        $blob->free();
        
        
                        echo "<br> The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded to Oracle.";
                    }
        
                    else
                    {
                        $errMsg = "Sorry, there was an error uploading your file.";
                        print_r(error_get_last());
                    }
        
        
                         }//if(!empty($_FILES['fileToUpload']["name"][$i]))

                 }//for($i=0; $i<count($_FILES['fileToUpload']['name']); $i++)
                }
}//end Function            
 ?>             
                