 <?php 
  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $formanID = isset($_GET['param']) ? $_GET['param'] : -1;
    $eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    
    $comments = "";
    
   
    $empAssigned= "";
    $button = "";
    $loc = "";
    $status = 0;
    $dateTime = "";
     if($formanID >= 0)
     {

         

	    $qry = oci_parse($c, "SELECT EMP_ASSIGNED, ASSIGN_LOC, STATUS, OPENTIME
								from WEMS_GANG where FORMANID = :FORMANID and EVENTID = :EVENTID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_bind_by_name($qry, ":FORMANID", $formanID, -1);
                                   oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);

                    while($row = oci_fetch_array($qry))
                    {

                        $empAssigned = $row['EMP_ASSIGNED'];
                        $loc = $row['ASSIGN_LOC'];
                        $status = $row['STATUS'];
                        $dateTime = $row['OPENTIME'];

                    } 
                    
                    
       /*
         $qry = oci_parse($c, "SELECT FULLNAME
								from WEMS_CLEANABLE_TARGET where CTID = :CTID")
                    								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                    
                    			oci_bind_by_name($qry, ":CTID", $loc, -1);
                    			
                    
                    			oci_execute($qry);
                    
                    			while($row = oci_fetch_array($qry))
                    			{
                    
                    				
                    				$loc = $row['FULLNAME'];
                    
                    			}
        */            


       $qry = oci_parse($c, "SELECT TO_CHAR(NOTETIME, 'MM/DD/YYYY HH:MI PM') as NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ASSIGN_LOC
								from WEMS_GANG_NOTES where FORMANID = :FORMANID and EVENTID = :EVENTID order by ENTER_DATETIME")
       								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       								oci_bind_by_name($qry, ":FORMANID", $formanID, -1);
       								oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       								oci_execute($qry);


       								while($row = oci_fetch_array($qry))
       								{
       								    
   
       								    $noteTime = $row['NOTETIME'];
       								    $user = $row['NOTEUSER'];
       								    $note = $row['EVENTUPDATE'];
       								    $noteEmpAssigned = $row['EMP_ASSIGNED'];
       								    
       								    $loc = $row['ASSIGN_LOC'];
       								    
       								    if($loc != "")
       								    {
       								        
       								        $qry2 = oci_parse($c, "SELECT FULLNAME from WEMS_CLEANABLE_TARGET where MARKERID = :CTID")
       								        								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       								        
       								        								oci_bind_by_name($qry2, ":CTID", $loc, -1);

       								        								oci_execute($qry2);
       								        
       								        								while($row = oci_fetch_array($qry2))
       								        								{
       								        								    $loc = $row['FULLNAME'];
       								        
       								        								}
       								        								
       								        $comments .= $noteTime . ",  user: " . $user . ", Gang Assigned to: " . $loc ."\\n";
       								        $button = "Update Gang";
       								        								
       								    }
       								    else 
       								    {
       								        
       								        $comments .= $noteTime . ",  user: " . $user . ",  " . $note . ", Employees assigned: " . $noteEmpAssigned . "\\n";
       								        $button = "Update Gang";
       								        
       								    }
       								    
       								    
       								    
       							    
       				

       								}

}

if($button == "")$button = "Enter Gang"; 

    $json = "[{
        \"EMP_ASSIGNED\": \"$empAssigned\",
        \"COMMENTS\": \"$comments\",
        \"BUTTON\": \"$button\",
        \"STATUS\": \"$status\",
        \"DATETIME\": \"$dateTime\"
        }]";



  echo $json;


  oci_close($c);

?>
