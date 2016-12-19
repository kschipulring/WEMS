<?php
  


	
	
  $task =  isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : false;
  
 require '../wemsDatabase.php';

  $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
		
  
	//********Approve	
	$taskNum = isset($_POST['taskNum'])  ? $_POST['taskNum'] : "";
	$assignedBy = isset($_POST['assignedBySelect'])  ? $_POST['assignedBySelect'] : "";
	$category = isset($_POST['categorySelect'])  ? $_POST['categorySelect'] : "";
	$requestDate = isset($_POST['requestDate'])  ? $_POST['requestDate'] : "";
	$issueDate = isset($_POST['issueDate'])  ? $_POST['issueDate'] : "";
	$projCompleteDate = isset($_POST['projCompleteDate'])  ? $_POST['projCompleteDate'] : "";
	$engineerAssignOne = isset($_POST['engineerAssignOne'])  ? $_POST['engineerAssignOne'] : "";
	$engineerAssignTwo = isset($_POST['engineerAssignTwo'])  ? $_POST['engineerAssignTwo'] : "";
	$engineerAssignThree = isset($_POST['engineerAssignThree'])  ? $_POST['engineerAssignThree'] : ""; 
	$engineerAssignFour = isset($_POST['engineerAssignFour'])  ? $_POST['engineerAssignFour'] : ""; 
	$referTN = isset($_POST['referTN'])  ? $_POST['referTN'] : ""; 
	$taskType = isset($_POST['taskType'])  ? $_POST['taskType'] : "";
	$supportDoc = "";
	$taskDesc = isset($_POST['taskDesc'])  ? $_POST['taskDesc'] : "";
	$system = isset($_POST['system'])  ? $_POST['system'] : "";
	$subSys = isset($_POST['subSys'])  ? $_POST['subSys'] : "";
	$priority = isset($_POST['priority'])  ? $_POST['priority'] : "";
	$status = isset($_POST['status'])  ? $_POST['status'] : "";
	$closeDate = isset($_POST['closeDate'])  ? $_POST['closeDate'] : "";
	$requestedBy = isset($_POST['requestedBy'])  ? $_POST['requestedBy'] : "";
	$equipmentTypeOne = isset($_POST['equipmentTypeOne'])  ? $_POST['equipmentTypeOne'] : "";
	$equipmentTypeTwo = isset($_POST['equipmentTypeTwo'])  ? $_POST['equipmentTypeTwo'] : "";
	$equipmentTypeThree = isset($_POST['equipmentTypeThree'])  ? $_POST['equipmentTypeThree'] : "";
	$shopAffectedOne = isset($_POST['shopAffectedOne'])  ? $_POST['shopAffectedOne'] : "";
	$shopAffectedTwo = isset($_POST['shopAffectedTwo'])  ? $_POST['shopAffectedTwo'] : "";
	$aINumOne = isset($_POST['aINumOne'])  ? $_POST['aINumOne'] : "";
	$aINumTwo = isset($_POST['aINumTwo'])  ? $_POST['aINumTwo'] : "";
	$aINumThree = isset($_POST['aINumThree'])  ? $_POST['aINumThree'] : "";
	$itemNameOne = isset($_POST['itemNameOne'])  ? $_POST['itemNameOne'] : "";
	$itemNameTwo = isset($_POST['itemNameTwo'])  ? $_POST['itemNameTwo'] : "";
	$itemNameThree = isset($_POST['itemNameThree'])  ? $_POST['itemNameThree'] : "";
	$taskNotes = isset($_POST['taskNotes'])  ? $_POST['taskNotes'] : "";
	
	//*********Update
	$taskNumUd = isset($_POST['taskNum2'])  ? $_POST['taskNum2'] : "";
	$assignedByUd = isset($_POST['assignedBySelect2'])  ? $_POST['assignedBySelect2'] : "";
	$categoryUd = isset($_POST['categorySelect2'])  ? $_POST['categorySelect2'] : "";
	$requestDateUd = isset($_POST['requestDate2'])  ? $_POST['requestDate2'] : "";
	$issueDateUd = isset($_POST['issueDate2'])  ? $_POST['issueDate2'] : "";
	$projCompleteDateUd = isset($_POST['projCompleteDate2'])  ? $_POST['projCompleteDate2'] : "";
	$engineerAssignOneUd = isset($_POST['engineerAssignOne2'])  ? $_POST['engineerAssignOne2'] : "";
	$engineerAssignTwoUd = isset($_POST['engineerAssignTwo2'])  ? $_POST['engineerAssignTwo2'] : "";
	$engineerAssignThreeUd = isset($_POST['engineerAssignThree2'])  ? $_POST['engineerAssignThree2'] : ""; 
	$engineerAssignFourUd = isset($_POST['engineerAssignFour2'])  ? $_POST['engineerAssignFour2'] : ""; 
	$referTNUd = isset($_POST['referTN2'])  ? $_POST['referTN2'] : ""; 
	$taskTypeUd = isset($_POST['taskType2'])  ? $_POST['taskType2'] : "";
	
	$supportDocUd = isset($_POST['supportDoc2'])  ? $_POST['supportDoc2'] : "";
	//$supportDocUpd = isset($_POST['fileToUploadUpdate'])  ? $_POST['fileToUploadUpdate'] : "";
    //$fileToUploadUpdate[] = isset($_POST['fileToUploadUpdate'])  ? $_POST['fileToUploadUpdate'] : ""; 
	
	
	$taskDescUd = isset($_POST['taskDesc2'])  ? $_POST['taskDesc2'] : "";
	$systemUd = isset($_POST['system2'])  ? $_POST['system2'] : "";
	$subSysUd = isset($_POST['subSys2'])  ? $_POST['subSys2'] : "";
	$priorityUd = isset($_POST['priority2'])  ? $_POST['priority2'] : "";
	$status2Ud = isset($_POST['status2'])  ? $_POST['status2'] : "";
	$closeDateUd = isset($_POST['closeDate2'])  ? $_POST['closeDate2'] : "";
	$requestedByUd = isset($_POST['requestedBy2'])  ? $_POST['requestedBy2'] : "";
	$equipmentTypeOneUd = isset($_POST['equipmentTypeOne2'])  ? $_POST['equipmentTypeOne2'] : "";
	$equipmentTypeTwoUd = isset($_POST['equipmentTypeTwo2'])  ? $_POST['equipmentTypeTwo2'] : "";
	$equipmentTypeThreeUd = isset($_POST['equipmentTypeThree2'])  ? $_POST['equipmentTypeThree2'] : "";
	$shopAffectedOneUd = isset($_POST['shopAffectedOne2'])  ? $_POST['shopAffectedOne2'] : "";
	$shopAffectedTwoUd = isset($_POST['shopAffectedTwo2'])  ? $_POST['shopAffectedTwo2'] : "";
	$aINumOneUd = isset($_POST['aINumOne2'])  ? $_POST['aINumOne2'] : "";
	$aINumTwoUd = isset($_POST['aINumTwo2'])  ? $_POST['aINumTwo2'] : "";
	$aINumThreeUd = isset($_POST['aINumThree2'])  ? $_POST['aINumThree2'] : "";
	$itemNameOneUd = isset($_POST['itemNameOne2'])  ? $_POST['itemNameOne2'] : "";
	$itemNameTwoUd = isset($_POST['itemNameTwo2'])  ? $_POST['itemNameTwo2'] : "";
	$itemNameThreeUd = isset($_POST['itemNameThree2'])  ? $_POST['itemNameThree2'] : "";
	$taskNotesUd = isset($_POST['taskNotes2']) ? $_POST['taskNotes2'] : "";
	$pastTaskNotesUd = isset($_POST['pastTaskNotes'])  ? $_POST['pastTaskNotes'] : "";
	
	$reOpentaskNum = isset($_POST['closedJobsSelect'])  ? $_POST['closedJobsSelect'] : "";
	
	//***************REPORT
	$rptStatus = isset($_POST['selectRptStatus'])  ? $_POST['selectRptStatus'] : -1;
	$rptYear = isset($_POST['selectRptYear'])  ? $_POST['selectRptYear'] : "";
	$startTaskNum = isset($_POST['startTaskNum'])  ? $_POST['startTaskNum'] : "";
	$endTaskNum = isset($_POST['endTaskNum'])  ? $_POST['endTaskNum'] : "";
	$rptEngineer = isset($_POST['selectRptEngineer'])  ? $_POST['selectRptEngineer'] : -1;
	$rptStartDate = isset($_POST['rptStartDate'])  ? $_POST['rptStartDate'] : "";
	$rptEndDate = isset($_POST['rptEndDate'])  ? $_POST['rptEndDate'] : "";
	$rptTaskType = isset($_POST['selectRptTaskType'])  ? $_POST['selectRptTaskType'] : -1;
	$rptSystem = isset($_POST['selectRptSystem'])  ? $_POST['selectRptSystem'] : -1;
	$rptSystem = isset($_POST['selectRptSystem'])  ? $_POST['selectRptSystem'] : -1;
	$rptAssignedBy = isset($_POST['selectrptAssignedBy'])  ? $_POST['selectrptAssignedBy'] : -1;
	$rptCategory = isset($_POST['selectRptCategory'])  ? $_POST['selectRptCategory'] : "";
	$rptStartRequestDate = isset($_POST['rptStartRequestDate'])  ? $_POST['rptStartRequestDate'] : "";
	$rptEndRequestDate = isset($_POST['rptEndRequestDate'])  ? $_POST['rptEndRequestDate'] : "";
	$rptAINumber = isset($_POST['selectRptAINumber'])  ? $_POST['selectRptAINumber'] : "";
	$rptItemName = isset($_POST['selectRptItemName'])  ? $_POST['selectRptItemName'] : "";
	$rptSearchDesc = isset($_POST['rptSearchDesc'])  ? $_POST['rptSearchDesc'] : -1;
	
	$rptEquipmentType = isset($_POST['rptEquipmentType'])  ? $_POST['rptEquipmentType'] : -1;
	
	
	

	if($task == "Download")
	{
	    
	    
	    
	    $selectedFile = isset($_POST['downloadFile'])  ? $_POST['downloadFile'] : "";
	     
	    echo $selectedFile;
	
	
	
	
	    //include('config.php');
	
	    //echo $ID;
	    $qry = oci_parse($c, "SELECT TN, ID, BLOB_COL FROM BLOBS WHERE ID = '$selectedFile' and TN = '$taskNumUd'");
	    oci_execute($qry, OCI_DEFAULT) or die ("Unable to execute query");
	
	
	    while($row = oci_fetch_array($qry)){
	
	        $tn = $row['TN'];
	        $id =  $row['ID'];
	        $blob = $row['BLOB_COL']->load();
	        
	        $tmp = explode(".",$id);
	        
	        
	        
	        
	        
	        if($tmp == "pdf") 
	        {
	            header('Content-Description: File Transfer');
	            header('Content-Type: application/octet-stream');
	            header("Content-Disposition: attachment; filename=".$id );
	            header('Expires: 0');
	            header('Cache-Control: must-revalidate');
	            header('Pragma: public');
	            //header('Content-Length: ' . filesize($id));
	            echo $blob;
	            exit;
	        }
	        else
	        {
	            $tmp = explode(".",$id);
	             
	            switch ($tmp[count($tmp)-1])
	            {
	               
	                case "exe": $ctype="application/octet-stream"; break;
	                case "zip": $ctype="application/zip"; break;
	                case "docx":
	                case "doc": $ctype="application/msword"; break;
	                case "csv":
	                case "xls":
	                case "xlsx": $ctype="application/vnd.ms-excel"; break;
	                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	                case "gif": $ctype="image/gif"; break;
	                case "png": $ctype="image/png"; break;
	                case "jpeg":
	                case "jpg": $ctype="image/jpg"; break;
	                case "tif":
	                case "tiff": $ctype="image/tiff"; break;
	                case "psd": $ctype="image/psd"; break;
	                case "bmp": $ctype="image/bmp"; break;
	                case "ico": $ctype="image/vnd.microsoft.icon"; break;
	                case "msg": $ctype="application/vnd.ms-outlook;charset=UTF-8"; break;
	                default: $ctype="application/force-download";
	            }
	             
	            header("Pragma: public"); // required
	            header("Expires: 0");
	            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	            header("Cache-Control: private",false); // required for certain browsers
	            //header("Content-Type: application/msword");
	            header("Content-type: $ctype");
	            header("Content-Type: application/force-download");
	            header("Content-Type: application/octet-stream");
	            header("Content-Type: application/download");;
	            header("Content-Disposition: attachment; filename=".$id );
	            header('Accept-Ranges: bytes');
	            header("Content-Transfer-Encoding: binary");
	             
	            ob_clean();
	            flush();
	             
	            echo $blob;
	            exit;
	        }
	        
	       
	        
	       
	
	        $tmp = explode(".",$id);
	        
	        switch ($tmp[count($tmp)-1])
	        {
	            case "pdf": $ctype="application/pdf"; break;
	            case "exe": $ctype="application/octet-stream"; break;
	            case "zip": $ctype="application/zip"; break;
	            case "docx":
	            case "doc": $ctype="application/msword"; break;
	            case "csv":
	            case "xls":
	            case "xlsx": $ctype="application/vnd.ms-excel"; break;
	            case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
	            case "gif": $ctype="image/gif"; break;
	            case "png": $ctype="image/png"; break;
	            case "jpeg":
	            case "jpg": $ctype="image/jpg"; break;
	            case "tif":
	            case "tiff": $ctype="image/tiff"; break;
	            case "psd": $ctype="image/psd"; break;
	            case "bmp": $ctype="image/bmp"; break;
	            case "ico": $ctype="image/vnd.microsoft.icon"; break;
	            case "msg": $ctype="application/vnd.ms-outlook;charset=UTF-8"; break;
	            default: $ctype="application/force-download";
	        }
	        
	        header("Pragma: public"); // required
	        header("Expires: 0");
	        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	        header("Cache-Control: private",false); // required for certain browsers
	        //header("Content-Type: application/msword");
	        header("Content-type: $ctype");
	        header("Content-Type: application/force-download");
	        header("Content-Type: application/octet-stream");
	        header("Content-Type: application/download");;
	        header("Content-Disposition: attachment; filename=".$id );
	        header('Accept-Ranges: bytes');
	        header("Content-Transfer-Encoding: binary");
	        
	        ob_clean();
	        flush();
	        
	        echo $blob;
	        
	        exit; // With out the Exit the code will display in the file that was downloaded. (found when viewing file in notepad)
	     
	        
	        
	        
	        
	        
	        
	        
	        
	    }
	    
	    
	   
	    //echo ini_get('upload_max_filesize');
	    echo "<br>";
	    echo ini_get('MAX_FILE_SIZE');
	   
	   
	
	}
	 
		

if($task == "Report")
{
   // if($rptAINumber==0) $rptAINumber = "";
   // if($rptItemName==0) $rptItemName = "";
	$url = "rptOpenStatus.php?status=$rptStatus&year=$rptYear&startJob=$startTaskNum&endJob=$endTaskNum&engineer=$rptEngineer&startDte=$rptStartDate&endDte=$rptEndDate&taskType=$rptTaskType&system=$rptSystem&assignedBy=$rptAssignedBy&category=$rptCategory&startRequestDte=$rptStartRequestDate&endRequestDte=$rptEndRequestDate&aiNumber=$rptAINumber&itemName=$rptItemName&searchDesc=$rptSearchDesc&equipmentType=$rptEquipmentType";
										
	header("Location: " . $url);
	

}

if($task == "Report by Job number")
{

    $url = "jobStatus.php";

    header("Location: " . $url);


}

	
	
