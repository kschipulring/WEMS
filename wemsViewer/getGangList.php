<?php 
  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $loc = isset($_GET['loc']) ? $_GET['loc'] : -1;
    $eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    


	    $qry = oci_parse($c, "select g.FORMANID, e.NAME, g.ASSIGN_LOC from WEMS_GANG g, EMPLOYEE e where g.EVENTID = :EVENTID and g.FORMANID = e.EMPLOYEEID ")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                  
                                   oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);


     

          $json = "[";
         
         				$json .= "{\"FORMANID\": \"0\",\"NAME\": \"\"},";
         					while($row = oci_fetch_array($qry))
         					{
         					    $assign_loc = $row['ASSIGN_LOC'];
         					    if(($loc == $assign_loc) or ($assign_loc ==""))
         					    {
         					         $json .= "{\"FORMANID\": \"$row[FORMANID]\",\"NAME\": \"$row[NAME]\",\"LOCATION\": \"$row[ASSIGN_LOC]\"},";
         					    }
         
         					}
         								 
         $json .= "]";

     
       

      



   


 echo $json;


  oci_close($c);

?>
