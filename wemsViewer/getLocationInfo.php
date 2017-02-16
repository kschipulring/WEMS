 <?php 
  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $formanID = isset($_GET['param']) ? $_GET['param'] : -1;
    
    $comments = "";
    
    $eventID = 0;
    $empAssigned= "";
    $button = "";

     if($formanID >= 0)
     {

         
         $qry = oci_parse($c, "SELECT EVENTID
								from WEMS_EVENT where CLOSEUSER is null")
         								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
         
         								oci_execute($qry);
         
         
         								while($row = oci_fetch_array($qry))
         								{
         
         								    $eventID = $row['EVENTID'];
         
         								}
         								 
         
          
         
         
         
         

	    $qry = oci_parse($c, "SELECT EMP_ASSIGNED
								from WEMS_GANG where FORMANID = :FORMANID and EVENTID = :EVENTID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_bind_by_name($qry, ":FORMANID", $formanID, -1);
                                   oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);


      while($row = oci_fetch_array($qry))
       {

           $empAssigned = $row['EMP_ASSIGNED'];

       } 
       

       $qry = oci_parse($c, "SELECT TO_CHAR(NOTETIME, 'MM/DD/YYYY HH:MI PM') as NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED
								from WEMS_GANG_NOTES where FORMANID = :FORMANID and EVENTID = :EVENTID")
       								OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       								oci_bind_by_name($qry, ":FORMANID", $formanID, -1);
       								oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       								oci_execute($qry);
       

       								while($row = oci_fetch_array($qry))
       								{

       								    $noteTime = $row['NOTETIME'];
       								    $user = $row['NOTEUSER'];
       								    $note = $row['EVENTUPDATE'];
       								    $empAssigned = $row['EMP_ASSIGNED'];
       								    
       				$comments .= $noteTime . ",  user: " . $user . ",  " . $note . ", Employees assigned: " . $empAssigned . "\\n";
       				                    $button = "Update Gang"; 

       								}

}

if($button == "")$button = "Enter Gang"; 

    $json = "[{
        \"EMP_ASSIGNED\": \"$empAssigned\",
        \"COMMENTS\": \"$comments\",
        \"BUTTON\": \"$button\"
        }]";



  echo $json;


  oci_close($c);

?>
