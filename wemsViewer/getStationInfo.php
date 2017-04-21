
 <?php
//whether if this is a direct call
/*
if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
	//now load classes easily without worrying about including their files
	spl_autoload_register(function ($class) {
		include_once "../classes/{$class}Class.php";
	});
	
	$CTID = isset($_GET['param']) ? $_GET['param'] : -1;
	$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
	
	$location = new location();
	
	$ggl = $location->getStationInfo($CTID, $eventID);
	
	echo json_encode( $ggl );
}
*/

  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $CTID = isset($_GET['param']) ? $_GET['param'] : -1;
    $eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    
    $comments = "";
    
    //$eventID = 0;
    $empAssigned= "";
    $button = "";
    $noteTime = "";
    $forman = "";
    $crewSize = "";
    $bags = "";
    $pass = "";
    $status = "";
    $formanName = "";
    $supportDocs = "";
    

   		/*	
	    $qry = oci_parse($c, "SELECT ct.NOTIFYTIME, ct.ASSIGNED_CREWSIZE, ct.ASSIGNED_SITEFOREMEN, ct.CT_STATUS, ct.CT_PASSNUM, ct.CT_BAGS, e.NAME
								from WEMS_CLEANABLE_TARGET ct, EMPLOYEE e where e.EMPLOYEEID = ct.ASSIGNED_SITEFOREMEN AND CTID = :CTID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_bind_by_name($qry, ":CTID", $CTID, -1);
                                   //oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);


      while($row = oci_fetch_array($qry))
       {

          $noteTime = $row['NOTIFYTIME'];
       	  $forman = $row['ASSIGNED_SITEFOREMEN'];
       	  $formanName = $row['NAME'];
       	  $crewSize = $row['ASSIGNED_CREWSIZE'];
          $bags = $row['CT_BAGS'];
       	  $pass = $row['CT_PASSNUM'];
       	  $status = $row['CT_STATUS'];
       	  $button = "Update Station";
       } 
      */
    
    
            
          
         $qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :CTID")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
         oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
         oci_bind_by_name($qryDoc, ":CTID", $CTID, -1);
          
         oci_execute($qryDoc);
         
          
         
         while($row = oci_fetch_array($qryDoc))
         {
             	
             $supportDocs = $supportDocs . $row['ID'] . ",";
             // $supportDoc = $row['ID'];
             	
         }
          
   
    
    
    
    $qry = oci_parse($c, "SELECT NOTIFYTIME, ASSIGNED_CREWSIZE, ASSIGNED_SITEFOREMEN, CT_STATUS, CT_PASSNUM, CT_BAGS, NAME
								from WEMS_CLEANABLE_TARGET  where CTID = :CTID")
    								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    								oci_bind_by_name($qry, ":CTID", $CTID, -1);
    								//oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
    
    								oci_execute($qry);
    
    
    								while($row = oci_fetch_array($qry))
    								{
    
    								    $noteTime = $row['NOTIFYTIME'];
    								    $forman = $row['ASSIGNED_SITEFOREMEN'];
    								   // $formanName = $row['NAME'];
    								    $crewSize = $row['ASSIGNED_CREWSIZE'];
    								    $bags = $row['CT_BAGS'];
    								    $pass = $row['CT_PASSNUM'];
    								    $status = $row['CT_STATUS'];
    								    $button = "Update Station";
    								}
    
    
    
    
    

      /* $qry = oci_parse($c, "SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, t.CTNOTES, e.NAME, t.CTSTATUS, t.CTPASSNUM,
      //                          t.CTBAGS, t.CTNOTEUSER
								from WEMS_CLEANABLE_TARGET_NOTES t, EMPLOYEE e where CTID = :CTID and EVENTID = :EVENTID and t.FORMANID = e.EMPLOYEEID ORDER BY ENTER_DATETIME")
       								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       */
    					$qry = oci_parse($c, "SELECT TO_CHAR(WEMS_CLEANABLE_TARGET_NOTES.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME,
                                            WEMS_CLEANABLE_TARGET_NOTES.CTNOTES, WEMS_EMPLOYEE.FST_NME, WEMS_EMPLOYEE.LST_NME, WEMS_CLEANABLE_TARGET_NOTES.CTSTATUS, 
                                            WEMS_CLEANABLE_TARGET_NOTES.CTPASSNUM, WEMS_CLEANABLE_TARGET_NOTES.CTBAGS, 
                                            WEMS_CLEANABLE_TARGET_NOTES.CTNOTEUSER
                                            FROM WEMS_CLEANABLE_TARGET_NOTES
                                            LEFT JOIN WEMS_EMPLOYEE ON WEMS_EMPLOYEE.EMPLOYEENUMBER = WEMS_CLEANABLE_TARGET_NOTES.FORMANID 
                                            where WEMS_CLEANABLE_TARGET_NOTES.CTID = :CTID and 
                                            WEMS_CLEANABLE_TARGET_NOTES.EVENTID = :EVENTID and  
                                            ((WEMS_CLEANABLE_TARGET_NOTES.FORMANID = WEMS_EMPLOYEE.EMPLOYEENUMBER) or (WEMS_CLEANABLE_TARGET_NOTES.FORMANID is NULL))ORDER BY ENTER_DATETIME")
    																OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       								oci_bind_by_name($qry, ":CTID", $CTID, -1);
       								oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       								oci_execute($qry);
       

       								while($row = oci_fetch_array($qry))
       								{

       								    $nNoteTime = $row['CTSTARTTIME'];
       								    $nUser = $row['CTNOTEUSER'];
       								    $nNote = $row['CTNOTES'];
       								    $nForman = $row['FST_NME'] . " " . $row['LST_NME'];
       								    $nBags = $row['CTBAGS'];
       								    $nPass = $row['CTPASSNUM'];
       								    $nStatus = $row['CTSTATUS'];
       								    
       								    $qry2 = oci_parse($c, "SELECT STATUS from WEMS_LOCATION_STATUS WHERE STATUSID = :STATUSID")
       								     OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       								    
       								      oci_bind_by_name($qry2, ":STATUSID", $nStatus, -1);
       								    
       								    								oci_execute($qry2);
       								    
       								    								while($row = oci_fetch_array($qry2))
       								    								{
       								    								    $nStatus = $row['STATUS'];
       								    								}
       								    
       								    
       								    
       								    
       								    
       				$comments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Bags: " . $nBags . ", Pass #: " . $nPass . ", Status: " .  $nStatus . "\\n";
       				                    

       								}

       								//$comments = str_replace('"', '\"', $comments);
       								//$comments = "event ID = " .  $eventID;


    $json = "[{
        \"GANG\": \"$forman\",
        \"FORMANNAME\": \"$nForman\",
        \"COMMENTS\": \"$comments\",
        \"STATUS\": \"$status\",
        \"PASS\": \"$pass\",
        \"BAGS\": \"$bags\",
        \"TIME\": \"$noteTime\",
        \"BUTTON\": \"$button\",
        \"SUPPORTDOCS\": \"$supportDocs\"
        }]";



  echo $json;


  oci_close($c);

?>