if($task == "Open")
{
	$qryReOpen = oci_parse($c, "update JOBS set STATUS = 1 where TASK_NUM = :TASKNUM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
				oci_bind_by_name($qryReOpen, ":TASKNUM", $reOpentaskNum, -1);

                oci_execute($qryReOpen);  
				
	$errMsg = $reOpentaskNum;			
				
				
}




if($task == "Duplicate")
{



				$qryDupeTask = oci_parse($c, "Update JOBS set STATUS = 3, APPROVED = 1 where TASK_NUM = :TN")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDupeTask, ":TN", $taskNum, -1);
								   
                                   oci_execute($qryDupeTask);


}

if($task == "Approve")
  {
	
	 if($assignedBy < 1)
	  {
	 	$errMsg .= " . <li>Please enter the person who is assigning this task</li> . ";
	  }
	
	 
	 if($category < 1)
	  {
	 	$errMsg .= " . <li>Please enter a Category</li> . ";
	  }
	 
	 
	 	 $date = explode("/", $requestDate);
  		if(count($date) != 3)
		{
    	  $errMsg .= "<li>Invalid Request Date.</li>";
  		}
  		else
		{
		  $m = $date [0];
		  $d = $date [1];
		  $y = $date [2];
			if((strlen($y) < 3) || (strlen($y) > 4))
			{
				$errMsg .= "<li>Please enter a four character year.</li>";
			}
		  if((!is_numeric($m)) || (!is_numeric($d)) || (!is_numeric($y)))
		  {
		  		$errMsg .= "<li>Invalid Request Date.</li>";
		  }
		  else
		  {
    	    if(!checkdate($date [0], $date [1], $date [2]))
		    {
      		  $errMsg .= "<li>Invalid Request Date.</li>";
    	    }
		    
  
  	      }
		 }
	 
		if($engineerAssignOne < 1)
		{
			$errMsg .= " . <li>Please enter the Engineer assigned to this Task</li> . ";
		}
	 
		if($taskType < 1)
		{
			$errMsg .= " . <li>Please enter Task Type</li> . ";
			
		}
		
		//if($taskType == 9 && $supportDoc == "")
			//{
				//$errMsg .= " . <li>This Task Type requires a support Document </li> . ";
			//}
	 
		if(!strlen($taskDesc))
		{
			$errMsg .= " . <li>Please enter a task description</li> . ";
		}
		
		
	
		if($system < 1)
		{
			$errMsg .= " . <li>Please enter a system</li> . ";
		}
	
		if($priority < 1)
		{
			$errMsg .= " . <li>Please enter a Priority</li> . ";
		}
	
		if($status < 1)
		{
			$errMsg .= " . <li>Please enter a Status</li> . ";
		}
	 
		if($requestedBy  == "")
		{
			$errMsg .= " . <li>Please enter The Person or department requesting task</li> . ";
		}
	
		if($equipmentTypeOne  < 1)
		{
			$errMsg .= " . <li>Please enter Equipment Type</li> . ";
		}
	
		if($shopAffectedOne  < 1)
		{
			$errMsg .= " . <li>Please enter The Shop Affected</li> . ";
		}
		
		
		

		
		if(!strlen($errMsg)) 
		{
		    
		   
		
		
		$qry3 = oci_parse($c, "update JOBS set ASSIGNED_BY = :ASSIGNEDBY, JOB_CATEGORY = :JOBCATEGORY, REQUEST_DATE = to_date(:REQUEST_DATE, 'mm/dd/yyyy'),
							ISSUE_DATE = to_date(:ISSUE_DATE, 'mm/dd/yyyy'), PROJ_COMP_DATE = to_date(:PROJ_COMP_DATE, 'mm/dd/yyyy'), 
							ENG_ASSIGN = :ENG_ASSIGN, EA2 = :EA_TWO, EA3 = :EA_THREE, EA4 = :EA_FOUR, REFER_TN = :REFER_TN, TASK_TYPE = :TASK_TYPE, 
							TASK_DESCRIPTION = :TASK_DESCRIPTION, SYS = :SYS, SUB_SYSTEM = :SUB_SYSTEM, PRIORITY = :PRIORITY,
							STATUS = :STATUS, CLOSE_DATE = to_date(:CLOSE_DATE, 'mm/dd/yyyy'),
							REQUESTED = :REQUESTED, EQUIP_TYPE = :EQUIP_TYPE, ET2 = :ET2, ET3 = :ET3, 
							AREA_SHOP_AFFECTED_ONE = :AREA_SHOP_AFFECTED_ONE, AREA_SHOP_AFFECTED_TWO = :AREA_SHOP_AFFECTED_TWO, 
							AI_NUM_ONE = :AI_NUM_ONE, AI_NUM_TWO = :AI_NUM_TWO, AI_NUM_THREE = :AI_NUM_THREE,
							ITEM_NAME = :ITEM_NAME, ITEM_NAME_TWO = :ITEM_NAME_TWO, ITEM_NAME_THREE = :ITEM_NAME_THREE, 
							TASK_PROGRESS_NOTES = :TASK_PROGRESS_NOTES, APPROVED = 1
							where TASK_NUM = :tn")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


                                   
								   oci_bind_by_name($qry3, ":ASSIGNEDBY", $assignedBy, -1);
								   oci_bind_by_name($qry3, ":JOBCATEGORY", $category, -1);
								   oci_bind_by_name($qry3, ":REQUEST_DATE", $requestDate, -1);
								   oci_bind_by_name($qry3, ":ISSUE_DATE", $issueDate, -1);
								   oci_bind_by_name($qry3, ":PROJ_COMP_DATE", $projCompleteDate, -1);
								   oci_bind_by_name($qry3, ":ENG_ASSIGN", $engineerAssignOne, -1);
								   oci_bind_by_name($qry3, ":EA_TWO", $engineerAssignTwo, -1);
								   oci_bind_by_name($qry3, ":EA_THREE", $engineerAssignThree, -1);
								   oci_bind_by_name($qry3, ":EA_FOUR", $engineerAssignFour, -1);
								   oci_bind_by_name($qry3, ":REFER_TN", $referTN, -1);
								   oci_bind_by_name($qry3, ":TASK_TYPE", $taskType, -1);
								   //oci_bind_by_name($qry3, ":TASK_DOC", $supportDoc, -1);
								   oci_bind_by_name($qry3, ":TASK_DESCRIPTION", $taskDesc, -1);
								   oci_bind_by_name($qry3, ":SYS", $system, -1);
								   oci_bind_by_name($qry3, ":SUB_SYSTEM", $subSys, -1);
								   oci_bind_by_name($qry3, ":PRIORITY", $priority, -1);
								   oci_bind_by_name($qry3, ":STATUS",  $status, -1);
								   oci_bind_by_name($qry3, ":CLOSE_DATE", $closeDate, -1);
								   oci_bind_by_name($qry3, ":REQUESTED", $requestedBy, -1);
								   oci_bind_by_name($qry3, ":EQUIP_TYPE", $equipmentTypeOne, -1);
								   oci_bind_by_name($qry3, ":ET2", $equipmentTypeTwo, -1);
								   oci_bind_by_name($qry3, ":ET3", $equipmentTypeThree, -1); 
								   oci_bind_by_name($qry3, ":AREA_SHOP_AFFECTED_ONE", $shopAffectedOne, -1);
								   oci_bind_by_name($qry3, ":AREA_SHOP_AFFECTED_TWO", $shopAffectedTwo, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_ONE", $aINumOne, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_TWO", $aINumTwo, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_THREE", $aINumThree, -1); 
								   oci_bind_by_name($qry3, ":ITEM_NAME", $itemNameOne, -1);
								   oci_bind_by_name($qry3, ":ITEM_NAME_TWO", $itemNameTwo, -1);
								   oci_bind_by_name($qry3, ":ITEM_NAME_THREE", $itemNameThree, -1);
								   oci_bind_by_name($qry3, ":TASK_PROGRESS_NOTES", $taskNotes, -1);
								   oci_bind_by_name($qry3, ":tn", $taskNum, -1);

								   
                                   oci_execute($qry3);
								   
								   
			$successMsg = "Task Number $taskNum has been Approved";
			
			
			$taskNum = "";
			$assignedBy = "";
			$category = "";
			$requestDate = "";
			$issueDate = "";
			$projCompleteDate = "";
			$engineerAssignOne = "";
			$engineerAssignTwo = "";
			$engineerAssignThree = ""; 
			$engineerAssignFour = ""; 
			$referTN = ""; 
			$taskType = "";
			$supportDoc = "";
			$taskDesc = "";
			$system = "";
			$subSys = "";
			$priority = "";
			$status = "";
			$closeDate = "";
			$requestedBy = "";
			$equipmentTypeOne = "";
			$equipmentTypeTwo = "";
			$equipmentTypeThree = "";
			$shopAffectedOne = "";
			$shopAffectedTwo = "";
			$aINumOne = "";
			$aINumTwo = "";
			$aINumThree = "";
			$itemNameOne = "";
			$itemNameTwo = "";
			$itemNameThree = "";
			$taskNotes = "";
			
			
			
			
			
			
		}
		
		

  }  
  
  //--------------------------------------------------------------------------------------------
  //-------------------------------------UPDATE-------------------------------------------------
  //--------------------------------------------------------------------------------------------
  
  if($task == "UPDATE")
  {
      
     $fileUpload = 0;
     
     //if($taskTypeUd != "") 
      
      for($i=0; $i<count($_FILES['fileToUpload']['name']); $i++) {
          //Get the temp file path
          // $tmpFilePath = $_FILES['fileToUpload']['tmp_name'][$i];
      
          $target_file = "/www/cmc/documents/".basename($_FILES["fileToUpload"]["name"][$i]);
            
         // $uploadOk = 1;
          $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
      
          //if ($uploadOk == 0) 
         //{
         //     echo "Sorry, your file was not uploaded.";
              // if everything is ok, try to upload file
          //} 
         // else 
         // {
              if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file))
              {
                  //echo "The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded.";
      
                  // now insert into Oracle
                  //$jobID = 100016;
      
                 // $image = file_get_contents($_FILES["fileToUpload"]["name"][$i]);
                 
                  
                  
                  $image = file_get_contents($target_file);
                  $fileName = basename( $_FILES["fileToUpload"]["name"][$i]);
      
                  $sql = oci_parse($c, "INSERT INTO blobs (ID, TN, BLOB_COL) VALUES(:ID, :TN, empty_blob()) RETURNING BLOB_COL INTO :BLOB_COL");
      
                  oci_bind_by_name($sql, ":TN", $taskNumUd, -1);
                  oci_bind_by_name($sql, ":ID", $fileName, -1);
      
      
                  $blob = oci_new_descriptor($c, OCI_D_LOB);
      
      
                  oci_bind_by_name($sql, ":BLOB_COL", $blob, -1, OCI_B_BLOB);
      
                  oci_execute($sql, OCI_DEFAULT) or die ("Unable to execute query");
      
                  if(!$blob->save($image)) {
                      oci_rollback($c);
                  }
                  else {
                      oci_commit($c);
                  }
      
                  oci_free_statement($sql);
                  $blob->free();
      
                   echo "<br> The file ". basename( $_FILES["fileToUpload"]["name"][$i]). " has been uploaded to Oracle.";
                   $fileUpload = 1;
                   
                   //$errMsgUpdate = "<li>Task Type" . $taskTypeUd .  "file upload = " . $fileUpload. "</li>"; 
      
              }
             // else
             // {
             //     echo "Sorry, there was an error uploading your file.";
             // }
              
              
         // }
      
      
      
      
      }//end for($i=0; $i<count($_FILES['fileToUpload']['name']); $i++)
      

		
	if($status2Ud == 2 && $closeDateUd == "")
	{
		$errMsgUpdate .= "<li>Please Enter a close Date.</li>";
	}
	
	if($taskNotesUd == "")
	{
		$errMsgUpdate .= "<li>Please Add a Task Progress Note explaining what the update is.</li>";
	}
	
	
	 if($assignedByUd < 1)
	  {
	 	$errMsgUpdate .= " . <li>Please enter the person who is assigning this task</li> . ";
	  }
	
	 
	 if($categoryUd < 1)
	  {
	 	$errMsgUpdate .= " . <li>Please enter a Category</li> . ";
	  }
	 
	 
	 	 
		 
		 $date = explode("/", $requestDateUd);
		 if(count($date) != 3)
		 {
		     $errMsgUpdate .= "<li>Invalid Request Date.</li>";
		 }
		 else
		 {
		     $m = $date [0];
		     $d = $date [1];
		     $y = $date [2];
		     if((strlen($y) < 3) || (strlen($y) > 4))
		     {
		         $errMsgUpdate .= "<li>Invalid Request Date. Please enter a four character year.</li>";
		     }
		     if((!is_numeric($m)) || (!is_numeric($d)) || (!is_numeric($y)))
		     {
		         $errMsgUpdate .= "<li>Invalid Request date.</li>";
		     }
		     else
		     {
		         if(!checkdate($date [0], $date [1], $date [2]))
		         {
		             $errMsgUpdate .= "<li>Invalid Request Date.</li>";
		         }
		 
		 
		     }
		 }
		 	
		/* 	
		 $date = explode("/", $issueDateUd);
		 if(count($date) != 3)
		 {
		     $errMsgUpdate .= "<li>Invalid Issue Date.</li>";
		 }
		 else
		 {
		     $m = $date [0];
		     $d = $date [1];
		     $y = $date [2];
		     if((strlen($y) < 3) || (strlen($y) > 4))
		     {
		        $errMsgUpdate .= "<li> Invalid Issue Date.Please enter a four character year.</li>";
		     }
		     if((!is_numeric($m)) || (!is_numeric($d)) || (!is_numeric($y)))
		     {
		         $errMsgUpdate .= "<li>Invalid Issue date.</li>";
		     }
		     else
		     {
		         if(!checkdate($date [0], $date [1], $date [2]))
		         {
		             $errMsgUpdate .= "<li>Invalid Issue Date.</li>";
		         }
		         	
		         	
		     }
		 }
		 	
		 	
		 	
		 $date = explode("/", $projCompleteDateUd);
		 if($date != "")
		 {
		 if(count($date) != 3)
		 {
		     $errMsgUpdate .= "<li>Invalid Projected Completion Date.</li>";
		 }
		 else
		 {
		     $m = $date [0];
		     $d = $date [1];
		     $y = $date [2];
		     if((strlen($y) < 3) || (strlen($y) > 4))
		     {
		         $errMsgUpdate .= "<li> Invalid Projected Completion Date. Please enter a four character year.</li>";
		     }
		     if((!is_numeric($m)) || (!is_numeric($d)) || (!is_numeric($y)))
		     {
		         $errMsgUpdate .= "<li>Invalid Projected Completion Date.</li>";
		     }
		     else
		     {
		         if(!checkdate($date [0], $date [1], $date [2]))
		         {
		             $errMsgUpdate .= "<li>Invalid Projected Completion Date.</li>";
		         }
		 
		 
		     }
		 }
		
		 
		 $date = explode("/", $closeDateUd);
		 if($date != "")
		 {
		 if(count($date) != 3)
		 {
		    $errMsgUpdate .= "<li>Invalid Close Date.</li>";
		 }
		 else
		 {
		     $m = $date [0];
		     $d = $date [1];
		     $y = $date [2];
		     if((strlen($y) < 3) || (strlen($y) > 4))
		     {
		         $errMsgUpdate .= "<li> Invalid Close Date. Please enter a four character year.</li>";
		     }
		     if((!is_numeric($m)) || (!is_numeric($d)) || (!is_numeric($y)))
		     {
		         $errMsgUpdate .= "<li>Invalid Close Date.</li>";
		     }
		     else
		     {
		         if(!checkdate($date [0], $date [1], $date [2]))
		         {
		             $errMsgUpdate .= "<li>Invalid Close Date.</li>";
		         }
		         	
		         	
		     }
		 }
		 
		  */
		 //}
		
		 // } 6/3/16
		 
		 
		
	 
		if($engineerAssignOneUd < 1)
		{
			$errMsgUpdate .= " . <li>Please enter the Engineer assigned to this Task</li> . ";
		}
	 
		if($taskTypeUd < 1)
		{
			$errMsgUpdate .= " . <li>Please enter Task Type</li> . ";
			
		}
		
		//$docCnt = $_FILES['fileToUpload']['name'][0];
		
		//$errMsgUpdate = "<li>Task Type" . $taskTypeUd .  "file upload = " . $fileUpload. "</li>"; 
		
		if(($taskTypeUd == 9) && ($fileUpload == 0))
			{
			    
			    $fileCnt = 0;
			    $qry = oci_parse($c, "SELECT count(TN) as TNC FROM BLOBS WHERE TN = :TN");
			    
			    oci_bind_by_name($qry, ":TN", $taskNumUd, -1);
			    
			    oci_execute($qry, OCI_DEFAULT) or die ("Unable to execute query");
			    
			    
			    while($row = oci_fetch_array($qry)){
			        
			        $fileCnt = $row['TNC'];
			    
			    }
			    
			    if($fileCnt == 0)
			    {
			        $errMsgUpdate .= "<li>This Task Type requires a support document </li> ";
			    }
			    
			    
				
			}
	 
		if(!strlen($taskDescUd))
		{
			$errMsgUpdate .= " . <li>Please enter a task description</li> . ";
		}
		
		
	
		if($systemUd < 1)
		{
			$errMsgUpdate .= " . <li>Please enter a system</li> . ";
		}
	
		if($priorityUd < 1)
		{
			$errMsgUpdate .= " . <li>Please enter a Priority</li> . ";
		}
	
		if($status2Ud < 1)
		{
			$errMsgUpdate .= " . <li>Please enter a Status</li> . ";
		}
	 
		if($requestedByUd == "")
		{
			$errMsgUpdate .= " . <li>Please enter The Person or Department requesting task</li> . ";
		}
	
		if($equipmentTypeOneUd  < 1)
		{
			$errMsgUpdate .= " . <li>Please enter Equipment Type</li> . ";
		}
	
		if($shopAffectedOneUd  < 1)
		{
			$errMsgUpdate .= " . <li>Please enter The Shop Affected</li> . ";
		}
		
		
		
		
		
		
		if(!strlen($errMsgUpdate)) 
		{


		    
		    
		    
		
		
	
		$taskNotesAppend =  date("F j, Y, g:i a") . " - " . $taskNotesUd  ."<br>" . $pastTaskNotesUd;

			
		
		
		$qry3 = oci_parse($c, "update JOBS set ASSIGNED_BY = :ASSIGNEDBY, JOB_CATEGORY = :JOBCATEGORY, REQUEST_DATE = to_date(:REQUEST_DATE, 'mm/dd/yyyy'),
							ISSUE_DATE = to_date(:ISSUE_DATE, 'mm/dd/yyyy'), PROJ_COMP_DATE = to_date(:PROJ_COMP_DATE, 'mm/dd/yyyy'), 
							ENG_ASSIGN = :ENG_ASSIGN, EA2 = :EA_TWO, EA3 = :EA_THREE, EA4 = :EA_FOUR, REFER_TN = :REFER_TN, TASK_TYPE = :TASK_TYPE, 
							TASK_DESCRIPTION = :TASK_DESCRIPTION, SYS = :SYS, SUB_SYSTEM = :SUB_SYSTEM, PRIORITY = :PRIORITY,
							STATUS = :STATUS, CLOSE_DATE = to_date(:CLOSE_DATE, 'mm/dd/yyyy'),
							REQUESTED = :REQUESTED, EQUIP_TYPE = :EQUIP_TYPE, ET2 = :ET2, ET3 = :ET3, 
							AREA_SHOP_AFFECTED_ONE = :AREA_SHOP_AFFECTED_ONE, AREA_SHOP_AFFECTED_TWO = :AREA_SHOP_AFFECTED_TWO, 
							AI_NUM_ONE = :AI_NUM_ONE, AI_NUM_TWO = :AI_NUM_TWO, AI_NUM_THREE = :AI_NUM_THREE,
							ITEM_NAME = :ITEM_NAME, ITEM_NAME_TWO = :ITEM_NAME_TWO, ITEM_NAME_THREE = :ITEM_NAME_THREE, 
							TASK_PROGRESS_NOTES = :TASK_PROGRESS_NOTES, APPROVED = 1
							where TASK_NUM = :tn")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


                                   
								   oci_bind_by_name($qry3, ":ASSIGNEDBY", $assignedByUd, -1);
								   oci_bind_by_name($qry3, ":JOBCATEGORY", $categoryUd, -1);
								   oci_bind_by_name($qry3, ":REQUEST_DATE", $requestDateUd, -1);
								   oci_bind_by_name($qry3, ":ISSUE_DATE", $issueDateUd, -1);
								   oci_bind_by_name($qry3, ":PROJ_COMP_DATE", $projCompleteDateUd, -1);
								   oci_bind_by_name($qry3, ":ENG_ASSIGN", $engineerAssignOneUd, -1);
								   oci_bind_by_name($qry3, ":EA_TWO", $engineerAssignTwoUd, -1);
								   oci_bind_by_name($qry3, ":EA_THREE", $engineerAssignThreeUd, -1);
								   oci_bind_by_name($qry3, ":EA_FOUR", $engineerAssignFourUd, -1);
								   oci_bind_by_name($qry3, ":REFER_TN", $referTNUd, -1);
								   oci_bind_by_name($qry3, ":TASK_TYPE", $taskTypeUd, -1);
								   //oci_bind_by_name($qry3, ":TASK_DOC", $supportDocUd, -1);
								   oci_bind_by_name($qry3, ":TASK_DESCRIPTION", $taskDescUd, -1);
								   oci_bind_by_name($qry3, ":SYS", $systemUd, -1);
								   oci_bind_by_name($qry3, ":SUB_SYSTEM", $subSysUd, -1);
								   oci_bind_by_name($qry3, ":PRIORITY", $priorityUd, -1);
								   oci_bind_by_name($qry3, ":STATUS",  $status2Ud, -1);
								   oci_bind_by_name($qry3, ":CLOSE_DATE", $closeDateUd, -1);
								   oci_bind_by_name($qry3, ":REQUESTED", $requestedByUd, -1);
								   oci_bind_by_name($qry3, ":EQUIP_TYPE", $equipmentTypeOneUd, -1);
								   oci_bind_by_name($qry3, ":ET2", $equipmentTypeTwoUd, -1);
								   oci_bind_by_name($qry3, ":ET3", $equipmentTypeThreeUd, -1); 
								   oci_bind_by_name($qry3, ":AREA_SHOP_AFFECTED_ONE", $shopAffectedOneUd, -1);
								   oci_bind_by_name($qry3, ":AREA_SHOP_AFFECTED_TWO", $shopAffectedTwoUd, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_ONE", $aINumOneUd, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_TWO", $aINumTwoUd, -1);
								   oci_bind_by_name($qry3, ":AI_NUM_THREE", $aINumThreeUd, -1); 
								   oci_bind_by_name($qry3, ":ITEM_NAME", $itemNameOneUd, -1);
								   oci_bind_by_name($qry3, ":ITEM_NAME_TWO", $itemNameTwoUd, -1);
								   oci_bind_by_name($qry3, ":ITEM_NAME_THREE", $itemNameThreeUd, -1);
								   oci_bind_by_name($qry3, ":TASK_PROGRESS_NOTES", $taskNotesAppend, -1);
								   oci_bind_by_name($qry3, ":tn", $taskNumUd, -1);

								   
                                   oci_execute($qry3);
								   
								   
			$successMsgUpdate = "Task Number $taskNumUd has been Updated";
			
			
			$taskNumUd = "";
			$assignedByUd = "";
			$categoryUd = "";
			$requestDateUd = "";
			$issueDateUd = "";
			$projCompleteDateUd = "";
			$engineerAssignOneUd = "";
			$engineerAssignTwoUd = "";
			$engineerAssignThreeUd = ""; 
			$engineerAssignFourUd = ""; 
			$referTNUd = ""; 
			$taskTypeUd = "";
			$supportDocUd = "";
			$taskDescUd = "";
			$systemUd = "";
			$subSysUd = "";
			$priorityUd = "";
			$status2Ud = "";
			$closeDateUd = "";
			$requestedByUd = "";
			$equipmentTypeOneUd = "";
			$equipmentTypeTwoUd = "";
			$equipmentTypeThreeUd = "";
			$shopAffectedOneUd = "";
			$shopAffectedTwoUd = "";
			$aINumOneUd = "";
			$aINumTwoUd = "";
			$aINumThreeUd = "";
			$itemNameOneUd = "";
			$itemNameTwoUd = "";
			$itemNameThreeUd = "";
			$taskNotesUd = "";
			$pastTaskNotesUd = "";
			$reOpentaskNum = "";
			$rptStatus = "";
			
			
			
			
			
			
		}

		// }//6/3/16
  }
  
//*************************************************************************************************************************** 
//************************************************Maintenance****************************************************************
//***************************************************************************************************************************  
  
$empMaintSelect = isset($_POST['empMaintSelect'])  ? $_POST['empMaintSelect'] : ""; 
$empFName = isset($_POST['empFName'])  ? $_POST['empFName'] : "";
$empLName = isset($_POST['empLName'])  ? $_POST['empLName'] : "";
$empInitials = isset($_POST['empInitials'])  ? $_POST['empInitials'] : "";
$empDept = isset($_POST['empDept'])  ? $_POST['empDept'] : "";
$empEmail = isset($_POST['empEmail'])  ? $_POST['empEmail'] : "";  


 
 
if($task == "Add Employee")
{
	$empCount = 0;
	$qryEmpCnt= oci_parse($c, "select MAX(EMPID) as CNT from ENGINEER")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryEmpCnt);
					   
				 while(($row = oci_fetch_array($qryEmpCnt)) !== false)
				 {
						$empCount = $row['CNT'];
                 }
	
	$empCount = $empCount +1;
	
	$qryAddEmp = oci_parse($c, "insert into ENGINEER (EMPID, FNAME, LNAME, INITIALS, DEPT, EMAIL) 
								Values (:EMPID, :FNAME, :LNAME, :INITIALS, :DEPT, :EMAIL)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddEmp, ":EMPID", $empCount, -1);
								   oci_bind_by_name($qryAddEmp, ":FNAME", $empFName, -1);
								   oci_bind_by_name($qryAddEmp, ":LNAME", $empLName, -1);
								   oci_bind_by_name($qryAddEmp, ":INITIALS", $empInitials, -1);	
								   oci_bind_by_name($qryAddEmp, ":DEPT", $empDept, -1);	
								   oci_bind_by_name($qryAddEmp, ":EMAIL", $empEmail, -1);
								   
                                   oci_execute($qryAddEmp);

}
if($task == "Update Employee")
{



	$qryUpdateEmp = oci_parse($c, "Update ENGINEER set FNAME = :FNAME, LNAME = :LNAME, INITIALS = :INITIALS, DEPT = :DEPT, EMAIL = :EMAIL
									where EMPID = :EMPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateEmp, ":FNAME", $empFName, -1);
								   oci_bind_by_name($qryUpdateEmp, ":LNAME", $empLName, -1);
								   oci_bind_by_name($qryUpdateEmp, ":INITIALS", $empInitials, -1);	
								   oci_bind_by_name($qryUpdateEmp, ":DEPT", $empDept, -1);	
								   oci_bind_by_name($qryUpdateEmp, ":EMAIL", $empEmail, -1);
								   oci_bind_by_name($qryUpdateEmp, ":EMPID", $empMaintSelect, -1);
								   
                                   oci_execute($qryUpdateEmp);






}
if($task == "Delete Employee")
{

	$qryDeleteEmp = oci_parse($c, "DELETE from ENGINEER where EMPID = :EMPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteEmp, ":EMPID", $empMaintSelect, -1);
								   
                                   oci_execute($qryDeleteEmp);

}
//****************************Category**************************************************

$catMaintSelect = isset($_POST['catMaintSelect'])  ? $_POST['catMaintSelect'] : ""; 
$catDesc = isset($_POST['maintCatDesc'])  ? $_POST['maintCatDesc'] : "";

 
if($task == "Add Category")
{
	$taskCount = 0;
	$qryCnt= oci_parse($c, "select MAX(CATID) as CNT from CATEGORY")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt)) !== false)
				 {
						$taskCount = $row['CNT'];
                 }
	
	$taskCount = $taskCount + 1;
	
	$qryAddCat = oci_parse($c, "insert into CATEGORY (CATID, CATDESC) 
								Values (:CATID, :CATDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddCat, ":CATID", $taskCount, -1);
								   oci_bind_by_name($qryAddCat, ":CATDESC", $catDesc, -1);
								   
								   
                                   oci_execute($qryAddCat);

}
if($task == "Update Category")
{

	$qryUpdateCat = oci_parse($c, "Update CATEGORY set CATDESC = :CATDESC where CATID = :CATID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateCat, ":CATID", $catMaintSelect, -1);
								   oci_bind_by_name($qryUpdateCat, ":CATDESC", $catDesc, -1);
								   
								   
                                   oci_execute($qryUpdateCat);
}
if($task == "Delete Category")
{

	$qryDeleteCat = oci_parse($c, "DELETE from CATEGORY where CATID = :CATID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteCat, ":CATID", $catMaintSelect, -1);
								   
                                   oci_execute($qryDeleteCat);

}

//*****************************************Task Type***********************************************************************


$taskMaintSelect = isset($_POST['taskMaintSelect'])  ? $_POST['taskMaintSelect'] : ""; 
$taskMaintDesc = isset($_POST['maintTaskDesc'])  ? $_POST['maintTaskDesc'] : "";

 
if($task == "Add Task")
{
	$taskCount = 0;
	$qryCnt= oci_parse($c, "select MAX(TASKID) as CNT from TASK_TYPE")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt)) !== false)
				 {
						$taskCount = $row['CNT'];
                 }
	
	$taskCount = $taskCount + 1;
	
	$qryAddTask = oci_parse($c, "insert into TASK_TYPE (TASKID, TASKDESC) 
								Values (:TASKID, :TASKDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddTask, ":TASKID", $taskCount, -1);
								   oci_bind_by_name($qryAddTask, ":TASKDESC", $taskMaintDesc, -1);
								   
								   
                                   oci_execute($qryAddTask);

}
if($task == "Update Task")
{

	$qryUpdateTask = oci_parse($c, "Update TASK_TYPE set TASKDESC = :TASKDESC where TASKID = :TASKID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateTask, ":TASKID", $taskMaintSelect, -1);
								   oci_bind_by_name($qryUpdateTask, ":TASKDESC", $taskMaintDesc, -1);
								   
								   
                                   oci_execute($qryUpdateTask);
}
if($task == "Delete Task")
{

	$qryDeleteTask = oci_parse($c, "DELETE from TASK_TYPE where TASKID = :TASKID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteTask, ":TASKID", $taskMaintSelect, -1);
								   
                                   oci_execute($qryDeleteTask);

}
//*****************************************System***********************************************************************


$sysMaintSelect = isset($_POST['sysMaintSelect'])  ? $_POST['sysMaintSelect'] : ""; 
$sysDesc = isset($_POST['maintSysDesc'])  ? $_POST['maintSysDesc'] : "";

 
if($task == "Add System")
{
	$sysCount = 0;
	$qryCnt= oci_parse($c, "select MAX(SYSID) as CNT from JOB_SYSTEM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt))!== false)
				 {
						$sysCount = $row['CNT'];
                 }
	
	$sysCount = $sysCount + 1;
	
	$qryAddSys = oci_parse($c, "insert into JOB_SYSTEM (SYSID, SYSDESC) 
								Values (:SYSID, :SYSDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddSys, ":SYSID", $sysCount, -1);
								   oci_bind_by_name($qryAddSys, ":SYSDESC", $sysDesc, -1);
								   
								   
                                   oci_execute($qryAddSys);

}
if($task == "Update System")
{

	$qryUpdateSys = oci_parse($c, "Update JOB_SYSTEM set SYSDESC = :SYSDESC where SYSID = :SYSID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateSys, ":SYSID", $sysMaintSelect, -1);
								   oci_bind_by_name($qryUpdateSys, ":SYSDESC", $sysDesc, -1);
								   
								   
                                   oci_execute($qryUpdateSys);
}
if($task == "Delete System")
{

	$qryDeleteSys = oci_parse($c, "DELETE from JOB_SYSTEM where SYSID = :SYSID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteSys, ":SYSID", $sysMaintSelect, -1);
								   
                                   oci_execute($qryDeleteSys);

}
//*****************************************Sub-System***********************************************************************


$subSysMaintSelect = isset($_POST['subSysMaintSelect'])  ? $_POST['subSysMaintSelect'] : ""; 
$subSysDesc = isset($_POST['maintSubSysDesc'])  ? $_POST['maintSubSysDesc'] : "";

 
if($task == "Add SubSystem")
{
	$subSysCount = 0;
	$qryCnt= oci_parse($c, "select MAX(SUBSYSID) as CNT from JOB_SUB_SYSTEM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt))!== false)
				 {
						$subSysCount = $row['CNT'];
                 }
	
	$subSysCount = $subSysCount + 1;
	
	$qryAddSubSys = oci_parse($c, "insert into JOB_SUB_SYSTEM (SUBSYSID, SUBSYSDESC) 
								Values (:SUBSYSID, :SUBSYSDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddSubSys, ":SUBSYSID", $subSysCount, -1);
								   oci_bind_by_name($qryAddSubSys, ":SUBSYSDESC", $subSysDesc, -1);
								   
								   
                                   oci_execute($qryAddSubSys);

}
if($task == "Update SubSystem")
{

	$qryUpdateSubSys = oci_parse($c, "Update JOB_SUB_SYSTEM set SUBSYSDESC = :SUBSYSDESC where SUBSYSID = :SUBSYSID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateSubSys, ":SUBSYSID", $subSysMaintSelect, -1);
								   oci_bind_by_name($qryUpdateSubSys, ":SUBSYSDESC", $subSysDesc, -1);
								   
								   
                                   oci_execute($qryUpdateSubSys);
}
if($task == "Delete SubSystem")
{

	$qryDeleteSubSys = oci_parse($c, "DELETE from JOB_SUB_SYSTEM where SUBSYSID = :SUBSYSID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteSubSys, ":SUBSYSID", $subSysMaintSelect, -1);
								   
                                   oci_execute($qryDeleteSubSys);

} 
//*****************************************Equipment Type***********************************************************************


$eqMaintSelect = isset($_POST['eqMaintSelect'])  ? $_POST['eqMaintSelect'] : ""; 
$eqDesc = isset($_POST['maintEqDesc'])  ? $_POST['maintEqDesc'] : "";

 
if($task == "Add Equipment")
{
	$eqCount = 0;
	$qryCnt= oci_parse($c, "select MAX(EQUIPID) as CNT from EQUIP_TYPE")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt))!== false)
				 {
						$eqCount = $row['CNT'];
                 }
	
	$eqCount = $eqCount + 1;
	
	$qryAddEq = oci_parse($c, "insert into EQUIP_TYPE (EQUIPID, EQUIPDESC) 
								Values (:EQUIPID, :EQUIPDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddEq, ":EQUIPID", $eqCount, -1);
								   oci_bind_by_name($qryAddEq, ":EQUIPDESC", $eqDesc, -1);
								   
								   
                                   oci_execute($qryAddEq);

}
if($task == "Update Equipment")
{

	$qryUpdateEq = oci_parse($c, "Update EQUIP_TYPE set EQUIPDESC = :EQUIPDESC where EQUIPID = :EQUIPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateEq, ":EQUIPID", $eqMaintSelect, -1);
								   oci_bind_by_name($qryUpdateEq, ":EQUIPDESC", $eqDesc, -1);
								   
								   
                                   oci_execute($qryUpdateEq);
}
if($task == "Delete Equipment")
{

	$qryDeleteEq = oci_parse($c, "DELETE from EQUIP_TYPE where EQUIPID = :EQUIPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteEq, ":EQUIPID", $eqMaintSelect, -1);
								   
                                   oci_execute($qryDeleteEq);

}  
 
//*****************************************Shop***********************************************************************


$shopMaintSelect = isset($_POST['shopMaintSelect'])  ? $_POST['shopMaintSelect'] : ""; 
$shopDesc = isset($_POST['maintShopDesc'])  ? $_POST['maintShopDesc'] : "";

 
if($task == "Add Shop")
{
	$shopCount = 0;
	$qryCnt= oci_parse($c, "select MAX(SHOPID) as CNT from SHOP")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt))!== false)
				 {
						$shopCount = $row['CNT'];
                 }
	
	$shopCount = $shopCount + 1;
	
	$qryAddShop = oci_parse($c, "insert into SHOP (SHOPID, SHOPDESC) 
								Values (:SHOPID, :SHOPDESC)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddShop, ":SHOPID", $shopCount, -1);
								   oci_bind_by_name($qryAddShop, ":SHOPDESC", $shopDesc, -1);
								   
								   
                                   oci_execute($qryAddShop);

}
if($task == "Update Shop")
{

	$qryUpdateShop = oci_parse($c, "Update SHOP set SHOPDESC = :SHOPDESC where SHOPID = :SHOPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateShop, ":SHOPID", $shopMaintSelect, -1);
								   oci_bind_by_name($qryUpdateShop, ":SHOPDESC", $shopDesc, -1);
								   
								   
                                   oci_execute($qryUpdateShop);
}
if($task == "Delete Shop")
{

	$qryDeleteShop = oci_parse($c, "DELETE from SHOP where SHOPID = :SHOPID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteShop, ":SHOPID", $shopMaintSelect, -1);
								   
                                   oci_execute($qryDeleteShop);

}   
 
//*****************************************Department***********************************************************************


$deptMaintSelect = isset($_POST['deptMaintSelect'])  ? $_POST['deptMaintSelect'] : ""; 
$deptDesc = isset($_POST['maintDeptDesc'])  ? $_POST['maintDeptDesc'] : "";

 
if($task == "Add Department")
{
	$deptCount = 0;
	$qryCnt= oci_parse($c, "select MAX(DEPTNUM) as CNT from DEPARTMENTS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
									   
				 oci_execute($qryCnt);
					   
				 while(($row = oci_fetch_array($qryCnt))!== false)
				 {
						$deptCount = $row['CNT'];
                 }
	
	$deptCount = $deptCount + 1;
	
	$qryAddDept = oci_parse($c, "insert into DEPARTMENTS (DEPTNUM, DEPT) 
								Values (:DEPTNUM, :DEPT)")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryAddDept, ":DEPTNUM", $deptCount, -1);
								   oci_bind_by_name($qryAddDept, ":DEPT", $deptDesc, -1);
								   
								   
                                   oci_execute($qryAddDept);

}
if($task == "Update Department")
{

	$qryUpdateDept = oci_parse($c, "Update DEPARTMENTS set DEPT = :DEPT where DEPTNUM = :DEPTNUM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								  
								   oci_bind_by_name($qryUpdateDept, ":DEPTNUM", $deptMaintSelect, -1);
								   oci_bind_by_name($qryUpdateDept, ":DEPT", $deptDesc, -1);
								   
								   
                                   oci_execute($qryUpdateDept);
}
if($task == "Delete Department")
{

	$qryDeleteDept = oci_parse($c, "DELETE from DEPARTMENTS where DEPTNUM = :DEPTNUM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

								   oci_bind_by_name($qryDeleteDept, ":DEPTNUM", $deptMaintSelect, -1);
								   
                                   oci_execute($qryDeleteDept);

}   
  
 


header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past










?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="jobs.css">
<title>TASK ASSIGNMENTS</title>

<link rel="stylesheet" type="text/css" media="all" href="../lib/jscalendar/skins/aqua/theme.css" title="win2k-cold-1" />

<script type="text/javascript" src="../lib/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../lib/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script>

<link href="template1/tabcontent.css" rel="stylesheet" type="text/css" /> 
<script src="tabcontent.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="styles.css">
<script type="text/javascript" src="jquery-1.11.2.js"></script>


<META HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
<META HTTP-EQUIV="Expires" CONTENT="-1"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />


</head>

<body>

<center><A href="jobs.php">Back to Task Assigment Menu</A></center>

	<ul class="tabs" data-persist="true"> 
		<li><a href="#view1">Approve</a></li> 
		<li><a href="#view2">Update</a></li> 
		<li><a href="#view4">Reports</a></li> 
		<li><a href="#view5">Closed Jobs</a></li> 
		<li><a href="#view6">Maintenance</a></li> 
	</ul> 
	
	<div class="tabcontents"> 

		<div style="background-color:#FFF2F2;" id="view1"  > 
		

 
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform">
          
           
  <fieldset id="task">
    <legend>TASK ASSIGNMENTS ADMINISTRATION <?php //session_start(); echo $_SESSION['group'] ?></legend>
	
	
    <table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
                  

                 <?php
				 
					if(strlen($errMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $errMsg </td></tr>";
					}
					
					if(strlen($successMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $successMsg </td></tr>";
					}
				  
				 ?>

                  <tr>
				  
					 <td>Tasks to be approved: </td>
				  
                     <?php 
					 
					
					 
					 
					 
						echo "<td><select name=\"taskNum\" id = \"taskNum\" onchange=\"getData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASK_NUM from JOBS where APPROVED = 0 order by JOB_ID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASK_NUM'];
    
									if($id == $taskNum)
									echo "<option value= \"$id\" selected=\"selected\"> $id </option> ";
									else
									echo "<option value=\"$id\"> $id </option>";
									
									


                                   }
						echo "</select></td>";
						
						
                                  
						?>
					  <td><font color="red">*</font> Assigned By: </td>
					 
					 <?php 
					 
					 			 
						echo "<td><select name=\"assignedBySelect\" id = \"assignedBySelect\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];
									 
										if($id == $assignedBy)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";	
										


                                   }
						echo "</select></td>";
					 ?>
					 
					 <td><font color="red">*</font> Category: </td>
					 <td>
					<?php 
					
					  echo "<select name=\"categorySelect\" id = \"categorySelect\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT CATID, CATDESC from CATEGORY order by CATDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['CATID'];
                                     $desc = $row['CATDESC'];
										if($id == $category)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";
                                       


                                   }
                                  
					?> 
					 
					</select>		
					 </td>
 
                  </tr>
                   <tr>
                    
					 
					 <td><font color="red">*</font> Request Date: </td><td><input readonly type="text" name="requestDate" size="10" tabindex="24" id="requestDate" value="<?php echo $requestDate; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbutton2" alt="Click here to pick date" /></td>
					 <td> Issue Date:  </td><td><input readonly type="text" name="issueDate" size="10" tabindex="24" id="issueDate" value="<?php echo $issueDate; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbutton3" alt="Click here to pick date" /></td> 
						<td>Proj. Compl. Date: </td><td><input readonly  type="text" name="projCompleteDate" size="10" tabindex="24" id="projCompleteDate" value="<?php echo $projCompleteDate; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbutton4" alt="Click here to pick date" /></td>
                  </tr>
                   <tr>
					 <td><font color="red">*</font> Engineer Assigned: </td><td>
					 
					 <?php 
					 
	
					 
					 
					 
					 
					  echo "<select name=\"engineerAssignOne\" id = \"engineerAssignOne\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];
										if($id == $engineerAssignOne)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";
                                       


                                   }

                                  
					?> 
					 
					 
					 
					 </select></td>
					 <td>Engineer Assigned(2): </td><td>
					 
					 
					 <?php 
					  echo "<select name=\"engineerAssignTwo\" id = \"engineerAssignTwo\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignTwo)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }

                                  
					?> 
					 
					 
					 </select></td>
					 <td>Engineer Assigned(3): </td>
					 
					 <?php 
					  echo "<td><select name=\"engineerAssignThree\" id = \"engineerAssignThree\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignThree)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
					 echo "</select></td>";
                                  
					?> 
					 
					 
					
                  </tr>
                   <tr>
		
					 </select></td>
					 <td>Engineer Assigned(4): </td>
					 
					 <?php 
					  echo "<td><select name=\"engineerAssignFour\" id = \"engineerAssignFour\" >";
						echo "<option value= 0 selected>  </option>";
 
                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                      $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignFour)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
					 echo "</select></td>";
                                  
					?> 
						
						<td> Refer - T/N:  </td><td><input name="referTN" id="referTN" value="<?php echo $referTN; ?>" size ="10"  maxlength="50"/> </td>
						
						<td><font color="red">*</font> Task Type: </td>
						
						<?php 
						//onchange=\"checkTask()\"
						echo "<td><select name=\"taskType\" id = \"taskType\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASKID, TASKDESC from TASK_TYPE order by TASKDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASKID'];
                                     $desc = $row['TASKDESC'];
									 

                                       if($id == $taskType)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                   
						?>

						
						
				
                  </tr>
				  
					<tr>
						
						<!-- <td> Support Document:  </td><td><input name="fileToUpload[]" id= "supportDocUpld" size="75" type="file" multiple="multiple" value="<?php echo $supportDoc; ?>" /></td> -->	
						<td> Support Documents attached:  </td><td colspan = "4"><input name="supportDoc" id="supportDoc" value="<?php echo $supportDocUd; ?>" size ="80"  maxlength="100"/> </td>
						<td>
						
						
						
						
						
						
						</td>
						
						
						</tr>
						
						<tr>
						
						<td><font color="red">*</font> Requested By: </td>
						<td><input name="requestedBy" id="requestedBy" value="<?php echo $requestedBy; ?>" size ="30"  maxlength="50"/></td>
						
						
					
					
					</tr>

				  
                   <tr >
                     <td><font color="red">*</font> Task Description:</td><td colspan = "5">  <textarea rows="3" cols="120" name="taskDesc" id = "taskDesc"> <?php echo $taskDesc; ?> </textarea></td>
                  </tr>
                   <tr>
                        <td><font color="red">*</font> System:  </td>
						<?php 
						echo "<td><select name=\"system\" id = \"system\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SYSID, SYSDESC from JOB_SYSTEM order by SYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SYSID'];
                                     $desc = $row['SYSDESC'];
									 

                                      if($id == $system)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Sub System: </td>
							
						<?php 
						echo "<td><select name=\"subSys\" id = \"subSys\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SUBSYSID, SUBSYSDESC from JOB_SUB_SYSTEM order by SUBSYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SUBSYSID'];
                                     $desc = $row['SUBSYSDESC'];
									 

                                       if($id == $subSys)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						<td><font color="red">*</font> Priority: </td>
						
						<?php 
						echo "<td><select name=\"priority\" id = \"priority\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT PRIORITYID, PRIORITYDESC from PRIORITY order by PRIORITYID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['PRIORITYID'];
                                     $desc = $row['PRIORITYDESC'];
									 

                                       if($id == $priority)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						
                  
                   </tr>
				   <tr>
                        <td><font color="red">*</font> Status:  </td>
														


						<?php 
						echo "<td><select name=\"status\" id = \"status\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT STATID, STATDESC from STATUS order by STATID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATID'];
                                     $desc = $row['STATDESC'];
									 

                                       if($id == $status)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						
						<td>Close Date: </td><td><input readonly type="text" name="closeDate" size="10" tabindex="24" id="closeDate" disabled value="<?php echo $closeDate; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbutton5" alt="Click here to pick date" /></td> </td>
						
						
                  
                   </tr>
				   <tr>
						<td><font color="red">*</font> Equipment Type: </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeOne\" id = \"equipmentTypeOne\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                      if($id == $equipmentTypeOne)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						<td>Equipment Type (2): </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeTwo\" id = \"equipmentTypeTwo\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                       if($id == $equipmentTypeTwo)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Equipment Type (3): </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeThree\" id = \"equipmentTypeThree\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                       if($id == $equipmentTypeThree)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
                  </tr> 
                  <tr>
						<td></td><td></td>
						
						<td></td><td></td>
                  </tr> 
					<tr>
						
	
	
						<td><font color="red">*</font> Area/Shop Affected: </td>
						<?php 
						echo "<td><select name=\"shopAffectedOne\" id = \"shopAffectedOne\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SHOPID, SHOPDESC from SHOP order by SHOPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SHOPID'];
                                     $desc = $row['SHOPDESC'];
									 

                                       if($id == $shopAffectedOne)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Area/Shop Affected (2): </td>
						<?php 
						echo "<td><select name=\"shopAffectedTwo\" id = \"shopAffectedTwo\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SHOPID, SHOPDESC from SHOP order by SHOPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SHOPID'];
                                     $desc = $row['SHOPDESC'];
									 

                                       if($id == $shopAffectedTwo)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td></td><td></td>
                  </tr> 

				  <tr>
                     <td>Commodity ID Number:</td><td> <input name="aINumOne" id="aINumOne" value="<?php echo $aINumOne; ?>" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(2) Number: </td><td><input name="aINumTwo" id="aINumTwo" value="<?php echo $aINumTwo; ?>" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(3) Number:  </td><td><input name="aINumThree" id="aINumThree" value="<?php echo $aINumThree; ?>" size ="10"  maxlength="50"/></td>
                  </tr>
				 
				  <tr>
                     <td>Item Description:</td><td> <input name="itemNameOne" id="itemNameOne" value="<?php echo $itemNameOne; ?>" size ="30"  maxlength="50"/></td>
					 <td>Item Description(2): </td><td><input name="itemNameTwo" id="itemNameTwo" value="<?php echo $itemNameTwo; ?>" size ="30"  maxlength="50"/></td>
					 <td>Item Description(3):  </td><td><input name="itemNameThree" id="itemNameThree" value="<?php echo $itemNameThree; ?>" size ="30"  maxlength="50"/></td>
                  </tr>
				  <tr >
                     <td>Task Progress Notes:</td><td colspan = "5"><textarea rows="3" cols="120" name="taskNotes" id="taskNotes" ><?php echo $taskNotes; ?> </textarea></td>
                  </tr>
				<tr>
					<td colspan = "6" align="center"> __________________________________________________________________________________</td>
				</tr>
                  <td align="right" class="titleCell" colspan = "3">
                   <?php
                      
                        echo "<input class=\"submitAdd\" type=\"submit\" value=\"Approve\" name=\"SUBMIT\" id=\"SUBMIT\" />";
						echo "</td><td align=\"left\" class=\"titleCell\" colspan = \"3\">";
						
						 echo "<input class=\"submitAdd\" type=\"submit\" value=\"Duplicate\" name=\"SUBMIT\" id=\"SUBMIT\" />";
                   ?>
                  </td>


                  </tr>

                  
         </table>
	
	
   
    
  </fieldset>
  <br>
  
  
  
</form>
 </div>
 <!--
 ************************************************************************************************************************************************
 ************************************************************************************************************************************************
 -->
 
 <!--<div id="view2"> -->
 
<div style="background-color:#FFF2F2;" id="view2"  > 
		

  
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
          
           
  <fieldset id="update">
    <legend>TASK ASSIGNMENTS ADMINISTRATION</legend>
	
	
    <table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
                  

                  <?php
				 
					if(strlen($errMsgUpdate)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $errMsgUpdate </td></tr>";
					}
					
					if(strlen($successMsgUpdate)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $successMsgUpdate </td></tr>";
					}
				  
				 ?>
                 
                  <tr>
				  
					 <td>Tasks: </td>
				  
                     <?php 
                       // echo " <td>\"$taskNumUd\" </td>";
                     
						echo "<td><select name=\"taskNum2\" id = \"taskNum2\" onchange=\"getData2()\"> <option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASK_NUM from JOBS where APPROVED = 1 order by JOB_YEAR, TASK_NUM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASK_NUM'];
                                     
									 if($id == $taskNumUd)
										echo "<option value= \"$id\" selected=\"selected\"> $id </option> ";
										else
										echo "<option value=\"$id\"> $id </option>";
                                    


                                   }
						echo "</select></td>";
                                  
						?>
					  <td><font color="red">*</font> Assigned By: </td>
					 
					 <?php 
						echo "<td><select name=\"assignedBySelect2\" id = \"assignedBySelect2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];
									 

                                       if($id == $assignedByUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
					 ?>
					 
					 <td><font color="red">*</font> Category: </td>
					 <td>
					<?php 
					  echo "<select name=\"categorySelect2\" id = \"categorySelect2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT CATID, CATDESC from CATEGORY order by CATDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                       oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['CATID'];
                                     $desc = $row['CATDESC'];

                                       if($id == $categoryUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                                  
					?> 
					 
					</select>		
					 </td>
 
                  </tr>
                   <tr>
                    
					 
					 <td><font color="red">*</font> Request Date: </td><td><input readonly type="text" name="requestDate2" size="10" tabindex="24" id="requestDate2" value="<?php echo $requestDateUd; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbuttonU2" alt="Click here to pick date" /></td>
					 <td> Issue Date:  </td><td><input readonly type="text" name="issueDate2" size="10" tabindex="24" id="issueDate2" value="<?php echo $issueDateUd; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbuttonU3" alt="Click here to pick date" /></td> 
						<td>Proj. Compl. Date: </td><td><input readonly type="text" name="projCompleteDate2" size="10" tabindex="24" id="projCompleteDate2" value="<?php echo $projCompleteDateUd; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbuttonU4" alt="Click here to pick date" /></td>
                  </tr>
                   <tr>
					 <td><font color="red">*</font> Engineer Assigned: </td><td>
					 
					 <?php 
					  echo "<select name=\"engineerAssignOne2\" id = \"engineerAssignOne2\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }

                                  
					?> 
					 
					 
					 
					 </select></td>
					 <td>Engineer Assigned(2): </td><td>
					 
					 
					 <?php 
					  echo "<select name=\"engineerAssignTwo2\" id = \"engineerAssignTwo2\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignTwoUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }

                                  
					?> 
					 
					 
					 </select></td>
					 <td>Engineer Assigned(3): </td>
					 
					 <?php 
					  echo "<td><select name=\"engineerAssignThree2\" id = \"engineerAssignThree2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignThreeUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
					 echo "</select></td>";
                                  
					?> 
					 
					 
					 
					
                  </tr>
                   <tr>
				   
						<td>Engineer Assigned(4): </td>
					 
					 <?php 
					  echo "<td><select name=\"engineerAssignFour2\" id = \"engineerAssignFour2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignFourUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
					 echo "</select></td>";
					 ?>
				   								
						<td> Refer - T/N:  </td><td><input name="referTN2" id="referTN2" value="<?php echo $referTNUd; ?>" size ="10"  maxlength="50"/> </td>
						
						<td><font color="red">*</font> Task Type: </td>
						
						<?php 
						echo "<td><select name=\"taskType2\" id = \"taskType2\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASKID, TASKDESC from TASK_TYPE order by TASKDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASKID'];
                                     $desc = $row['TASKDESC'];
									 

                                       if($id == $taskTypeUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?>

						
						
				
                  </tr>
				  
				  	<tr>
						
						
						
						<td>  Support Documents attached:</td>
						
						<td><select name=downloadFile id = downloadFile>"
						
						<?php
						/*
						//echo "<td> TASK Num = $taskNumUd </td>";
						
						//echo"<td><select name= \"downloadFile\" id = \"downloadFile\">";
												
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT ID, TN from BLOBS where TN = :TASKNUM order by ID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                   
                                   oci_bind_by_name($qry, ":TASKNUM", $taskNumUd, -1);

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                   $desc = $row['ID'];
                                   $tn = $row['TN'];
									 
                                   $desc = "TEST";
                                      
										echo "<option value=\"$tn\" > TEST </option>";


                                   }
                                   
						echo "</select></td>";
                        */          
						?>
						
						
						
						</select>
						</td>
						
						
						
						<td> Add Support Document: <input name="fileToUpload[]" id= "rDoc" size="10" type="file" multiple="multiple" value="" /></td>
						
					</tr>
						
						<tr>
						

						
						<td><font color="red">*</font> Requested By: </td>
						<td><input name="requestedBy2" id="requestedBy2" value="<?php echo $requestedByUd; ?>" size ="30"  maxlength="50"/></td>
						
						
					</tr>

				  
                   <tr >
                     <td><font color="red">*</font> Task Description:</td><td colspan = "5">  <textarea rows="3" cols="120" name="taskDesc2" id = "taskDesc2"><?php echo $taskDescUd; ?> </textarea></td>
                  </tr>
                   <tr>
                        <td><font color="red">*</font> System:  </td>
						<?php 
						echo "<td><select name=\"system2\" id = \"system2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SYSID, SYSDESC from JOB_SYSTEM order by SYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SYSID'];
                                     $desc = $row['SYSDESC'];
									 

                                       if($id == $systemUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Sub System: </td>
							
						<?php 
						echo "<td><select name=\"subSys2\" id = \"subSys2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SUBSYSID, SUBSYSDESC from JOB_SUB_SYSTEM order by SUBSYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SUBSYSID'];
                                     $desc = $row['SUBSYSDESC'];
									 

                                       if($id == $subSysUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						<td><font color="red">*</font> Priority: </td>
						
						<?php 
						echo "<td><select name=\"priority2\" id = \"priority2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT PRIORITYID, PRIORITYDESC from PRIORITY order by PRIORITYID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['PRIORITYID'];
                                     $desc = $row['PRIORITYDESC'];
									 

                                       if($id == $priorityUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						
                  
                   </tr>
				   <tr>
                        <td><font color="red">*</font> Status:  </td>
														


						<?php 
						echo "<td><select name=\"status2\" id = \"status2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT STATID, STATDESC from STATUS order by STATID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATID'];
                                     $desc = $row['STATDESC'];
									 

                                       if($id == $status2Ud)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						
						<td>Close Date: </td><td><input readonly type="text" name="closeDate2" size="10" tabindex="24" id="closeDate2" value="<?php echo $closeDateUd; ?>" /><img src="cal.gif" width="16" border="0" id="startCalbuttonU5" alt="Click here to pick date" /></td> </td>
						
						
                  
                   </tr>
				   <tr>
						<td><font color="red">*</font> Equipment Type: </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeOne2\" id = \"equipmentTypeOne2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                       if($id == $equipmentTypeOneUd )
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
						<td>Equipment Type (2): </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeTwo2\" id = \"equipmentTypeTwo2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                      if($id == $equipmentTypeTwoUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Equipment Type (3): </td>
						
						<?php 
						echo "<td><select name=\"equipmentTypeThree2\" id = \"equipmentTypeThree2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
                                     $desc = $row['EQUIPDESC'];
									 

                                       if($id == $equipmentTypeThreeUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						
                  </tr> 
                  <tr>
						<td></td><td></td>
						
						<td></td><td></td>
                  </tr> 
					<tr>
						
	
	
						<td><font color="red">*</font> Area/Shop Affected: </td>
						<?php 
						echo "<td><select name=\"shopAffectedOne2\" id = \"shopAffectedOne2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SHOPID, SHOPDESC from SHOP order by SHOPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SHOPID'];
                                     $desc = $row['SHOPDESC'];
									 

                                       if($id == $shopAffectedOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td>Area/Shop Affected (2): </td>
						<?php 
						echo "<td><select name=\"shopAffectedTwo2\" id = \"shopAffectedTwo2\" >";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SHOPID, SHOPDESC from SHOP order by SHOPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SHOPID'];
                                     $desc = $row['SHOPDESC'];
									 

                                       if($id == $shopAffectedTwoUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						?> 
						<td></td><td></td>
                  </tr> 

				  <tr>
                     <td>Commodity ID Number:</td><td> <input name="aINumOne2" id="aINumOne2" value="<?php echo $aINumOneUd; ?>" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(2) Number: </td><td><input name="aINumTwo2" id="aINumTwo2" value="<?php echo $aINumTwoUd; ?>" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(3) Number:  </td><td><input name="aINumThree2" id="aINumThree2" value="<?php echo $aINumThreeUd; ?>" size ="10"  maxlength="50"/></td>
                  </tr>
				 
				  <tr>
                     <td>Item Description:</td><td> <input name="itemNameOne2" id="itemNameOne2" value="<?php echo $itemNameOneUd; ?>" size ="30"  maxlength="50"/></td>
					 <td>Item Description(2): </td><td><input name="itemNameTwo2" id="itemNameTwo2" value="<?php echo $itemNameTwoUd; ?>" size ="30"  maxlength="50"/></td>
					 <td>Item Description(3):  </td><td><input name="itemNameThree2" id="itemNameThree2" value="<?php echo $itemNameThreeUd; ?>" size ="30"  maxlength="50"/></td>
                  </tr>
				  <tr >
                     <td><font color="red">*</font> Task Progress Notes:</td><td colspan = "5"><textarea rows="3" cols="120" name="taskNotes2" id="taskNotes2" ><?php echo $taskNotesUd; ?></textarea></td>
					 
                  </tr>
				  <tr >
				 <td colspan = "5"><textarea style="display:none;" rows="3" cols="120" name="pastTaskNotes" id="pastTaskNotes" ><?php echo $pastTaskNotesUd; ?></textarea></td>
				 </tr>
				 <tr>
				 <td>Past Progress Notes:</td><td colspan = "5"><p name="p1" id="p1"><?php echo $pastTaskNotesUd; ?></p></td>
				 </tr> 
				<tr>
					<td colspan = "6" align="center"> ___________________________________________________</td>
				</tr>
				<tr>
                  <td align="center" class="titleCell" colspan = "6">
                   <?php
                      
                        echo "<input class=\"submitAdd\" type=\"submit\" value=\"UPDATE\" name=\"SUBMIT\" id=\"SUBMIT\" />";
						
                   ?>
                  </td>


                  </tr>

                  
         </table>
	
	
    
  </fieldset>
  <br></br>
  
  
  
</form>
 
 
 
 </div> 
 
 
 
 
 
 
 <div id="view4">
 
 
<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="Report" id="mainform" >
 <?php
 
 if($task != "Report")
 {
 //echo "<a href=\"rptOpenStatus.php\">Status Report</a><br><br>";
 
 echo "<table border=0 cellpadding=\"10\">";
 

 
 echo "<tr><td>Status:  </td>";
  
					
                       
 
						echo "<td><select name=\"selectRptStatus\" id = \"selectRptStatus\" >";
						//echo "<option value= 0 selected>  </option>";
						echo "<option value=0 > All </option>";

                                   $qry = oci_parse($c, "SELECT STATID, STATDESC from STATUS order by STATID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATID'];
                                     $desc = $row['STATDESC'];
									 

                                      
										echo "<option value=\"$id\" > $desc </option>";


                                   }
						echo "</select></td>";
                                  
						echo "<td>Year:  </td>";
						echo "<td><select name=\"selectRptYear\" id = \"selectRptYear\" >";
						//echo "<option value= 0 selected>  </option>";
						echo "<option value=0 >All</option>";
						
		
						$qry = oci_parse($c, "SELECT DISTINCT JOB_YEAR as JOB_YEAR from JOBS order by JOB_YEAR")
						OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
						
						oci_execute($qry);
						
						while($row = oci_fetch_array($qry)){
						    $id = $row['JOB_YEAR'];
						    
						
						   // if(($id >= 0)&&($id < 10))
						   // {
						   //     $desc = '200' . $id;
						   // }
						   // else
						   // {
						   //     $desc = '20' . $id;
						   // }
						   // if($id > 50) $desc = '19' . $id;
						
						
						    echo "<option value=\"$id\" >   $id  </option>";
						
						
						}
						
						echo "</select></td></tr>";
						
						//************START
						
						
						 
						echo "<tr><td>Start with Job:  </td>";
						
						 
						 
						
						echo "<td><select name=\"startTaskNum\" id = \"startTaskNum\" >";
						echo "<option value= 0 selected>  </option>";
						
						$qry = oci_parse($c, "SELECT JOB_ID, TASK_NUM from JOBS order by JOB_YEAR, TASK_NUM")
						OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
						
						oci_execute($qry);
						
						while($row = oci_fetch_array($qry)){
						    $id = $row['JOB_ID'];
						    $num = $row['TASK_NUM'];
						
						
						
						    echo "<option value=\"$id\" > $num </option>";
						
						
						}
						echo "</select></td>";
						
						
						echo "<td>End with Job:  </td>";
						echo "<td><select name=\"endTaskNum\" id = \"endTaskNum\" >";
						echo "<option value= 0 selected>  </option>";
						
						
						
						
						$qry = oci_parse($c, "SELECT JOB_ID, TASK_NUM from JOBS order by JOB_YEAR, TASK_NUM")
						OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
						
						oci_execute($qry);
						
						while($row = oci_fetch_array($qry)){
						    $id = $row['JOB_ID'];
						    $num = $row['TASK_NUM'];
						
						
						
						    echo "<option value=\"$id\" > $num </option>";
						
						
						}
						
						echo "</select></td>";
						
						
						
						
						
						
						
						
						
						
						echo "</tr>";
						
						
						
						
						
						//**********END
						
 
 
 
 echo "<tr><td>Engineer:  </td>";
 
					
 
 
						 echo "<td><select name=\"selectRptEngineer\" id = \"selectRptEngineer\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                            echo "</select></td> ";
                            
                            //$rptEquipmentType
						
 
                            
                            
                            
                            
                            echo "<td>Equipment Type:  </td>";
                            
                            	
                            
                            
                            echo "<td><select name=\"rptEquipmentType\" id = \"rptEquipmentType\" >";
                            echo "<option value= 0 selected>  </option>";
                            
                            $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                            
                            oci_execute($qry);
                            
                            while($row = oci_fetch_array($qry)){
                                $id = $row['EQUIPID'];
                                $desc = $row['EQUIPDESC'];
                                //$desc .= ", " . $row['FNAME'];
                            
                               // if($id == $engineerAssignOneUd)
                               //     echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
                               //     else
                                      echo "<option value=\"$id\" > $desc </option>";
                            
                            
                            }
                            echo "</select></td> </tr>";
                            
                            
                            
                            
                            
                            
                            
                            
 
 
		echo "<tr><td>Start Issue Date: </td><td><input readonly type=\"text\" name=\"rptStartDate\" size=\"10\" tabindex=\"24\" id=\"rptStartDate\" value=\" $requestDate \"/><img src=\"cal.gif\" width=\"16\" border=\"0\" id=\"startCalbuttonRpt\" alt=\"Click here to pick date\" /></td>";
		echo "<td>End Issue Date: </td><td><input readonly type=\"text\" name=\"rptEndDate\" size=\"10\" tabindex=\"24\" id=\"rptEndDate\" value=\"$requestDate \" /><img src=\"cal.gif\" width=\"16\" border=\"0\" id=\"endCalbuttonRpt\" alt=\"Click here to pick date\" /></td></tr>";
 
 
 echo "<tr><td>Task Type:  </td>";
 
					
 
 
						 echo "<td><select name=\"selectRptTaskType\" id = \"selectRptTaskType\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASKID, TASKDESC from TASK_TYPE order by TASKDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASKID'];
                                     $desc = $row['TASKDESC'];
									 

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                            echo "</select></td> ";      
						 
 

 
   echo "<td>System:  </td>";

					
 
 
						 echo "<td><select name=\"selectRptSystem\" id = \"selectRptSystem\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SYSID, SYSDESC from JOB_SYSTEM order by SYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SYSID'];
                                     $desc = $row['SYSDESC'];
									 

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                            echo "</select></td></tr>";      
						
 
 
 
 echo"<tr><td>Assigned By:  </td>";
 
					
 
 
						 echo "<td><select name=\"selectrptAssignedBy\" id = \"selectrptAssignedBy\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                            echo "</select></td> ";          
					
 

 
 echo"<td>Category:  </td>";

					
 
 
						 echo "<td><select name=\"selectRptCategory\" id = \"selectRptCategory\" >";
							echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT CATID, CATDESC from CATEGORY order by CATDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['CATID'];
                                     $desc = $row['CATDESC'];
									

                                       if($id == $engineerAssignOneUd)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";


                                   }
                            echo "</select></td></tr>";          
						
 
 
 
 echo"<tr><td>Commodity ID Number:  </td>";
 
					
					$aiArray = array();
 
						$qry = oci_parse($c, "SELECT distinct AI_NUM_ONE from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['AI_NUM_ONE'];
                                     
									
									
                                     $aiArray[] = $ai;
										


                                   }
 
						$qry = oci_parse($c, "SELECT distinct AI_NUM_TWO from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['AI_NUM_TWO'];
                                     
									
									
                                     $aiArray[] = $ai;
										


                                   }
								   
								   
						$qry = oci_parse($c, "SELECT distinct AI_NUM_THREE from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['AI_NUM_THREE'];
                                     
									
									
                                     $aiArray[] = $ai;
										


                                   }
								   
								   
							$uniqueAINums = array_unique($aiArray);
							
							sort($uniqueAINums);
 
						 echo "<td><select name=\"selectRptAINumber\" id = \"selectRptAINumber\" >";
							echo "<option value= 0 selected>  </option>";
							
							foreach($uniqueAINums as &$value)
							{
								echo "<option value=\"$value\" > $value </option>";
							}

                                   
                            echo "</select></td>";          
					
 
 
 
 
 
 echo"<td>Item Description:  </td>";
 			
					$itemArray = array();
 
						$qry = oci_parse($c, "SELECT distinct ITEM_NAME from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['ITEM_NAME'];
                                     
									
									
                                     $itemArray[] = $ai;
										


                                   }
 
						$qry = oci_parse($c, "SELECT distinct ITEM_NAME_TWO from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['ITEM_NAME_TWO'];
                                     
									
									
                                     $itemArray[] = $ai;
										


                                   }
								   
								   
						$qry = oci_parse($c, "SELECT distinct ITEM_NAME_THREE from JOBS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $ai = $row['ITEM_NAME_THREE'];
                                     
									
									
                                     $itemArray[] = $ai;
										


                                   }
								   
								   
							$uniqueItems = array_unique($itemArray);
							
							sort($uniqueItems);
 
						 echo "<td><select name=\"selectRptItemName\" id = \"selectRptItemName\" >";
							echo "<option value= 0 selected>  </option>";
							
							foreach($uniqueItems as &$value)
							{
								echo "<option value=\"$value\" > $value </option>";
							}

                                   
                            echo "</select></td> </tr>";          
						
 

 
 
 
 
		echo"<td>Start Request Date: </td><td><input readonly type=\"text\" name=\"rptStartRequestDate\" size=\"10\" tabindex=\"24\" id=\"rptStartRequestDate\" value=\"$rptStartRequestDate\" /><img src=\"cal.gif\" width=\"16\" border=\"0\" id=\"startRequestCalbuttonRpt\" alt=\"Click here to pick date\" /></td>";
		echo"<td>End Request Date: </td><td><input readonly type=\"text\" name=\"rptEndRequestDate\" size=\"10\" tabindex=\"24\" id=\"rptEndRequestDate\" value=\"$rptEndRequestDate\" /><img src=\"cal.gif\" width=\"16\" border=\"0\" id=\"endRequestCalbuttonRpt\" alt=\"Click here to pick date\" /></td></tr>";
 
 
		echo"<td>Search Description: </td><td><input type=\"text\" name=\"rptSearchDesc\" size=\"75\" tabindex=\"25\" id=\"rptSearchDesc\"  /></td>";
 
 
 
 echo"<tr>";
 
					
                                             
                        echo "<td colspan = \"4\" align=\"center\" ><input class=\"submitAdd\" type=\"submit\" value=\"Report\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
						                      
                   
 echo"</tr>";
 
 
 
 
 echo"<tr><td colspan = \"4\" align=\"center\"> ____________________________________________________________________</td></tr>";
 
 echo "<tr><td colspan = \"4\" align=\"center\"><b>Job Search</b> </td></tr>";
 
 
 
 echo "<tr><td colspan = \"4\" align=\"center\" ><input class=\"submitAdd\" type=\"submit\" value=\"Report by Job number\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
 
 
 
 
 
 
 
 
 
 echo"</table>";
 } //end if($task != "Report")
 ?>
 


 

 
 
 

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 </form>
 </div><!--<div id="view4">-->
 <div id="view5">
 
 
 <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" name="new_inquiry" id="mainform" >
 
	
 
 
    <!--

*******************************************************************************************************
________________________________________________________________________________________________________
????????????????????????????????????????????????????????????????????????????????????????????????????????
________________________________________________________________________________________________________
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
________________________________________________________________________________________________________
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
________________________________________________________________________________________________________
&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
________________________________________________________________________________________________________
########################################################################################################



		-->
           
  <fieldset id="Closed Jobs">
    <legend>Closed Jobs</legend>
	
	
    <table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
                  

                 
                  <tr>
				  
					 <td>Closed Jobs: </td>
				  
                     <?php 
						echo "<td><select name=\"closedJobsSelect\" id = \"closedJobsSelect\" onchange=\"getClosedData()\"> <option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASK_NUM from JOBS where STATUS = 2 order by JOB_YEAR, TASK_NUM")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASK_NUM'];
                                     
									 if($id == $taskNumUd)
										echo "<option value= \"$id\" selected=\"selected\"> $id </option> ";
										else
										echo "<option value=\"$id\"> $id </option>";
                                    


                                   }
						echo "</select></td>";
                                  
						?>
					  <td>Assigned By: </td>
						<td>
							<input readonly type="text" name="closeAssignedBy" id="closeAssignedBy" >
						</td>				


                                   
					 
					 <td>Category: </td>
					 <td>
						<input readonly type="text" name="closeCategory" id="closeCategory" >
					 </td>
 
                  </tr>
                     <tr>
						<td>Request Date: </td><td><input readonly type="text" name="closeRequestDate" id="closeRequestDate" ></td>
						<td> Issue Date:  </td><td><input readonly type="text" name="closeIssueDate" id="closeIssueDate" ></td>
						<td>Proj. Compl. Date: </td><td><input readonly type="text" name="closeProjCompleteDate" id="closeProjCompleteDate" ></td>
					 </tr>
					 
                   <tr>
					 <td>Engineer Assigned: </td>
					 <td><input readonly type="text" name="closeEngineerAssignOne" id="closeEngineerAssignOne" ></td>
					 
					 <td>Engineer Assigned(2): </td>
					 <td><input readonly type="text" name="closeEngineerAssignTwo" id="closeEngineerAssignTwo" ></td>
					 
					 <td>Engineer Assigned(3): </td>
					 <td><input readonly type="text" name="closeEngineerAssignThree" id="closeEngineerAssignThree" ></td>
					 
					 
					 	 
                  </tr>
				  <tr>
					<td>Engineer Assigned(4): </td>
					 <td><input readonly type="text" name="closeEngineerAssignFour" id="closeEngineerAssignFour" ></td>
					 <td>Requested By: </td>
						<td><input readonly name="closeRequestedBy" id="closeRequestedBy" maxlength="100"/> </td>
				
					
						
				  </tr>
                   <tr>						
						<td> Refer - T/N:</td><td><input readonly name="closeReferTN" id="closeReferTN" maxlength="50"/> </td>
						
						<td>Task Type: </td>
						<td><input readonly name="closeTaskType" id="closeTaskType" maxlength="50"/> </td>
						
						<td> Support Document:  </td>
						<td><input readonly name="closeSupportDoc" id="closeSupportDoc" maxlength="100"/> </td>	
                  </tr>
				  
				  	

				  
                   <tr >
                     <td> Task Description:</td>
					 <td colspan = "5"><textarea rows="3" cols="120" name="closeTaskDesc" id="closeTaskDesc" ></textarea></td>
					 
                  </tr>
                   <tr>
                        <td> System:  </td>
						<td><input readonly name="closeSystem" id="closeSystem" maxlength="100"/> </td>
						
						<td>Sub System: </td>
						<td><input readonly name="closeSubSys" id="closeSubSys" maxlength="100"/> </td>	
												
						<td> Priority: </td>
						<td><input readonly name="closePriority" id="closePriority" maxlength="100"/> </td>

                   </tr>
				   <tr>
                        <td> Status:  </td>
						<td><input readonly name="closeStatus" id="closeStatus" maxlength="100"/> </td>

						<td>Close Date: </td>
						<td><input readonly name="closeCloseDate" id="closeCloseDate"  maxlength="100"/> </td>
						
						

                   </tr>
				   <tr>
						<td>Equipment Type: </td>
						<td><input readonly name="closeEquipmentTypeOne" id="closeEquipmentTypeOne" maxlength="100"/> </td>
											
						<td>Equipment Type (2): </td>
						<td><input readonly name="closeEquipmentTypeTwo" id="closeEquipmentTypeTwo" maxlength="100"/> </td>	
						
						<td>Equipment Type (3): </td>
						<td><input readonly name="closeEquipmentTypeThree" id="closeEquipmentTypeThree" maxlength="100"/> </td>
                  </tr> 
                  <tr>
						<td></td><td></td>
						
						<td></td><td></td>
                  </tr> 
					<tr>
						
	
	
						<td>Area/Shop Affected: </td>
						<td><input readonly name="closeShopAffectedOne" id="closeShopAffectedOne" maxlength="100"/> </td>
						
						<td>Area/Shop Affected (2): </td>
						<td><input readonly name="closeShopAffectedTwo" id="closeShopAffectedTwo" maxlength="100"/> </td>
						
						<td></td><td></td>
                  </tr> 

				  <tr>
                     <td>Commodity ID Number:</td><td> <input name="closeAINumOne" id="closeAINumOne" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(2): </td><td><input name="closeAINumTwo" id="closeAINumTwo" size ="10"  maxlength="50"/></td>
					 <td>Commodity ID Number(3):  </td><td><input name="closeAINumThree" id="closeAINumThree" size ="10"  maxlength="50"/></td>
                  </tr>
				 
				  <tr>
                     <td>Item Description:</td><td> <input name="closeItemNameOne" id="closeItemNameOne" size ="10"  maxlength="50"/></td>
					 <td>Item Description(2): </td><td><input name="closeItemNameTwo" id="closeItemNameTwo" size ="10"  maxlength="50"/></td>
					 <td>Item Description(3):  </td><td><input name="closeItemNameThree" id="closeItemNameThree" size ="10"  maxlength="50"/></td>
                  </tr>
				  
				 <tr>
				 <!--<td>Task Progress Notes:</td><td colspan = "5"><textarea rows="3" cols="120" name="closeTaskNotes" id="closeTaskNotes" ></textarea></td>-->
				 <td>Past Progress Notes:</td><td colspan = "5"><p name="cp1" id="cp1"></p></td>
				 </tr> 
				<tr>
					<td colspan = "6" align="center"> ___________________________________________________</td>
				</tr>
				<tr>
                  <td align="center" class="titleCell" colspan = "6">
                   <?php
                      
                        echo "<input class=\"submitAdd\" type=\"submit\" value=\"Open\" name=\"SUBMIT\" id=\"SUBMIT\" />";
						
                   ?>
                  </td>


                  </tr>

                  
         </table>
	
    <!--

*******************************************************************************************************
________________________________________________________________________________________________________
????????????????????????????????????????????????????????????????????????????????????????????????????????
________________________________________________________________________________________________________
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
________________________________________________________________________________________________________
++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
________________________________________________________________________________________________________
&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
________________________________________________________________________________________________________
########################################################################################################



		-->	
    
  </fieldset>
  <br>
  
  
  
</form>
 
 </div> <!-- <div id="view5"> -->
 
 
 <div id="view6">
 
 
 
<div style="background-color:#FFF2F2;" id="view6"  > 
		

 
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data"name="new_inquiry" id="mainform" >
  
  
  
  
          
           
	<fieldset id="Maintenance">
    <legend>Maintenance	</legend>

	
	
	
	
	
	
	<div id="content">
   <div id="tab-container">
      <ul id="tabs-titles"  class="content">
         <li><a href="#view1">Employee</a></li>
		 <li><a href="#view2">Category</a></li>
         <li><a href="#view3">Task Type</a></li>
		 <li><a href="#view4">System</a></li>
		 <li><a href="#view5">Sub-System</a></li>
		 <li><a href="#view6">Equipment Type</a></li>
		 <li><a href="#view7">Shop Area Affected</a></li>
		 <li><a href="#view8">Department</a></li>
      </ul>
</div>
  
   <div id="main-container">
   
		<ul id="tabs-contents" style="list-style: none;">
			<li>
			
			
				<div id="view1">
					<!-- vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "6" align="center"><font size="6"><b>Employee Maintenance</b></font></td>
					</tr>
					
					<tr>
						<td>Employee</td>
					</tr>
					<tr>
			
						<?php 				
		 
						echo "<td width=\"10%\"><select name=\"empMaintSelect\" id = \"empMaintSelect\" onchange=\"getEmpData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EMPID, FNAME, LNAME from ENGINEER order by LNAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPID'];
                                     $desc = $row['LNAME'];
									 $desc .= ", " . $row['FNAME'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
				
					</tr>
					<tr>
					<TD><BR></TD>
		
			
					<tr>
			
						<td width="10%">First Name:</td><td> <input name="empFName" id="empFName" value="" size ="20"  maxlength="50"/></td>
						<td width="10%">Last Name:</td><td> <input name="empLName" id="empLName" value="" size ="20"  maxlength="50"/></td>
						<td width="10%">Initials:</td><td> <input name="empInitials" id="empInitials" value="" size ="20"  maxlength="50"/></td>
		
					<tr>
						<td width="10%">Department:</td><td> <input name="empDept" id="empDept" value="" size ="20"  maxlength="50"/></td>
						<td width="10%">Email:</td><td> <input name="empEmail" id="empEmail" value="" size ="20"  maxlength="50"/></td>
					</tr>
					<TD><BR></TD>
					<tr>	
						<td colspan = "6" align="center">
						<input class="AddEmployee" type="submit" value="Add Employee" name="SUBMIT" id="SUBMIT" />
						<input class="UpdateEmployee" type="submit" value="Update Employee" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteEmployee" type="submit" value="Delete Employee" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			<!-- ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ -->	
				</div> <!-- ***<div id="view1">*** -->
			
			
			</li>
			<li>
			
				
				<div id="view2">
				
				<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Category Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a Category to update:</td>
						<?php 				
		 
						echo "<td><select name=\"catMaintSelect\" id = \"catMaintSelect\" onchange=\"getCatData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT CATID, CATDESC from CATEGORY order by CATDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['CATID'];
									 $desc = $row['CATDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Category:</td> <td> <input name="maintCatDesc" id="maintCatDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddCategory" type="submit" value="Add Category" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateCategory" type="submit" value="Update Category" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteCategory" type="submit" value="Delete Category" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
			
				</div><!--<div id="view2">-->
				
			</li>
			<li>
				<div id="view3">
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Task Type Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a Task Type to update:</td>
						<?php 				
		 
						echo "<td><select name=\"taskMaintSelect\" id = \"taskMaintSelect\" onchange=\"getTaskData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT TASKID, TASKDESC from TASK_TYPE order by TASKDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['TASKID'];
									 $desc = $row['TASKDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Task Type:</td> <td> <input name="maintTaskDesc" id="maintTaskDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddTask" type="submit" value="Add Task" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateTask" type="submit" value="Update Task" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteTask" type="submit" value="Delete Task" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
				</div>
			</li>
			<li>
				<div id="view4">
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>System Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a System to update:</td>
						<?php 				
		 
						echo "<td><select name=\"sysMaintSelect\" id = \"sysMaintSelect\" onchange=\"getSysData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SYSID, SYSDESC from JOB_SYSTEM order by SYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SYSID'];
									 $desc = $row['SYSDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">System:</td> <td> <input name="maintSysDesc" id="maintSysDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddSystem" type="submit" value="Add System" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateSystem" type="submit" value="Update System" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteSystem" type="submit" value="Delete System" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
				</div>
			</li>
			
			<li>
				<div id="view5">
				
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Sub-System Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a Sub-System to update:</td>
						<?php 				
		 
						echo "<td><select name=\"subSysMaintSelect\" id = \"subSysMaintSelect\" onchange=\"getSubSysData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SUBSYSID, SUBSYSDESC from JOB_SUB_SYSTEM order by SUBSYSDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SUBSYSID'];
									 $desc = $row['SUBSYSDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Sub-System:</td> <td> <input name="maintSubSysDesc" id="maintSubSysDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddSubSystem" type="submit" value="Add SubSystem" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateSubSystem" type="submit" value="Update SubSystem" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteSubSystem" type="submit" value="Delete SubSystem" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
			
				</div>
			</li>
			<li>
				<div id="view6">
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Equipment Type Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose an Equipment Type to update:</td>
						<?php 				
						
						echo "<td><select name=\"eqMaintSelect\" id = \"eqMaintSelect\" onchange=\"getEquipData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT EQUIPID, EQUIPDESC from EQUIP_TYPE order by EQUIPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EQUIPID'];
									 $desc = $row['EQUIPDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Equipment Type:</td> <td> <input name="maintEqDesc" id="maintEqDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddEquipment" type="submit" value="Add Equipment" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateEquipment" type="submit" value="Update Equipment" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteEquipment" type="submit" value="Delete Equipment" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
				</div>
			</li>
			
			<li>
				<div id="view7">
				
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Shop Area Affected Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a Shop to update:</td>
						<?php 				
		 
						echo "<td><select name=\"shopMaintSelect\" id = \"shopMaintSelect\" onchange=\"getShopData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT SHOPID, SHOPDESC from SHOP order by SHOPDESC")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['SHOPID'];
									 $desc = $row['SHOPDESC'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Shop:</td> <td> <input name="maintShopDesc" id="maintShopDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddShop" type="submit" value="Add Shop" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateShop" type="submit" value="Update Shop" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteShop" type="submit" value="Delete Shop" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
			
				</div>
			</li>
			
			
			
			
			
			
			
			
			
			
			<li>
				<div id="view8">
				
					<!-- ***************************************************************************************** -->
					<table border = "0" width=100%>
					
					<tr>
						<td colspan = "2" align="center"><font size="6"><b>Department Maintenance</b></font></td>
					</tr>
					
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>
						
						<td align="right">Choose a Department to update:</td>
						<?php 				
		 
						echo "<td><select name=\"deptMaintSelect\" id = \"deptMaintSelect\" onchange=\"getDeptData()\">";
						echo "<option value= 0 selected>  </option>";

                                   $qry = oci_parse($c, "SELECT DEPTNUM, DEPT from DEPARTMENTS order by DEPT")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['DEPTNUM'];
									 $desc = $row['DEPT'];
									 

                                       echo "<option value=\"$id\"> $desc </option>";


                                   }
						echo "</select></td>";
						?>
					 
					</tr>
					
					<tr colspan = "2" ></tr>
					
					<tr>

						<td align="right">Department:</td> <td> <input name="maintDeptDesc" id="maintDeptDesc" value="" size ="20"  maxlength="50"/></td>

					</tr>
					
					<tr>
						<td colspan = "2" align="center">_____________________________________________________</td>
					</tr>
					
					<tr>	
						<td colspan = "2" align="center">
						<input class="AddDept" type="submit" value="Add Department" name="SUBMIT" id="SUBMIT" /> 
						<input class="UpdateDept" type="submit" value="Update Department" name="SUBMIT" id="SUBMIT" /> 
						<input class="DeleteDept" type="submit" value="Delete Department" name="SUBMIT" id="SUBMIT" />	
						</td>
			
					</tr>	
	
			</table> 
			
			<!-- ***************************************************************************************** -->
			
				</div>
			</li>
			
			
			
			
			
			
			
			
			
			
			
			
			
		</ul>
	</div>
    
</div>
	
	
		
		
 </form>		
 
 
 
 </div> <!--<div id="view6">Maintenance -->
 
  
 
 
</div> <!-- end <div class="tabcontents"> -->
   


</body>
  <script language="JavaScript" type="text/javascript">
  

		Calendar.setup(
		{
			inputField : "requestDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbutton2",
			weekNumbers: false

		}  );
		
		
		
		Calendar.setup(
		{
			inputField : "start_input3",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbutton3",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "issueDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbutton3",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "projCompleteDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbutton4",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "closeDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbutton5",
			weekNumbers: false

		}  );
		
       function getData()
       {
        
        
         var param = document.getElementById('taskNum').value;
        // alert("Param= " + param);
		 
		 
		 
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

        // var url = "getCatInfo.php?param=" + param;
         // var url = "getCatInfo.php";
         //client.open("GET", url, true);

          client.onreadystatechange = function() {handler(client)};
          client.open("GET", "getTaskInfo.php?param=" + param);
          client.send("");

       }

            function handler(obj)
            {


               var assignedBy = document.getElementById('assignedBySelect');
               var jobCategory = document.getElementById('categorySelect');
			   var requestDate = document.getElementById('requestDate');
			   var issueDate = document.getElementById('issueDate');
               var projCompleteDate = document.getElementById('projCompleteDate');
               var engineerAssignOne = document.getElementById('engineerAssignOne');
			   var engineerAssignTwo = document.getElementById('engineerAssignTwo');
               var engineerAssignThree = document.getElementById('engineerAssignThree');
			   var engineerAssignFour = document.getElementById('engineerAssignFour');
			   var referTN = document.getElementById('referTN');
			   var taskType = document.getElementById('taskType');
               var supportDoc = document.getElementById('supportDoc');
			   var taskDesc = document.getElementById('taskDesc');



			  
			   
			   var system = document.getElementById('system');
               var subSys = document.getElementById('subSys');
			   var priority = document.getElementById('priority');
			   var status = document.getElementById('status');
               var closeDate = document.getElementById('closeDate');
			   var requestedBy = document.getElementById('requestedBy');
			
			   var equipmentTypeOne = document.getElementById('equipmentTypeOne');
			   var equipmentTypeTwo = document.getElementById('equipmentTypeTwo');
			   var equipmentTypeThree = document.getElementById('equipmentTypeThree');
			   var shopAffectedOne = document.getElementById('shopAffectedOne');
			   var shopAffectedTwo = document.getElementById('shopAffectedTwo');
			   
			   var aINumOne = document.getElementById('aINumOne');
			   var aINumTwo = document.getElementById('aINumTwo');
			   var aINumThree = document.getElementById('aINumThree');
			   var itemNameOne = document.getElementById('itemNameOne');
			   var itemNameTwo = document.getElementById('itemNameTwo');
			   var itemNameThree = document.getElementById('itemNameThree');
			   var taskNotes = document.getElementById('taskNotes');
			  
			   
			  
               if(obj.readyState == 4 && obj.status == 200)
               {

                 var val = eval('(' + obj.responseText + ')');
                 
                
                 //alert(val[0].name);
                 //alert(val[0].SUPPLIER);


                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                      

                       txtNew.text = val[i].ASSIGNED_BY;
                       assignedBy.value = txtNew.text;
					   
                      txtNew.text = val[i].JOB_CATEGORY;
                      jobCategory.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUEST_DATE;
                      requestDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ISSUE_DATE;
                      issueDate.value = txtNew.text;
					  
					  txtNew.text = val[i].PROJ_COMP_DATE;
                      projCompleteDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ENG_ASSIGN;
                      engineerAssignOne.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_TWO;
                      engineerAssignTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_THREE;
                      engineerAssignThree.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_FOUR;
                      engineerAssignFour.value = txtNew.text;
                      
					  txtNew.text = val[i].REFER_TN;
                      referTN.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_TYPE;
                      taskType.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_DOC;
                      supportDoc.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_DESCRIPTION;
                      taskDesc.value = txtNew.text;
					  
					  txtNew.text = val[i].SYS;
                      system.value = txtNew.text;
					  
					  txtNew.text = val[i].SUB_SYSTEM;
                      subSys.value = txtNew.text;
					  
					  txtNew.text = val[i].PRIORITY;
                      priority.value = txtNew.text;
					  
					  txtNew.text = val[i].STATUS;
                      status.value = txtNew.text;
					  
					  txtNew.text = val[i].CLOSE_DATE;
                      closeDate.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUESTED_BY;
                      requestedBy.value = txtNew.text;
					  
					 
					  
					  txtNew.text = val[i].EQUIP_TYPE;
                      equipmentTypeOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_TWO;
                      equipmentTypeTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_THREE;
                      equipmentTypeThree.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_ONE;
                      shopAffectedOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_TWO;
                      shopAffectedTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_ONE;
                      aINumOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_TWO;
                      aINumTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_THREE;
                      aINumThree.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME;
                      itemNameOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_TWO;
                      itemNameTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_THREE;
                      itemNameThree.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_PROGRESS_NOTES;
                      taskNotes.value = txtNew.text; 
					

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getData()
			 
			 

			 
		Calendar.setup(
		{
			inputField : "requestDate2",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonU2",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "start_input32",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonU3",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "issueDate2",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonU3",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "projCompleteDate2",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonU4",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "closeDate2",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonU5",
			weekNumbers: false

		}  );	
			 
			 
			 
			
			 
		function getData2()
       {
         var param = document.getElementById('taskNum2').value;
         //alert("Param= " + param);
		 
		 
		 
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

        // var url = "getCatInfo.php?param=" + param;
         // var url = "getCatInfo.php";
         //client.open("GET", url, true);

          client.onreadystatechange = function() {handler2(client)};
          client.open("GET", "getTaskInfo.php?param=" + param);
          client.send("");

       }

            function handler2(obj)
            {


               var assignedBy = document.getElementById('assignedBySelect2');
               var jobCategory = document.getElementById('categorySelect2');
			   var requestDate = document.getElementById('requestDate2');
			   var issueDate = document.getElementById('issueDate2');
               var projCompleteDate = document.getElementById('projCompleteDate2');
               var engineerAssignOne = document.getElementById('engineerAssignOne2');
			   var engineerAssignTwo = document.getElementById('engineerAssignTwo2');
               var engineerAssignThree = document.getElementById('engineerAssignThree2');
			   var engineerAssignFour = document.getElementById('engineerAssignFour2');
			   var referTN = document.getElementById('referTN2');
			   var taskType = document.getElementById('taskType2');
               //var supportDoc = document.getElementById('supportDoc2');

               var downloadFile = document.getElementById('downloadFile');
               
              // var length = downloadFile.options.length;
              // for (i = 0; i < length; i++) {
            	  //downloadFile.options[i] = null;
            	  
              // }
              // var options = ["1", "2", "3", "4", "5"];

              //for (var i = 0; i < options.length; i++) {
              //     var opt = options[i];
              //     var el = document.createElement("option");
              //     el.textContent = opt;
              //     el.value = opt;
              //     downloadFile.appendChild(el);
              //}
				
				
				
				
               
			   var taskDesc = document.getElementById('taskDesc2');
			   var system = document.getElementById('system2');
               var subSys = document.getElementById('subSys2');
			   var priority = document.getElementById('priority2');
			   var status = document.getElementById('status2');
               var closeDate = document.getElementById('closeDate2');
			   var requestedBy = document.getElementById('requestedBy2');

			   var equipmentTypeOne = document.getElementById('equipmentTypeOne2');
			   var equipmentTypeTwo = document.getElementById('equipmentTypeTwo2');
			   var equipmentTypeThree = document.getElementById('equipmentTypeThree2');
			   var shopAffectedOne = document.getElementById('shopAffectedOne2');
			   var shopAffectedTwo = document.getElementById('shopAffectedTwo2');
			   
			   var aINumOne = document.getElementById('aINumOne2');
			   var aINumTwo = document.getElementById('aINumTwo2');
			   var aINumThree = document.getElementById('aINumThree2');
			   var itemNameOne = document.getElementById('itemNameOne2');
			   var itemNameTwo = document.getElementById('itemNameTwo2');
			   var itemNameThree = document.getElementById('itemNameThree2');
			   var taskNotes = document.getElementById('pastTaskNotes');
			   var p1 = document.getElementById('p1');
			   
			  
			  
			   
			  
               if(obj.readyState == 4 && obj.status == 200)
               {

                 var val = eval('(' + obj.responseText + ')');
                 
                
                 //alert(val[0].name);
                 //alert(val[0].SUPPLIER);


                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                      

                       txtNew.text = val[i].ASSIGNED_BY;
                       assignedBy.value = txtNew.text;
					   
                      txtNew.text = val[i].JOB_CATEGORY;
                      jobCategory.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUEST_DATE;
                      requestDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ISSUE_DATE;
                      issueDate.value = txtNew.text;
					  
					  txtNew.text = val[i].PROJ_COMP_DATE;
                      projCompleteDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ENG_ASSIGN;
                      engineerAssignOne.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_TWO;
                      engineerAssignTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_THREE;
                      engineerAssignThree.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_FOUR;
                      engineerAssignFour.value = txtNew.text;
                      
					  txtNew.text = val[i].REFER_TN;
                      referTN.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_TYPE;
                      taskType.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_DOC;
                    //  supportDoc.value = txtNew.text;



					  var doctxt = txtNew.text;	
					  var docArray = new Array();
					  docArray = doctxt.split(",");
                 
					  for(var x=0; x<docArray.length; x++){
							
							var opt = docArray[x];
	                          var el = document.createElement("option");
	                          
	                          el.textContent = opt;
	                          el.value = opt;
	                          downloadFile.appendChild(el);


	                         	                          
						}
					  
					  txtNew.text = val[i].TASK_DESCRIPTION;
                      taskDesc.value = txtNew.text;
					  
					  txtNew.text = val[i].SYS;
                      system.value = txtNew.text;
					  
					  txtNew.text = val[i].SUB_SYSTEM;
                      subSys.value = txtNew.text;
					  
					  txtNew.text = val[i].PRIORITY;
                      priority.value = txtNew.text;
					  
					  txtNew.text = val[i].STATUS;
                      status.value = txtNew.text;
					  
					  txtNew.text = val[i].CLOSE_DATE;
                      closeDate.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUESTED_BY;
                      requestedBy.value = txtNew.text;
				  
				  
					  txtNew.text = val[i].EQUIP_TYPE;
                      equipmentTypeOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_TWO;
                      equipmentTypeTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_THREE;
                      equipmentTypeThree.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_ONE;
                      shopAffectedOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_TWO;
                      shopAffectedTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_ONE;
                      aINumOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_TWO;
                      aINumTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_THREE;
                      aINumThree.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME;
                      itemNameOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_TWO;
                      itemNameTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_THREE;
                      itemNameThree.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_PROGRESS_NOTES;
                     document.getElementById("p1").innerHTML = txtNew.text;
					  taskNotes.value = txtNew.text;
						
					  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)



              
			




               

             } //end function getData()
			 
			 
		//********************************************************************************************************
		//________________________________________________________________________________________________________
		//********************************************************************************************************
		//________________________________________________________________________________________________________
			 
		//********************************************************************************************************
			 

			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
			 
		function getClosedData()
		{
			var param = document.getElementById('closedJobsSelect').value;
            //alert("Param= " + param);
		 
		 
		 
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

        // var url = "getCatInfo.php?param=" + param;
         // var url = "getCatInfo.php";
         //client.open("GET", url, true);

          client.onreadystatechange = function() {closeHandler(client)};
          client.open("GET", "getClosedJobs.php?param=" + param);
          client.send("");

       }

            function closeHandler(obj)
            {


               var closeAssignedBy = document.getElementById('closeAssignedBy');
               var closeJobCategory = document.getElementById('closeCategory');
			   var closeRequestDate = document.getElementById('closeRequestDate');
			   var closeIssueDate = document.getElementById('closeIssueDate');
               var closeProjCompleteDate = document.getElementById('closeProjCompleteDate');
               var closeEngineerAssignOne = document.getElementById('closeEngineerAssignOne');
			   var closeEngineerAssignTwo = document.getElementById('closeEngineerAssignTwo');
               var closeEngineerAssignThree = document.getElementById('closeEngineerAssignThree');
			   var closeEngineerAssignFour = document.getElementById('closeEngineerAssignFour');
			   var closeReferTN = document.getElementById('closeReferTN');
			   var closeTaskType = document.getElementById('closeTaskType');
               var closeSupportDoc = document.getElementById('closeSupportDoc');
			   var closeTaskDesc = document.getElementById('closeTaskDesc');
			   var closeSystem = document.getElementById('closeSystem');
               var closeSubSys = document.getElementById('closeSubSys');
			   var closePriority = document.getElementById('closePriority');
			   var closeStatus = document.getElementById('closeStatus');
               var closeCloseDate = document.getElementById('closeCloseDate');
			   
			   var closeRequestedBy = document.getElementById('closeRequestedBy');

			   var closeEquipmentTypeOne = document.getElementById('closeEquipmentTypeOne');
			   var closeEquipmentTypeTwo = document.getElementById('closeEquipmentTypeTwo');
			   var closeEquipmentTypeThree = document.getElementById('closeEquipmentTypeThree');
			   var closeShopAffectedOne = document.getElementById('closeShopAffectedOne');
			   var closeShopAffectedTwo = document.getElementById('closeShopAffectedTwo');
			   
			   var closeAINumOne = document.getElementById('closeAINumOne');
			   var closeAINumTwo = document.getElementById('closeAINumTwo');
			   var closeAINumThree = document.getElementById('closeAINumThree');
			   var closeItemNameOne = document.getElementById('closeItemNameOne');
			   var closeItemNameTwo = document.getElementById('closeItemNameTwo');
			   var closeItemNameThree = document.getElementById('closeItemNameThree');
			   var closeTaskNotes = document.getElementById('closeTaskNotes');
			   var cp1 = document.getElementById('cp1');
			   
               if(obj.readyState == 4 && obj.status == 200)
               {

                 var val = eval('(' + obj.responseText + ')');
                 
                
                 //alert(val[0].TASK_PROGRESS_NOTES);
                 //alert(val[0].JOB_CATEGORY);


                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                      

                       txtNew.text = val[i].ASSIGNED_BY;
                       closeAssignedBy.value = txtNew.text;
					   
                      txtNew.text = val[i].JOB_CATEGORY;
                      closeJobCategory.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUEST_DATE;
                      closeRequestDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ISSUE_DATE;
                      closeIssueDate.value = txtNew.text;
					  
					  txtNew.text = val[i].PROJ_COMP_DATE;
                      closeProjCompleteDate.value = txtNew.text;
					  
					  txtNew.text = val[i].ENG_ASSIGN;
                      closeEngineerAssignOne.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_TWO;
                      closeEngineerAssignTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_THREE;
                      closeEngineerAssignThree.value = txtNew.text;
					  
					  txtNew.text = val[i].EA_FOUR;
                      closeEngineerAssignFour.value = txtNew.text;
                      
					  txtNew.text = val[i].REFER_TN;
                      closeReferTN.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_TYPE;
                      closeTaskType.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_DOC;
                      closeSupportDoc.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_DESCRIPTION;
                      closeTaskDesc.value = txtNew.text;
					  
					  txtNew.text = val[i].SYS;
                      closeSystem.value = txtNew.text;
					  
					  txtNew.text = val[i].SUB_SYSTEM;
                      closeSubSys.value = txtNew.text;
					  
					  txtNew.text = val[i].PRIORITY;
                      closePriority.value = txtNew.text;
					  
					  txtNew.text = val[i].STATUS;
                      closeStatus.value = txtNew.text;
					  
					  txtNew.text = val[i].CLOSE_DATE;
                      closeCloseDate.value = txtNew.text;
					  
					  txtNew.text = val[i].REQUESTED_BY;
                      closeRequestedBy.value = txtNew.text;
					  

					  
					  txtNew.text = val[i].EQUIP_TYPE;
                      closeEquipmentTypeOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_TWO;
                      closeEquipmentTypeTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ET_THREE;
                      closeEquipmentTypeThree.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_ONE;
                      closeShopAffectedOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AREA_SHOP_AFFECTED_TWO;
                      closeShopAffectedTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_ONE;
                      closeAINumOne.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_TWO;
                      closeAINumTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].AI_NUM_THREE;
                      closeAINumThree.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME;
                      closeItemNameOne.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_TWO;
                      closeItemNameTwo.value = txtNew.text;
					  
					  txtNew.text = val[i].ITEM_NAME_THREE;
                      closeItemNameThree.value = txtNew.text;
					  
					  txtNew.text = val[i].TASK_PROGRESS_NOTES;
					  document.getElementById("cp1").innerHTML = txtNew.text;
					  closeTaskNotes.value = txtNew.text;
						
					  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             

		}	//end getClosedData()
		
		
		
		//********************************************
		//*******************************************
					  
function getEmpData()
       {
         var param = document.getElementById('empMaintSelect').value;
         //alert("Param= " + param);
		 
		 
		 
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

        // var url = "getCatInfo.php?param=" + param;
         // var url = "getCatInfo.php";
         //client.open("GET", url, true);

          client.onreadystatechange = function() {handler3(client)};
          client.open("GET", "getEmployee.php?param=" + param);
          client.send("");

       }

            function handler3(obj)
            {

                var empFName = document.getElementById('empFName');
              	var empLName = document.getElementById('empLName');	   
				var empInitials = document.getElementById('empInitials');
				var empDept = document.getElementById('empDept');
				var empEmail = document.getElementById('empEmail');
			  
               if(obj.readyState == 4 && obj.status == 200)
               {

                 var val = eval('(' + obj.responseText + ')');
                 
                
                 //alert(val[0].FNAME);
                 //alert(val[0].LNAME);


                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].FNAME;
                       empFName.value = txtNew.text;
					   
					   txtNew.text = val[i].LNAME;
                       empLName.value = txtNew.text;
					   
					   txtNew.text = val[i].INITIALS;
                       empInitials.value = txtNew.text;
					   
					   txtNew.text = val[i].DEPT;
                       empDept.value = txtNew.text;
					   
					   txtNew.text = val[i].EMAIL;
                       empEmail.value = txtNew.text;
					   
					  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()		

function getCatData()
       {
         var param = document.getElementById('catMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler4(client)};
          client.open("GET", "getCategory.php?param=" + param);
          client.send("");

       }

            function handler4(obj)
            {

                var catDesc = document.getElementById('maintCatDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');
				 
                 

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].CATDESC;
                       catDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()		

function getTaskData()
       {
         var param = document.getElementById('taskMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler5(client)};
          client.open("GET", "getTask.php?param=" + param);
          client.send("");

       }

            function handler5(obj)
            {

                var taskDesc = document.getElementById('maintTaskDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].TASKDESC;
                       taskDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()	
			 
function getSysData()
       {
         var param = document.getElementById('sysMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler6(client)};
          client.open("GET", "getSystem.php?param=" + param);
          client.send("");

       }

            function handler6(obj)
            {

                var sysDesc = document.getElementById('maintSysDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].SYSDESC;
                       sysDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function 

function getSubSysData()
       {
         var param = document.getElementById('subSysMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler7(client)};
          client.open("GET", "getSubSystem.php?param=" + param);
          client.send("");

       }

            function handler7(obj)
            {

                var subSysDesc = document.getElementById('maintSubSysDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].SUBSYSDESC;
                       subSysDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()	
			 
function getEquipData()
       {
         var param = document.getElementById('eqMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler8(client)};
          client.open("GET", "getEquip.php?param=" + param);
          client.send("");

       }

            function handler8(obj)
            {

                var equipDesc = document.getElementById('maintEqDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].EQUIPDESC;
                       equipDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()	


function getShopData()
       {
         var param = document.getElementById('shopMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler9(client)};
          client.open("GET", "getShop.php?param=" + param);
          client.send("");

       }

            function handler9(obj)
            {

                var shopDesc = document.getElementById('maintShopDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].SHOPDESC;
                       shopDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()	


function getDeptData()
       {
         var param = document.getElementById('deptMaintSelect').value;
         
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

          client.onreadystatechange = function() {handler10(client)};
          client.open("GET", "getDept.php?param=" + param);
          client.send("");

       }

            function handler10(obj)
            {

                var deptDesc = document.getElementById('maintDeptDesc');
             			  
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			   //alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 for(var i = 0; i < val.length; i++)
                 {

                       var txtNew = document.createElement('text');

                       txtNew.text = val[i].DEPT;
                       deptDesc.value = txtNew.text;	  
                      

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)

             } //end function getEmpData()	







			 
					  
					  
					  
	var tabs = $('#tabs-titles li'); //grab tabs
var contents = $('#tabs-contents li'); //grab contents

tabs.bind('click',function(){
    contents.hide(); //hide all contents
  tabs.removeClass('current'); //remove 'current' classes

  $(contents[$(this).index()]).show(); //show tab content that matches tab title index
  $(this).addClass('current'); //add current class on clicked tab title
});
				  

		
		Calendar.setup(
		{
			inputField : "rptStartDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startCalbuttonRpt",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "rptEndDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "endCalbuttonRpt",
			weekNumbers: false

		}  );

		Calendar.setup(
		{
			inputField : "rptStartRequestDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "startRequestCalbuttonRpt",
			weekNumbers: false

		}  );
		
		Calendar.setup(
		{
			inputField : "rptEndRequestDate",
			ifFormat   : "%m/%d/%Y",
			displayArea: "start_display2",
			daFormat   : "%m/%d/%Y",
			button     : "endRequestCalbuttonRpt",
			weekNumbers: false

		}  );


			$(document).on("keydown", function (e) {
		    if (e.which === 8 && !$(e.target).is("input:not([readonly]):not([type=radio]):not([type=checkbox]), textarea, [contentEditable], [contentEditable=true]")) {
		        e.preventDefault();
		    }
		});

		
		
		
   </script>






